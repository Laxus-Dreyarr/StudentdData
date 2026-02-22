from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import joblib
import numpy as np
import pandas as pd
import logging

app = FastAPI()

# Set up logging to see errors in the console
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Load models and scaler at startup
try:
    rf_model = joblib.load('random_forest.pkl')
    xgb_model = joblib.load('xgboost.pkl')
    scaler = joblib.load('scaler.pkl')
    logger.info("Models loaded successfully.")
except Exception as e:
    logger.error(f"Failed to load models: {e}")
    raise

class StudentFeatures(BaseModel):
    overall_gwa: float
    domain_gwa: float
    programming_gpa: float
    course_completion_ratio: float
    failed_subject_count: int
    gpa_trend_slope: float
    has_probation: bool

@app.post("/predict")
async def predict_risk(features: StudentFeatures):
    try:
        # Convert to dictionary and then to DataFrame (preserves column names)
        input_dict = features.dict()
        input_dict['has_probation'] = int(input_dict['has_probation'])
        input_df = pd.DataFrame([input_dict])  # shape (1,7) with column names

        # Scale using the pre‑fitted scaler
        input_scaled = scaler.transform(input_df)

        # Predict probabilities
        rf_proba = rf_model.predict_proba(input_scaled)
        xgb_proba = xgb_model.predict_proba(input_scaled)

        # Handle single‑class models gracefully
        if rf_proba.shape[1] == 1:
            # Only one class present in training: probability of the positive class is 0
            rf_prob = 0.0
        else:
            rf_prob = rf_proba[0][1]   # probability of class 1 (at‑risk)

        if xgb_proba.shape[1] == 1:
            xgb_prob = 0.0
        else:
            xgb_prob = xgb_proba[0][1]

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

@app.get("/health")
async def health():
    return {"status": "ok"}


# Command to run the API: uvicorn in cmd
# cd C:\xampp\htdocs\StudentData-V2
# venv\Scripts\activate
# cd C:\xampp\htdocs\StudentData-V2\app\python-api
# (venv) C:\xampp\htdocs\StudentData-V2\app\python-api>uvicorn main:app --reload --host 127.0.0.1 --port 8000