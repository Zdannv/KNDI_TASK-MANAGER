<script setup>
import { ref, onBeforeUnmount } from 'vue';
import axios from 'axios';

const video = ref(null)
const canvas = ref(null)
const isCameraOn = ref(false)
const result = ref(null)
const recognizing = ref(false)
let scanInterval = null

const toggleCamera = async () => {
  if (isCameraOn.value) {
    stopCamera();
  } else {
    await startCamera();
  }
};

const startCamera = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
    video.value.srcObject = stream;
    isCameraOn.value = true;
    scanInterval = setInterval(autoRecognize, 2000);  
  } catch(err) {
    alert("Gagal mengakses kamera")
  }
};

const stopCamera = async () => {
  const stream = video.value.srcObject;
  if (stream) {
    stream.getTracks().forEach(track => track.stop());
  }

  video.value.srcObject = null;
  isCameraOn.value = false;
  result.value = null;

  if (scanInterval) {
    clearInterval(scanInterval)
  }
};

const autoRecognize = async () => {
  if (!isCameraOn.value || recognizing.value) return;

  const ctx = canvas.value.getContext('2d');
  canvas.value.width = video.value.videoWidth;
  canvas.value.height = video.value.videoHeight;
  ctx.drawImage(video.value, 0, 0);

  canvas.value.toBlob(async (blob) => {
    recognizing.value = true;
    const formData = new FormData();
    formData.append('file', blob, 'scan.jpg');

    try {
      const response = await axios.post('http://localhost:8000/recognize', formData);
      result.value = response.data;

    } catch (err) {
      console.error("AI Service Error", err);
    } finally {
      recognizing.value = false;
    }
  }, 'image/jpeg', 0.7);
};

onBeforeUnmount(() => stopCamera());
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-8 flex flex-col items-center">
    <div class="max-w-2xl w-full bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="bg-indigo-600 p-4">
        <h1 class="text-white text-xl font-bold text-center">Sistem Absensi</h1>
      </div>

      <div class="p-6 flex flex-col items-center">
        <div class="relative w-full aspect-video bg-black rounded-lg overflow-hidden border-4 border-gray-200">
          <video 
            ref="video"
            autoplay
            playsinline
            class="w-full h-full object-cover"
            :class="{ 'opacity-50': !isCameraOn }"
          ></video>

          <div v-if="!isCameraOn" class="absolute top-4 right-4 flex items-center bg-indigo-500 text-white px-3 py-1 rounded-full text-xs animate-pulse">
            <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
            Memindai...
          </div>
        </div>

        <div class="mt-6 flex space-x-4">
          <button
            @click="toggleCamera"
            :class="[
              'px-6 py-2 rounded-full font-semibold transition-all duration-200',
              isCameraOn
                ? 'bg-red-100 text-red-600 hover:bg-red-200'
                : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-md'
            ]"
          >
            {{ isCameraOn ? 'Matikan Kamera' : 'Nyalakan Kamera' }}
          </button>
        </div>

        <transition enter-active-class="transition duration-300 ease-out" enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100">
          <div v-if="isCameraOn && result" class="mt-8 w-full">
            <div 
              :class="[
                'p-4 rounded-lg border-2 flex items-center justify-between',
                result.name !== 'unknown' 
                  ? 'bg-green-50 border-green-200 text-green-800' 
                  : 'bg-gray-50 border-gray-200 text-gray-600'
              ]"
            >
              <div>
                <p class="text-sm uppercase tracking-wider font-semibold opacity-70">Status Identitas</p>
                <h2 class="text-2xl font-bold">{{ result.name }}</h2>
              </div>
              <div class="text-right">
                <p class="text-sm opacity-70">Confidence</p>
                <p class="text-xl font-mono">{{ (result.score * 100).toFixed(1) }}%</p>
              </div>
            </div>
          </div>
        </transition>

        <canvas ref="canvas" v-show="false"></canvas>
      </div>
    </div>
  </div>
</template>