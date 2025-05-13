from flask import Flask, request, jsonify
from transformers import TrOCRProcessor, VisionEncoderDecoderModel
from PIL import Image
import io
import os
import difflib
import Levenshtein
import jiwer
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Path to store the model locally
MODEL_DIR = './trocr-large-handwritten'

# Check if the model is already downloaded
if not os.path.exists(MODEL_DIR):
    print("Downloading model...")
    processor = TrOCRProcessor.from_pretrained('microsoft/trocr-large-handwritten')
    model = VisionEncoderDecoderModel.from_pretrained('microsoft/trocr-large-handwritten')
    
    # Save the model locally
    processor.save_pretrained(MODEL_DIR)
    model.save_pretrained(MODEL_DIR)
    print("Model downloaded and saved locally.")
else:
    print("Loading model from local files...")
    processor = TrOCRProcessor.from_pretrained(MODEL_DIR, local_files_only=True)
    model = VisionEncoderDecoderModel.from_pretrained(MODEL_DIR, local_files_only=True)
    print("Model loaded from local files.")

model.eval()

@app.route('/predict', methods=['POST'])
def predict():
    print("Request received:", request.files, request.form)

    # Ensure both files are present
    # if 'answer_script' not in request.files or 'ground_truth' not in request.files:
    #     print("Missing file or ground truth")
    #     return jsonify({"error": "File or ground truth missing"}), 400
    
    file = request.files['file']
    print("file",file)
    ground_truth_file = request.form['groundtruth']
    print("groundtruth",ground_truth_file)
    if file == '' or ground_truth_file == '':
        return jsonify({"error": "No file selected"}), 400
    
    # Read the image and convert to RGB
    image = Image.open(io.BytesIO(file.read())).convert("RGB")

    # Read the ground truth text
    ground_truth = ground_truth_file

    # Preprocess image
    pixel_values = processor(images=image, return_tensors="pt").pixel_values

    # Generate output
    generated_ids = model.generate(pixel_values)
    predicted_text = processor.batch_decode(generated_ids, skip_special_tokens=True)[0]

    # Calculate similarity score
    similarity_score = difflib.SequenceMatcher(None, ground_truth, predicted_text).ratio()

    # Calculate WER and CER using jiwer
    wer = jiwer.wer(ground_truth, predicted_text)
    cer = jiwer.cer(ground_truth, predicted_text)

    # Calculate Levenshtein distance
    levenshtein_distance = Levenshtein.distance(ground_truth, predicted_text)

    # Return results
    return jsonify({
        "predicted_text": predicted_text,
        "similarity_score": similarity_score,
        "wer": wer,
        "cer": cer,
        "levenshtein_distance": levenshtein_distance
    })

if __name__ == '__main__':
    app.run(debug=True, port=5001)
