<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

const video = ref(null);
const canvas = ref(null);
const isCameraOn = ref(false);
const attendanceResult = ref(null);
const recognizing = ref(false);
const currentTime = ref('');
let scanInterval = null;
let clockInterval = null;

const scanningResults = ref({
  name: null,
  score: 0
});

const updateClock = () => {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString([], { 
    hour: '2-digit', 
    minute: '2-digit', 
    second: '2-digit',
    hour12: false
  });
};

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
    attendanceResult.value = null;
    scanInterval = setInterval(autoRecognize, 1000);  // 1 second
  } catch(err) {
    alert("Gagal mengakses kamera");
  }
};

const stopCamera = async () => {
  const stream = video.value?.srcObject;
  if (stream) {
    stream.getTracks().forEach(track => track.stop());
  }

  if (video.value) {
    video.value.srcObject = null;
  }
  
  isCameraOn.value = false;
  result.value = null;
  
  if (scanInterval) {
    clearInterval(scanInterval);
  }
};

const saveAttendance = async (identifyResult) => {
  const formData = new FormData();
  formData.append('name', identifyResult.name);
  formData.append('score', identifyResult.score);

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

  try {
    const response = await axios.post('/attendance/store', formData, {
      headers: {
        'X-CSRF-TOKEN': csrfToken
      }
    });
    
    attendanceResult.value = {
      message: response.data.message,
      success: response.data.success
    };
  } catch (err) {
    console.error("Gagal Melakukan Absensi", err);
    alert(err.response?.data?.message || "Gagal melakukan absensi");
  }
};

const autoRecognize = async () => {
  if (!isCameraOn.value || recognizing.value) return;

  const context = canvas.value.getContext('2d');
  canvas.value.width = video.value.videoWidth;
  canvas.value.height = video.value.videoHeight;
  context.drawImage(video.value, 0, 0);

  canvas.value.toBlob(async (blob) => {
    recognizing.value = true;
    const formData = new FormData();
    formData.append('file', blob, 'scan.jpg');

    try {
      // Hit ke FastAPI
      const recognizeResponse = await axios.post('http://localhost:8000/recognize', formData);
      const response = recognizeResponse.data;

      scanningResults.value = {
        name: response.name,
        score: response.score
      };

      if (response.name !== 'unknown' && response.score > 0.45) {
        console.log("MENYIMPAN ABSENSI");
        await saveAttendance(response);
      }
    } catch (err) {
      console.error("AI Service Error", err);
    } finally {
      recognizing.value = false;
    }
  }, 'image/jpeg', 0.7);
};

onMounted(() => {
  updateClock();
  clockInterval = setInterval(updateClock, 1000);
});

onBeforeUnmount(() => {
  stopCamera();
  if (clockInterval) clearInterval(clockInterval);
});
</script>

<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 p-6 font-sans text-white">
    <div class="mb-6">
      <button
        @click="$inertia.visit(route('welcome'))"
        class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg border border-gray-600 transition flex items-center"
      >
        <span>Back</span>
      </button>
    </div>

    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <div class="lg:col-span-2 space-y-4">
        <div class="relative bg-black rounded-3xl overflow-hidden border-4 border-gray-700 shadow-2xl aspect-video">
          <video 
            ref="video"
            autoplay
            playsinline
            class="w-full h-full object-cover"
            :class="{ 'opacity-40': !isCameraOn }"
          ></video>
          
          <div v-if="!isCameraOn" class="absolute inset-0 flex items-center justify-center">
             <p class="text-gray-500">Kamera Nonaktif</p>
          </div>
        </div>
        
        <button
          @click="toggleCamera"
          :class="[
            'w-full py-4 rounded-xl font-bold text-lg transition-all',
            isCameraOn ? 'bg-red-500 hover:bg-red-600' : 'bg-indigo-600 hover:bg-indigo-700'
          ]"
        >
          {{ isCameraOn ? 'Matikan Kamera' : 'Mulai Absensi Wajah' }}
        </button>
      </div>

      <div class="flex flex-col space-y-6">
        <div class="bg-gray-800 border border-gray-700 p-8 rounded-3xl text-center shadow-xl">
          <p class="text-gray-400 uppercase tracking-widest text-sm mb-2">Waktu Saat Ini</p>
          <h2 class="text-5xl font-mono font-bold text-indigo-400">{{ currentTime }}</h2>
        </div>

        <div class="flex-grow bg-gray-800 border border-gray-700 p-8 rounded-3xl shadow-xl">
          <p class="text-gray-400 uppercase tracking-widest text-sm mb-4">Status Absensi</p>
          
          <div v-if="isCameraOn" class="mb-6 p-4 bg-gray-900/50 rounded-2xl border border-gray-700">
            <p class="text-xs text-indigo-400 font-bold mb-2">LIVE SCANNER:</p>
            <div class="flex justify-between items-center">
              <span class="text-sm">Terdeteksi: 
                <b :class="scanningResults.name === 'unknown' ? 'text-red-400' : 'text-green-400'">
                  {{ scanningResults.name || 'Mencari...' }}
                </b>
              </span>
              <span v-if="scanningResults.score > 0" class="text-xs bg-gray-700 px-2 py-1 rounded text-gray-300">
                Sim: {{ (scanningResults.score * 100).toFixed(1) }}%
              </span>
            </div>
          </div>

          <div v-if="attendanceResult" class="space-y-4">
            <div class="p-6 rounded-2xl border-2 bg-green-900/30 border-green-500 text-green-400">
              <h3 class="text-2xl font-bold mb-1">BERHASIL</h3>
              <p class="text-sm text-white opacity-90">{{ attendanceResult.message }}</p>
            </div>
          </div>

          <div v-else-if="!attendanceResult && isCameraOn" class="h-40 flex flex-col items-center justify-center text-gray-500 border-2 border-dashed border-gray-700 rounded-2xl text-center">
            <p class="text-sm animate-pulse">Memindai wajah, mohon tunggu...</p>
          </div>
        </div>
      </div>
    </div>
    <canvas ref="canvas" v-show="false"></canvas>
  </div>
</template>