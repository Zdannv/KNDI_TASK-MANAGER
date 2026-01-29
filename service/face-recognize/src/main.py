import cv2
import sys
import os

sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from model import initialize_model, process_frame, load_face

def main():
    print("Sedang memuat model AI...")
    model_ai = initialize_model()
    
    print("Sedang mendaftarkan wajah dari folder data/faces...")
    load_face(model_ai)
    
    cap = cv2.VideoCapture(0)
    
    if not cap.isOpened():
        print("Error: Kamera tidak dapat diakses!")
        return
    
    print("Sistem Berjalan. Tekan 'q' pada jendela gambar untuk keluar.")

    while True:
        ret, frame = cap.read()
        if not ret:
            print("Gagal mengambil gambar dari kamera.")
            break

        processed_frame = process_frame(frame, model_ai)

        cv2.imshow("Face Recognition Testing - Local Mode", processed_frame)

        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()
    print("Sistem dihentikan.")

if __name__ == "__main__":
    main()