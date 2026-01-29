import cv2
import os
import insightface
import numpy as np
from numpy.linalg import norm

known_face = {}

def initialize_model():
    model = insightface.app.FaceAnalysis(name="buffalo_l", providers=['CPUExecutionProvider'])
    model.prepare(ctx_id=-1)
    return model

def recognize_face(embedding, threshold=0.4):
    if not known_face:
        return "unknown", 0.0
    
    best_match = "unknown"
    highest_similarity = -1.0

    for name, known_embedding in known_face.items():
        similarity = np.dot(embedding, known_embedding)

        if similarity > highest_similarity:
            highest_similarity = similarity
            best_match = name

    if highest_similarity > threshold:
        return best_match, float(highest_similarity)
    
    return "unknown", float(highest_similarity)

def load_face(model, folder_path=None):
    if folder_path is None:
        current_dir = os.path.dirname(os.path.abspath(__file__))
        folder_path = os.path.abspath(os.path.join(current_dir, "..", "data", "faces"))
    
    if not os.path.exists(folder_path):
        print(f"Error: Folder {folder_path} tidak ditemukan!")
        return

    for filename in os.listdir(folder_path):
        if filename.lower().endswith((".jpg", ".jpeg", ".png")):
            img_path = os.path.join(folder_path, filename)
            img = cv2.imread(img_path)

            if img is not None:
                faces = model.get(img)
                if len(faces) > 0:
                    name = os.path.splitext(filename)[0]
                    known_face[name] = faces[0].normed_embedding
                    print(f"Registrasi berhasil: {name}")

def process_frame(frame, model):
    rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    faces = model.get(rgb_frame)

    for face in faces:
        x1, y1, x2, y2 = map(int, face.bbox)
        name, score = recognize_face(face.normed_embedding)
        color = (0, 255, 0) if name != "unknown" else (0, 0, 255)

        cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)
        label = f"{name} ({score:.2f})"
        cv2.putText(frame, label, (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.6, color, 2)
        
    return frame