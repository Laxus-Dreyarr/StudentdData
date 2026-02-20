from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import joblib
import numpy as np
import pandas as pd

app = FastAPI()

# Load models and scaler at startup
rf_model = joblib.load('random_forest.pkl')
xgb_model = joblib.load('xgboost.pkl')
scaler = joblib.load('scaler.pkl')

class StudentFeatures(BaseModel):
    overall_gwa: float
    domain_gwa: float
    programming_gpa: float
    course_completion_ratio: float
    failed_subject_count: int
    gpa_trend_slope: float
    has_probation: bool   # will be converted to 0/1

@app.post("/predict")
async def predict_risk(features: StudentFeatures):
    try:
        # Convert to DataFrame for scaling
        input_dict = features.dict()
        # Convert bool to int
        input_dict['has_probation'] = int(input_dict['has_probation'])
        
        # Ensure order matches training
        feature_order = ['overall_gwa', 'domain_gwa', 'programming_gpa',
                         'course_completion_ratio', 'failed_subject_count',
                         'gpa_trend_slope', 'has_probation']
        input_array = np.array([[input_dict[col] for col in feature_order]])
        
        # Scale
        input_scaled = scaler.transform(input_array)
        
        # Predict from both models
        rf_prob = rf_model.predict_proba(input_scaled)[0][1]  # probability of class 1
        xgb_prob = xgb_model.predict_proba(input_scaled)[0][1]
        
        # You can combine them (e.g., average) or return separately
        combined_prob = (rf_prob + xgb_prob) / 2.0
        
        return {
            "rf_risk_probability": float(rf_prob),
            "xgb_risk_probability": float(xgb_prob),
            "combined_risk_probability": float(combined_prob),
            "risk_level": "high" if combined_prob > 0.5 else "low"
        }
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@app.get("/health")
async def health():
    return {"status": "ok"}