import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from xgboost import XGBClassifier
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
import joblib

# Load the exported CSV
# df = pd.read_csv('storage/app/public/train/student_features.csv')  # adjust path
df = pd.read_csv('../../storage/app/public/train/student_features.csv')

# Features and target
feature_cols = [
    'overall_gwa',
    'domain_gwa',
    'programming_gpa',
    'course_completion_ratio',
    'failed_subject_count',
    'gpa_trend_slope',
    'has_probation'
]
X = df[feature_cols]
y = df['at_risk']

# Handle missing values (e.g., students with no grades yet)
X = X.fillna(X.mean())

# Train/test split
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Scale features
scaler = StandardScaler()
X_train_scaled = scaler.fit_transform(X_train)
X_test_scaled = scaler.transform(X_test)

# Train Random Forest
rf = RandomForestClassifier(n_estimators=100, random_state=42)
rf.fit(X_train_scaled, y_train)

# Train XGBoost
xgb = XGBClassifier(n_estimators=100, learning_rate=0.1, random_state=42)
xgb.fit(X_train_scaled, y_train)

# Save models and scaler
joblib.dump(rf, 'random_forest.pkl')
joblib.dump(xgb, 'xgboost.pkl')
joblib.dump(scaler, 'scaler.pkl')

print("Models trained and saved.")