from flask import Flask, request, jsonify
import pytesseract
from PIL import Image
import os

app = Flask(__name__)

@app.route('/ocr', methods=['POST'])
def ocr():
    if 'images' not in request.files:
        return jsonify({'error': 'No images provided'}), 400

    files = request.files.getlist('images')
    results = []

    for file in files:
        if file.filename == '':
            continue

        try:
            image = Image.open(file)
            text = pytesseract.image_to_string(image)
            results.append({
                'filename': file.filename,
                'text': text.strip()
            })
        except Exception as e:
            results.append({
                'filename': file.filename,
                'error': str(e)
            })

    return jsonify({'results': results})

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'working'})

if __name__ == '__main__':
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port)
