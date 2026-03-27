<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        
        /* Animasi Card Muncul */
        .error-card {
            background: #fff;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        /* Animasi Teks 404 Mengambang */
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 0.5rem;
            animation: floatText 3s ease-in-out infinite;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 1rem;
        }

        .error-desc {
            color: #6c757d;
            margin-bottom: 2rem;
        }

        /* Styling & Animasi Karakter Hantu */
        .ghost-container {
            margin-bottom: 1.5rem;
            animation: floatGhost 4s ease-in-out infinite;
        }
        .ghost-eye {
            transform-origin: center;
            animation: blink 4s infinite;
        }

        /* --- Keyframes (Kumpulan Animasi) --- */
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatGhost {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
        }

        @keyframes floatText {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes blink {
            0%, 96%, 100% { transform: scaleY(1); }
            98% { transform: scaleY(0.1); }
        }
    </style>
</head>
<body>

    <div class="error-card">
        <div class="ghost-container">
            <svg width="120" height="120" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <path d="M 40 180 L 40 80 C 40 20 160 20 160 80 L 160 180 L 140 160 L 120 180 L 100 160 L 80 180 L 60 160 Z" fill="#e9ecef" stroke="#ced4da" stroke-width="3"/>
                <circle cx="80" cy="80" r="10" fill="#343a40" class="ghost-eye" style="transform-origin: 80px 80px;"/>
                <circle cx="120" cy="80" r="10" fill="#343a40" class="ghost-eye" style="transform-origin: 120px 80px;"/>
                <ellipse cx="100" cy="110" rx="12" ry="18" fill="#343a40" />
                <path d="M 90 125 Q 100 135 110 125" stroke="#e9ecef" stroke-width="4" fill="none" />
            </svg>
        </div>

        <div class="error-code">404</div>
        <div class="error-title">Oops! Halaman Tidak Ditemukan</div>
        <div class="error-desc">
            Maaf halaman yang Anda ketik tidak tersedia di sistem. Silakan kembali ke halaman utama.
        </div>

    </div>

</body>
</html>