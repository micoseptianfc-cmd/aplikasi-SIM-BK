<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM-BK</title>
    <!-- Memanggil Bootstrap untuk desain -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background-color: #2c3e50; /* Warna latar belakang gelap agar elegan */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card { 
            width: 100%; 
            max-width: 400px; 
            padding: 30px; 
            background-color: white; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <i class="fas fa-school fa-3x text-primary mb-3"></i>
            <h4><b>SIM-BK</b></h4>
            <p class="text-muted">Sistem Informasi Bimbingan Konseling</p>
        </div>
        
        <!-- Form ini akan mengirim data ke file proses_login.php -->
        <form action="proses/proses_login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username..." required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password..." required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i> Masuk
            </button>
        </form>
    </div>

</body>
</html>