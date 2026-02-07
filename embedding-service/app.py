from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer
import logging
import os

app = Flask(__name__)
logging.basicConfig(level=logging.INFO)

MODEL_NAME = 'paraphrase-multilingual-mpnet-base-v2'

try:
    model = SentenceTransformer(MODEL_NAME)
    logging.info(f"Model {MODEL_NAME} loaded successfully")
except Exception as e:
    logging.error(f"Failed to load model: {str(e)}")

@app.route('/embed', methods=['POST'])
def embed():
    try:
        data = request.json
        if isinstance(data, list):
            texts = [item.get('text', '') for item in data]
        elif isinstance(data, dict):
            texts = data.get('texts', [data.get('text')]) if any(k in data for k in ['text', 'texts']) else None
        
        if not texts or not any(texts):
            return jsonify({'error': 'No valid texts provided'}), 400
        
        # Очистка пустых строк
        texts = [str(t) for t in texts if t]
        
        embeddings = model.encode(texts, convert_to_numpy=True)
        
        if len(texts) == 1:
            return jsonify({'embedding': embeddings[0].tolist()})
        else:
            return jsonify({'embeddings': embeddings.tolist()})
            
    except Exception as e:
        logging.error(f"Error during embedding: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status': 'ok', 
        'model': MODEL_NAME,
        'dimension': 768 # У mpnet размерность 384
    })