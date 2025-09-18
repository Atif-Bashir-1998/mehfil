import os
import pandas as pd
import numpy as np
from sklearn.linear_model import LogisticRegression
from sklearn.svm import SVC
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score, classification_report
import joblib

# Load the embeddings and labels
X = np.load('./src/data/tfidf_embeddings/tfidf_vectors.npy')
y = np.load('./src/data/tfidf_embeddings/labels.npy')

print(f"Data loaded: {X.shape[0]} samples, {X.shape[1]} features")

# Split data into train and test sets
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42, stratify=y
)

print(f"Train set: {X_train.shape[0]} samples")
print(f"Test set: {X_test.shape[0]} samples")

# Initialize models
models = {
    'Logistic Regression': LogisticRegression(random_state=42, max_iter=1000),
    # 'SVM': SVC(random_state=42), # not using SVM because it is too slow
    'Random Forest': RandomForestClassifier(random_state=42, n_estimators=100)
}

# Train and evaluate each model
results = {}

for model_name, model in models.items():
    print(f"\nTraining {model_name}...")

    # Train the model
    model.fit(X_train, y_train)

    # Make predictions
    y_pred = model.predict(X_test)

    # Calculate metrics
    accuracy = accuracy_score(y_test, y_pred)
    precision = precision_score(y_test, y_pred)
    recall = recall_score(y_test, y_pred)
    f1 = f1_score(y_test, y_pred)

    # Store results
    results[model_name] = {
        'accuracy': accuracy,
        'precision': precision,
        'recall': recall,
        'f1': f1,
        'classification_report': classification_report(y_test, y_pred)
    }

    # Save the model
    model_path = f"./src/models/{model_name.replace(' ', '_').lower()}.pkl"
    joblib.dump(model, model_path)
    print(f"Saved {model_name} to {model_path}")

# Save performance summary
results_file = "./src/models/model_performance.txt"

with open(results_file, 'w') as f:
    f.write("Model Performance Summary\n")
    f.write("=" * 50 + "\n\n")

    for model_name, metrics in results.items():
        f.write(f"{model_name}\n")
        f.write("-" * 30 + "\n")
        f.write(f"Accuracy:  {metrics['accuracy']:.4f}\n")
        f.write(f"Precision: {metrics['precision']:.4f}\n")
        f.write(f"Recall:    {metrics['recall']:.4f}\n")
        f.write(f"F1 Score:  {metrics['f1']:.4f}\n\n")
        f.write("Classification Report:\n")
        f.write(metrics['classification_report'] + "\n")
        f.write("=" * 50 + "\n\n")

print(f"\nPerformance summary saved to: {results_file}")
print("\nTraining completed successfully!")
