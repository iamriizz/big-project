<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fav Dimsum - Sertifikat Halal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <main class="min-h-screen flex flex-col items-center justify-center p-6">
        <h1 class="text-3xl font-bold mb-6 text-orange-600">Fav Dimsum</h1>

        <!-- Trigger Modal Sertifikat Halal -->
        <div onclick="openHalalModal()" class="cursor-pointer group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:-translate-y-1">
            <img src="{{ asset('images/halal.jpeg') }}" 
                 alt="Sertifikat Halal Fav Dimsum" 
                 class="w-64 h-auto object-cover"
                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x600?text=Gambar+Tidak+Ditemukan';">
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white font-medium">
                Klik untuk memperbesar
            </div>
        </div>
    </main>

    <!-- Modal Pop-up Sertifikat Halal -->
    <div id="halalModal" 
         class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm items-center justify-center p-4" 
         onclick="closeHalalModal()">
        
        <!-- Wadah Konten Modal -->
        <div class="relative bg-white rounded-3xl p-4 max-w-lg w-full shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
            
            <!-- Tombol Close (✕) -->
            <button type="button" 
                    onclick="closeHalalModal()" 
                    class="absolute top-3 right-3 bg-orange-600 hover:bg-orange-700 text-white rounded-full w-9 h-9 flex items-center justify-center font-bold text-lg shadow-md transition transform hover:scale-105 z-20">
                ✕
            </button>

            <!-- Gambar Sertifikat Besar -->
            <div class="w-full flex justify-center items-center">
                <img src="{{ asset('images/halal.jpeg') }}" 
                     alt="Sertifikat Halal Fav Dimsum" 
                     class="w-full h-auto max-h-[80vh] object-contain rounded-2xl"
                     onerror="alert('File gambar tidak ditemukan di public/images/halal.jpeg');">
            </div>
        </div>
    </div>

    <!-- Script Penutup/Pembuka Modal -->
    <script>
        function openHalalModal() {
            const modal = document.getElementById('halalModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex'); // Tambahkan flex saat modal dibuka
                document.body.style.overflow = 'hidden';
            }
        }

        function closeHalalModal() {
            const modal = document.getElementById('halalModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex'); // Hapus flex saat modal ditutup
                document.body.style.overflow = 'auto';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeHalalModal();
            }
        });
    </script>
</body>
</html>