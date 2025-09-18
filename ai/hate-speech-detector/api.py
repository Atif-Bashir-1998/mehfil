from flask import Flask, request, jsonify
import numpy as np
import joblib
import os
from src.utils import preprocess_and_tokenize

app = Flask(__name__)

# Load models
try:
    # Use absolute paths for better container compatibility
    base_dir = os.path.dirname(os.path.abspath(__file__))
    vectorizer_path = os.path.join(base_dir, "src/data/tfidf_embeddings/tfidf_vectorizer.pkl")
    model_path = os.path.join(base_dir, "src/models/random_forest.pkl")

    vectorizer = joblib.load(vectorizer_path)
    model = joblib.load(model_path)
    print("Models loaded successfully!")
    print(f"Model type: {type(model).__name__}")
except Exception as e:
    print(f"Error loading models: {e}")
    import traceback
    traceback.print_exc()
    vectorizer = None
    model = None

def predict_hate_speech(text):
    """
    Predict if a text contains hate speech

    Args:
        text (str): Input text to classify

    Returns:
        tuple: (is_hate_speech, confidence)
    """
    if vectorizer is None or model is None:
        return False, 0.0

    # Preprocess the text
    tokens = preprocess_and_tokenize(text)
    processed_text = ' '.join(tokens)

    # Convert to TF-IDF vector and convert to DENSE array
    text_vector = vectorizer.transform([processed_text])
    text_vector_dense = text_vector.toarray()  # Convert sparse to dense

    # Make prediction
    prediction = model.predict(text_vector_dense)[0]

    # Calculate confidence
    if hasattr(model, 'predict_proba'):
        # For models with probability estimation (like Random Forest)
        probas = model.predict_proba(text_vector_dense)[0]
        confidence = probas[1] if prediction == 1 else probas[0]
    else:
        # For models without probability (like SVM)
        decision_values = model.decision_function(text_vector_dense)
        confidence = 1 / (1 + np.exp(-np.abs(decision_values[0])))  # Sigmoid of absolute decision value

    return bool(prediction), float(confidence)

@app.route('/predict', methods=['POST'])
def predict():
    """
    API endpoint to predict hate speech
    Expects JSON: {'text': 'your text here'}
    Returns JSON: {'text': original_text, 'isHateSpeech': boolean, 'confidence': percentage}
    """
    try:
        # Check if request has JSON data
        if not request.is_json:
            return jsonify({'error': 'Request must be JSON'}), 400

        # Get JSON data from request
        data = request.get_json()

        # Alternative way to get data if above fails
        if data is None:
            try:
                data = request.json
            except:
                return jsonify({'error': 'Invalid JSON format'}), 400

        if not data or 'text' not in data:
            return jsonify({'error': 'Missing text field'}), 400

        text = data['text']

        if not isinstance(text, str) or not text.strip():
            return jsonify({'error': 'Text must be a non-empty string'}), 400

        # Make prediction
        is_hate_speech, confidence = predict_hate_speech(text)

        # Prepare response
        response = {
            'text': text,
            'isHateSpeech': is_hate_speech,
            'confidence': f'{confidence:.2%}'
        }

        return jsonify(response)

    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'models_loaded': model is not None and vectorizer is not None
    })

@app.route('/', methods=['GET'])
def home():
    """Home endpoint with API information"""
    return jsonify({
        'message': 'Hate Speech Detection API',
        'endpoints': {
            'POST /predict': 'Analyze text for hate speech. Send JSON: {"text": "your text here"}',
            'GET /health': 'Check API health status'
        },
        'model_loaded': model is not None
    })

# Add a test endpoint to verify basic functionality
@app.route('/test', methods=['GET'])
def test():
    """Test endpoint to verify basic functionality"""
    return jsonify({'message': 'API is working!'})

if __name__ == '__main__':
    # Use environment variable for port or default to 5000
    port = int(os.environ.get('PORT', 5000))
    app.run(debug=True, host='0.0.0.0', port=port)
