import os
import numpy as np
import joblib
import sys
from src.utils import preprocess_and_tokenize

# Load the TF-IDF vectorizer
vectorizer_path = "./src/data/tfidf_embeddings/tfidf_vectorizer.pkl"
with open(vectorizer_path, 'rb') as f:
    vectorizer = joblib.load(f)

# Load the SVM model
model_path = "./src/models/random_forest.pkl"
model = joblib.load(model_path)

def predict_hate_speech(text):
    """
    Predict if a text contains hate speech

    Args:
        text (str): Input text to classify

    Returns:
        tuple: (prediction, confidence)
    """
    # Preprocess the text
    tokens = preprocess_and_tokenize(text)
    processed_text = ' '.join(tokens)

    # Convert to TF-IDF vector and convert to DENSE array
    text_vector = vectorizer.transform([processed_text])
    text_vector_dense = text_vector.toarray()  # Convert sparse to dense

    # Make prediction
    prediction = model.predict(text_vector_dense)[0]

    # For SVM without probability, use decision function for confidence
    if hasattr(model, 'predict_proba'):
        confidence = np.max(model.predict_proba(text_vector_dense)[0])
    else:
        # Use decision function values as proxy for confidence
        decision_values = model.decision_function(text_vector_dense)
        confidence = 1 / (1 + np.exp(-np.abs(decision_values[0])))  # Sigmoid of absolute decision value

    return prediction, confidence

def main():
    # Get text from command line argument or user input
    if len(sys.argv) > 1:
        text = ' '.join(sys.argv[1:])
    else:
        text = input("Enter the text to analyze: ")

    # Make prediction
    prediction, confidence = predict_hate_speech(text)

    # Display results
    print(f"\nText: '{text}'")
    print(f"Prediction: {'HATE SPEECH' if prediction == 1 else 'NOT HATE SPEECH'}")
    print(f"Confidence: {confidence:.2%}")

    return prediction

if __name__ == "__main__":
    main()
