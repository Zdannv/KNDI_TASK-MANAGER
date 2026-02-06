import cv2
import numpy as np
from insightface.app import FaceAnalysis

known_faces = {}

def initialize_model():
    model = FaceAnalysis(name='buffalo_l', providers=['CPUExecutionProvider'])
    model.prepare(ctx_id=-1)

    return model

model_app = initialize_model()

def recognize_face(embedding, threshold=0.5):
    if not known_faces:
        return "Unknown", 0.0
    
    best_match = "Unknown"
    highest_similarity = -1

    for name, known_embedding in known_faces.items():
        similarity_score = np.dot(embedding, known_embedding)

        if similarity_score > highest_similarity:
            highest_similarity = similarity_score
            best_match = name

    if highest_similarity > threshold:
        return best_match, float(highest_similarity)
    
    return "Unknown", float(highest_similarity)

def get_face_embedding(image_bytes):
    nparr = np.frombuffer(image_bytes, np.uint8)
    image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

    if image is None:
        return None
    
    faces = model_app.get(image)
    if not faces:
        return None
    
    return faces[0].normed_embedding.tolist()