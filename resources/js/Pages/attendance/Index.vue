<script setup>
import { ref, onMounted, onBeforeMount, h } from 'vue'

const video = ref(null)
const canvas = ref(null)
const photo = ref(null)
const statusMessage = ref("Menunggu kamera...")
const captureCount = ref(0)
const lastCaptureTime = ref("-")
const isScanning = ref(false)

let stream = null
let scanInterval = null

const startCamera = async () => {
  try {
    stream = await navigator.mediaDevices.getUserMedia({
      video: {
        width: { ideal: 640 },
        height: { ideal: 480 },
        facingMode: 'user'
      }
    })

    if (video.value) {
      video.value.srcObject = stream
      video.value.onloadedmetadata = () => {
        statusMessage = "Kamera aktif"
        startAutoCapture()
      }
    }
  } catch(err) {
    console.log("Error: ", err)
    statusMessage.value = "Gagal mengakses kamera"
  }
}

const startAutoCapture = () => {
  if (isScanning.value) return

  isScanning.value = true
  statusMessage.value = "Auto-Capture Berjalan"
  scanInterval = setInterval(() => {
    capturePhoto()
  }, 2000);
}

const stopAutoCapture = () => {
  if (scanInterval) {
    clearInterval(scanInterval)
    scanInterval = null
  }
  isScanning.value = false
  statusMessage.value = "Scanning Berhenti (Paused)"
}

const toggleScanning = () => {
  if (isScanning.value) {
    stopAutoCapture()
  } else {
    startAutoCapture()
  }
}

const capturePhoto = () => {
  if (!video.value || !canvas.value) {
    return
  }

  const width = video.value.videoWidth
  const height = video.value.videoHeight

  canvas.value.width = width
  canvas.value.height = height

  const context = canvas.value.getContext('2d')
  context.drawImage(video.value, 0, 0, width, height)

  const imageData = canvas.value.toDataURL('image/jpeg', 0.9)
  photo.value = imageData
  captureCount.value++

  const now = new Date()
  lastCaptureTime.value = now.toLocaleDateString()
}

onMounted(() => {
  startCamera()
})

onBeforeMount(() => {
  stopAutoCapture()

  if (stream) {
    stream.getTracks().forEach(t => t.stop())
  }
})
</script>

<template>
  <div class="camera-container">
    <h1>Test Camera</h1>

    <div class="video-wrapper">
      <video ref="video" autoplay playsinline class="video-feed"></video>
      <div class="scan-overlay"></div>
    </div>

    <div class="status-box">
      <p>Status : <strong>{{ statusMessage }}</strong></p>
      <p>Jumlah Foto terambil : <strong>{{ captureCount }}</strong></p>

      <button @click="toggleScanning" class="btn-toggle">
        {{ isScanning ? 'Stop Scanning' : 'Start Scanning' }}
      </button>
    </div>

    <canvas ref="canvas" style="display: none;"></canvas>

    <div v-if="photo" class="result-box">
      <h2>Hasil snapshot</h2>
      <img :src="photo" alt="photo-preview" />
      <p class="timestamp">Waktu: {{ lastCaptureTime }}</p>
    </div>
  </div>
</template>

<style scoped>
.camera-container {
  max-width: 600px;
  margin: 0 auto;
  text-align: center;
  font-family: Arial, sans-serif;
}

.video-wrapper {
  position: relative;
  width: 100%;
  border-radius: 8px;
  overflow: hidden;
  background: #000;
  margin-bottom: 15px;
}

.video-feed {
  width: 100%;
  display: block;
  transform: scaleX(-1); /* Mirror view untuk user experience */
}

/* UI Overlay Box */
.scan-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 200px;
  height: 200px;
  border: 2px dashed rgba(0, 255, 0, 0.7);
  border-radius: 10px;
}

.status-box {
  background: #f4f4f4;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.btn-toggle {
  margin-top: 10px;
  padding: 8px 16px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-toggle:hover {
  background-color: #0056b3;
}

.result-box {
  border: 1px solid #ddd;
  padding: 10px;
  border-radius: 8px;
  background: #fff;
}

.photo-preview {
  width: 100%;
  max-width: 300px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.timestamp {
  font-size: 0.9em;
  color: #666;
  margin-top: 5px;
}
</style>