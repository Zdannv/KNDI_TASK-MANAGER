import cv2
import numpy as np
import model
from fastapi import FastAPI, UploadFile, File
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

model_ai = model.initialize_model()
model.load_face(model_ai)

@app.post("/recognize")
async def recognize(file: UploadFile = File(...)):
    content = await file.read()
    nparr = np.frombuffer(content, np.uint8)
    img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

    if img is None:
        return {"name": "error", "score": 0.0, "message": "Gambar tidak valid"}

    faces = model_ai.get(img)
    if not faces:
        return {"name": "unknown", "score": 0.0}
    
    name, score = model.recognize_face(faces[0].normed_embedding)
    return {"name": name, "score": float(score)}