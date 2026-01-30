<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link } from '@inertiajs/vue3'; // Import Link untuk tombol back
import axios from 'axios';

// --- State Management ---
const videoRef = ref(null);
const canvasRef = ref(null);
const isCameraActive = ref(false);
const attendanceType = ref('check_in');
const recognitionResult = ref(null); // Menyimpan status terakhir
const isProcessing = ref(false);
const currentTime = ref('');
const currentDate = ref('');
let timeInterval;
let scanInterval;

// --- Jam & Tanggal ---
const updateTime = () => {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
    });
    currentDate.value = now.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });
};

// --- Kontrol Kamera ---
const toggleCamera = async () => {
    if (isCameraActive.value) stopCamera();
    else await startCamera();
};

const startCamera = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        if (videoRef.value) {
            videoRef.value.srcObject = stream;
            isCameraActive.value = true;
        }
    } catch (error) {
        alert("Gagal akses kamera.");
    }
};

const stopCamera = () => {
    if (videoRef.value && videoRef.value.srcObject) {
        videoRef.value.srcObject.getTracks().forEach(t => t.stop());
        videoRef.value.srcObject = null;
    }
    isCameraActive.value = false;
    // recognitionResult.value = null; // KITA HAPUS INI agar status tetap tampil meski kamera mati
};

// --- LOGIKA UTAMA (OPTIMIZED) ---
const captureAndRecognize = async () => {
    // Tetap scan meskipun sudah ada result (agar bisa update status ke orang baru)
    // Cuma stop kalau kamera mati atau sedang processing request sebelumnya
    if (!isCameraActive.value || isProcessing.value || !videoRef.value) return;

    isProcessing.value = true;

    try {
        const context = canvasRef.value.getContext('2d');
        
        // --- OPTIMASI SPEED: RESIZE GAMBAR ---
        // Kita kecilkan gambar sebelum kirim ke Python agar proses deteksi LEBIH CEPAT
        const scaleWidth = 500; // Lebar 500px cukup untuk wajah
        const scaleHeight = (videoRef.value.videoHeight / videoRef.value.videoWidth) * scaleWidth;
        
        canvasRef.value.width = scaleWidth;
        canvasRef.value.height = scaleHeight;
        
        // Gambar ulang video ke canvas ukuran kecil
        context.drawImage(videoRef.value, 0, 0, scaleWidth, scaleHeight);

        canvasRef.value.toBlob(async (blob) => {
            if (!blob) { isProcessing.value = false; return; }

            const formData = new FormData();
            formData.append('file', blob, 'scan.jpg'); 

            try {
                // 1. Tembak Python (Port 8000)
                // Timeout dipercepat (3 detik) agar tidak menunggu lama kalau python macet
                const pyResponse = await axios.post('http://localhost:8000/recognize', formData, { timeout: 3000 });
                
                const detectedName = pyResponse.data.name; 
                const score = pyResponse.data.score || 0;

                // 2. Cek Hasil AI
                // Kita hanya update status JIKA menemukan wajah yang JELAS (score > 0.50)
                // Ini mencegah status berkedip-kedip kalau wajah samar
                if (detectedName && detectedName !== 'unknown' && score > 0.50) {
                    
                    // Cek apakah status yang tampil sekarang SUDAH SAMA dengan yang baru dideteksi?
                    // Jika sama (orangnya masih diam di depan kamera), jangan kirim request ke Laravel lagi (Biar hemat resource)
                    if (recognitionResult.value && 
                        recognitionResult.value.name === detectedName && 
                        recognitionResult.value.status === 'success' &&
                        recognitionResult.value.type === attendanceType.value) {
                        // Skip request, biarkan tampilan tetap sama
                        return; 
                    }

                    // 3. Kirim ke Laravel
                    const laravelResponse = await axios.post('/api/attendance/store', {
                        name: detectedName,
                        type: attendanceType.value 
                    });

                    const res = laravelResponse.data;

                    // UPDATE STATUS PERMANEN (Akan tampil terus sampai diganti)
                    recognitionResult.value = {
                        status: res.status === 'success' ? 'success' : 'error',
                        name: res.name,
                        message: res.message,
                        type: attendanceType.value // simpan tipe biar tau konteks
                    };

                    // KITA HAPUS TIMEOUT PENGHAPUSAN DI SINI
                    // Status akan stay forever.

                } 
                // Jika "Unknown", kita biarkan status terakhir tetap tampil di layar
                // Atau bisa tambahkan logika lain jika mau menampilkan "Wajah Tidak Dikenal"

            } catch (error) {
                // Silent error connection
            } finally {
                isProcessing.value = false;
            }
        }, 'image/jpeg', 0.8); // Kualitas JPG 80%

    } catch (err) {
        console.error(err);
        isProcessing.value = false;
    }
};

onMounted(() => {
    updateTime();
    timeInterval = setInterval(updateTime, 1000);
    // Scan lebih sering (tiap 0.8 detik) karena gambar sudah dikompres jadi ringan
    scanInterval = setInterval(() => {
        if (isCameraActive.value) captureAndRecognize();
    }, 800); 
});

onBeforeUnmount(() => {
    stopCamera();
    clearInterval(timeInterval);
    clearInterval(scanInterval);
});
</script>

<template>
    <Head title="Absensi Wajah" />

    <div class="min-h-screen bg-gray-100 font-sans text-gray-900 flex flex-col relative">
        
        <Link href="/" class="absolute top-4 left-4 z-50 bg-white/80 backdrop-blur border border-gray-200 text-gray-700 px-4 py-2 rounded-xl shadow-sm hover:bg-white hover:text-indigo-600 hover:shadow-md transition-all flex items-center gap-2 font-bold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </Link>

        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-end">
                <div class="text-right">
                    <div class="text-2xl font-mono font-bold text-gray-800 leading-none">{{ currentTime }}</div>
                    <div class="text-xs text-gray-500 font-medium uppercase mt-1">{{ currentDate }}</div>
                </div>
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center p-6">
            <div class="w-full max-w-6xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-[600px]">
                    
                    <div class="flex flex-col gap-4 h-full">
                        <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-200 flex">
                            <button @click="attendanceType = 'check_in'"
                                class="flex-1 py-3 rounded-xl text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2"
                                :class="attendanceType === 'check_in' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'">
                                CHECK IN
                            </button>
                            <button @click="attendanceType = 'check_out'"
                                class="flex-1 py-3 rounded-xl text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2"
                                :class="attendanceType === 'check_out' ? 'bg-rose-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'">
                                CHECK OUT
                            </button>
                        </div>

                        <div class="flex-grow bg-slate-900 rounded-3xl shadow-lg border border-gray-200 overflow-hidden relative group flex flex-col items-center justify-center">
                            <video v-show="isCameraActive" ref="videoRef" autoplay playsinline muted 
                                class="absolute inset-0 w-full h-full object-cover transform scale-x-[-1]"></video>

                            <div v-if="isCameraActive && !recognitionResult" class="absolute inset-0 z-10 pointer-events-none flex items-center justify-center">
                                <div class="w-64 h-64 border-2 border-dashed border-white/70 rounded-full animate-spin-slow opacity-60"></div>
                                <div class="absolute w-56 h-56 border-2 border-white/40 rounded-3xl"></div>
                            </div>

                            <div v-if="!isCameraActive" class="z-20 text-center">
                                <button @click="toggleCamera" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:scale-105">
                                    Nyalakan Kamera
                                </button>
                            </div>

                            <div v-if="isCameraActive" class="absolute top-4 right-4 z-20">
                                <button @click="toggleCamera" class="bg-red-500/80 hover:bg-red-600 backdrop-blur text-white px-4 py-2 rounded-full text-xs font-bold shadow-sm">
                                    Matikan
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 flex flex-col items-center justify-center p-8 text-center relative overflow-hidden">
                        <div class="absolute top-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                        
                        <transition mode="out-in" enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-4">
                            
                            <div v-if="!recognitionResult" key="empty" class="flex flex-col items-center gap-6 opacity-60">
                                <div class="w-32 h-32 bg-gray-50 rounded-full flex items-center justify-center border-4 border-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800">Siap Memindai</h3>
                                    <p class="text-gray-500">Silakan menghadap kamera.</p>
                                </div>
                            </div>

                            <div v-else key="result" class="flex flex-col items-center gap-6 w-full">
                                <div class="w-36 h-36 rounded-full flex items-center justify-center shadow-lg transform transition-all"
                                    :class="recognitionResult.status === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                    
                                    <svg v-if="recognitionResult.status === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>

                                <div class="w-full">
                                    <h2 class="text-5xl font-black text-gray-900 mb-3 tracking-tight">{{ recognitionResult.name }}</h2>
                                    
                                    <div class="inline-flex items-center px-6 py-3 rounded-2xl text-lg font-bold border shadow-sm"
                                        :class="recognitionResult.status === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'">
                                        {{ recognitionResult.message }}
                                    </div>
                                    
                                    <p class="text-xs text-gray-400 mt-4 uppercase tracking-widest font-semibold">
                                        Status Terakhir • {{ recognitionResult.type === 'check_in' ? 'Masuk' : 'Pulang' }}
                                    </p>
                                </div>
                            </div>

                        </transition>
                    </div>
                </div>
            </div>
        </main>
        
        <canvas ref="canvasRef" class="hidden"></canvas>
    </div>
</template>

<style scoped>
.animate-spin-slow { animation: spin 8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>