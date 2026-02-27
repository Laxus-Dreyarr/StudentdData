from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import joblib
import numpy as np
import pandas as pd
import logging
from sklearn.ensemble import RandomForestClassifier
from xgboost import XGBClassifier
from sklearn.preprocessing import StandardScaler
import threading

app = FastAPI()
logger = logging.getLogger(__name__)
logging.basicConfig(level=logging.INFO)

# Global variables for models and scaler (will be updated after retraining)
rf_model = None
xgb_model = None
scaler = None

# Lock to prevent concurrent retraining
retrain_lock = threading.Lock()

# ------------------------------------------------------------------
# Define the schema for a single student's features (matches Laravel output)
class StudentFeatures(BaseModel):
    overall_gwa: float
    domain_gwa: float
    programming_gpa: float
    course_completion_ratio: float
    failed_subject_count: int
    gpa_trend_slope: float
    has_probation: bool

# Schema for the retraining request (list of students with features + at_risk)
class TrainingData(BaseModel):
    students: list  # each element should be a dict with the above features + "at_risk"
# ------------------------------------------------------------------

def load_models_from_disk():
    """Load models from .pkl files (used at startup)."""
    global rf_model, xgb_model, scaler
    try:
        rf_model = joblib.load('random_forest.pkl')
        xgb_model = joblib.load('xgboost.pkl')
        scaler = joblib.load('scaler.pkl')
        logger.info("Models loaded from disk.")
    except FileNotFoundError:
        logger.warning("No existing model files found. Will need initial training.")
        rf_model = xgb_model = scaler = None

# Try to load existing models at startup
load_models_from_disk()

# ------------------------------------------------------------------
@app.post("/predict")
async def predict_risk(features: StudentFeatures):
    global rf_model, xgb_model, scaler
    if rf_model is None or xgb_model is None or scaler is None:
        raise HTTPException(status_code=503, detail="Models not yet trained. Please trigger retraining first.")

    try:
        input_dict = features.dict()
        input_dict['has_probation'] = int(input_dict['has_probation'])
        input_df = pd.DataFrame([input_dict])

        input_scaled = scaler.transform(input_df)

        rf_proba = rf_model.predict_proba(input_scaled)
        xgb_proba = xgb_model.predict_proba(input_scaled)

        rf_prob = rf_proba[0][1] if rf_proba.shape[1] > 1 else 0.0
        xgb_prob = xgb_proba[0][1] if xgb_proba.shape[1] > 1 else 0.0
        combined_prob = (rf_prob + xgb_prob) / 2.0

        return {
            "rf_risk_probability": float(rf_prob),
            "xgb_risk_probability": float(xgb_prob),
            "combined_risk_probability": float(combined_prob),
            "risk_level": "high" if combined_prob > 0.5 else "low"
        }
    except Exception as e:
        logger.exception("Prediction error")
        raise HTTPException(status_code=500, detail=str(e))

# ------------------------------------------------------------------
from fastapi import BackgroundTasks

def process_training(students_data):
    global rf_model, xgb_model, scaler
    try:
        logger.info("Received retraining request with %d students", len(students_data))

        # Convert JSON to DataFrame
        df = pd.DataFrame(students_data)
        # The DataFrame must contain feature columns + 'at_risk'
        feature_cols = [
            'overall_gwa', 'domain_gwa', 'programming_gpa',
            'course_completion_ratio', 'failed_subject_count',
            'gpa_trend_slope', 'has_probation'
        ]
        X = df[feature_cols].copy()
        y = df['at_risk'].copy()

        # Convert boolean to int (if necessary)
        X['has_probation'] = X['has_probation'].astype(int)

        # Handle missing values (fill with mean)
        X = X.fillna(X.mean())

        # Scale features
        new_scaler = StandardScaler()
        X_scaled = new_scaler.fit_transform(X)

        # Train new models
        new_rf = RandomForestClassifier(n_estimators=100, random_state=42)
        new_rf.fit(X_scaled, y)

        new_xgb = XGBClassifier(n_estimators=100, learning_rate=0.1, random_state=42)
        new_xgb.fit(X_scaled, y)

        # Save to disk
        joblib.dump(new_rf, 'random_forest.pkl')
        joblib.dump(new_xgb, 'xgboost.pkl')
        joblib.dump(new_scaler, 'scaler.pkl')

        # Update global variables (atomic assignment)
        rf_model = new_rf
        xgb_model = new_xgb
        scaler = new_scaler

        logger.info("Retraining completed successfully.")
    except Exception as e:
        logger.exception("Retraining failed")
    finally:
        retrain_lock.release()

@app.post("/retrain")
async def retrain(data: TrainingData, background_tasks: BackgroundTasks):
    """Receive JSON training data and train models in the background."""
    # Use a lock to prevent concurrent retraining
    if not retrain_lock.acquire(blocking=False):
        raise HTTPException(status_code=429, detail="Retraining already in progress")

    # Pass the training task to the background
    background_tasks.add_task(process_training, data.students)
    return {"message": "Models are being retrained in the background."}

# ------------------------------------------------------------------
@app.get("/health")
async def health():
    return {"status": "ok", "models_loaded": rf_model is not None}

# Command to run the API: uvicorn in cmd
# cd C:\xampp\htdocs\StudentData-V2
# venv\Scripts\activate
# cd C:\xampp\htdocs\StudentData-V2\app\python-api
# (venv) C:\xampp\htdocs\StudentData-V2\app\python-api>uvicorn main:app --reload --host 127.0.0.1 --port 8000

#during queue work, make sure to have the API running in another terminal, and then run the following command to start processing jobs:
#php artisan queue:work