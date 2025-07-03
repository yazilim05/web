<?php
session_start();
include("db.php");

$error = ""; 
$success = ""; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;
    $role = $_POST['role'] ?? null;

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "Tüm alanları doldurmanız gerekmektedir!";
    } elseif ($password !== $confirm_password) {
        $error = "Şifreler eşleşmiyor!";
    } else {
        try {
            $sql = "SELECT * FROM users WHERE username = :username OR email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "Bu kullanıcı adı veya e-posta zaten kayıtlı!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $insert_sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
                $insert_stmt = $pdo->prepare($insert_sql);
                $insert_stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $insert_stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $insert_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                $insert_stmt->bindParam(':role', $role, PDO::PARAM_STR);

                if ($insert_stmt->execute()) {
                    $success = "Başarılı! Kayıt tamamlandı. <a href='login.php' class='alert-link'>Giriş yapmak için tıklayın.</a>";
                } else {
                    $error = "Başarısız! Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.";
                }
            }
        } catch (PDOException $e) {
            $error = "Bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #52e5e7 0%, #130cb7 47%, #fff720 100%);
            background-size: 300% 300%;
            animation: bgmove 12s ease-in-out infinite;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        @keyframes bgmove {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
        .wave-bg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100vw;
            height: 180px;
            z-index: 0;
            pointer-events: none;
        }
        .register-card {
            z-index: 2;
            background: rgba(255,255,255,0.96);
            border-radius: 24px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.18), 0 1.5px 4px #130cb722;
            padding: 36px 32px 22px 32px;
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            animation: fadeIn 1.5s;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98) translateY(-20px);}
            to { opacity: 1; transform: scale(1) translateY(0);}
        }
        .bus-svg {
            width: 92px;
            height: 92px;
            margin-top: -60px;
            margin-bottom: 2px;
            display: block;
            filter: drop-shadow(0 8px 18px #00bcd444);
            z-index: 1;
            background: none;
        }
        .register-title {
            text-align: center;
            font-weight: bold;
            font-size: 2.1rem;
            margin-bottom: 18px;
            margin-top: 2px;
            background: linear-gradient(90deg,#130cb7,#52e5e7,#fff720);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-register {
            background: linear-gradient(90deg,#fff720 0%, #52e5e7 100%);
            color: #130cb7;
            border: none;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.09rem;
            padding: 0.6rem 1.6rem;
            box-shadow: 0 2px 12px #00bcd444;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
        }
        .btn-register:hover {
            background: linear-gradient(90deg,#52e5e7 0%,#fff720 100%);
            color: #fff;
            box-shadow: 0 4px 24px #130cb755;
        }
        .form-label { color: #130cb7; font-weight: 600;}
        .form-select, .form-control { border-radius: 15px;}
        .alert-danger { background: #fff9c4; color: #ad1457; border: none;}
        .alert-success { background: #d0ffd6; color: #10451d; border: none;}
        .login-link-area {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            width: 100%;
        }
        .login-link-text {
            font-size: 1.05rem;
            color: #130cb7;
            margin-bottom: 0.25rem;
        }
        .btn-login-link {
            background: linear-gradient(90deg,#52e5e7 0%, #fff720 100%);
            color: #130cb7;
            border: none;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.09rem;
            padding: 0.6rem 1.6rem;
            box-shadow: 0 2px 12px #00bcd444;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-login-link:hover {
            background: linear-gradient(90deg,#fff720 0%, #52e5e7 100%);
            color: #fff;
            box-shadow: 0 4px 24px #130cb755;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .register-card { padding: 18px 4vw 8px 4vw; }
            .bus-svg { margin-top: -38px; width: 70px; height: 70px;}
            .wave-bg { height: 110px; }
        }
    </style>
</head>
<body>
    <!-- Wave SVG Arka Plan -->
    <svg class="wave-bg" viewBox="0 0 1440 180" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0 90L80 83.3C160 77 320 63 480 74.7C640 87 800 125 960 126.7C1120 129 1280 95 1360 78.3L1440 63V180H1360C1280 180 1120 180 960 180C800 180 640 180 480 180C320 180 160 180 80 180H0V90Z"
            fill="#fff720" fill-opacity="0.45"/>
        <path d="M0 110L80 120C160 130 320 150 480 150C640 150 800 130 960 120C1120 110 1280 120 1360 125L1440 130V180H1360C1280 180 1120 180 960 180C800 180 640 180 480 180C320 180 160 180 80 180H0V110Z"
            fill="#52e5e7" fill-opacity="0.38"/>
        <path d="M0 140L80 146.7C160 154 320 168 480 168C640 168 800 154 960 146.7C1120 139 1280 137 1360 137.3L1440 138V180H1360C1280 180 1120 180 960 180C800 180 640 180 480 180C320 180 160 180 80 180H0V140Z"
            fill="#130cb7" fill-opacity="0.22"/>
    </svg>
    <div class="register-card">
        <!-- Otobüs SVG'si -->
        <svg class="bus-svg" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Otobüs">
            <rect x="7" y="15" width="56" height="32" rx="8" fill="#52e5e7"/>
            <rect x="10" y="18" width="50" height="20" rx="5" fill="#fff"/>
            <rect x="15" y="40" width="40" height="7" rx="3" fill="#fff720"/>
            <circle cx="17.5" cy="51" r="5" fill="#626262" stroke="#333" stroke-width="1.5"/>
            <circle cx="52.5" cy="51" r="5" fill="#626262" stroke="#333" stroke-width="1.5"/>
            <rect x="23" y="22" width="10" height="10" rx="2" fill="#b6b6b6"/>
            <rect x="37" y="22" width="16" height="10" rx="2" fill="#b6b6b6"/>
            <rect x="13" y="26.5" width="44" height="1.5" rx="0.75" fill="#52e5e7"/>
            <rect x="14" y="33" width="10" height="3" rx="1.5" fill="#fff720"/>
            <rect x="46" y="33" width="10" height="3" rx="1.5" fill="#fff720"/>
            <ellipse cx="35" cy="61" rx="8" ry="2.5" fill="#130cb7" opacity="0.21"/>
        </svg>
        <h2 class="register-title">Kayıt Ol</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success text-center" role="alert">
                <?= $success ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST" class="w-100">
            <div class="mb-3">
                <label for="username" class="form-label">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı adınızı giriniz" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="E-posta adresinizi giriniz" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Şifrenizi giriniz" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Şifre (Tekrar)</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Şifrenizi tekrar giriniz" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rol Seçiniz</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="" disabled selected>Rol seçiniz</option>
                    <option value="admin">Admin</option>
                    <option value="user">Kullanıcı</option>
                </select>
            </div>
            <button type="submit" class="btn btn-register w-100">Kayıt Ol</button>
        </form>
        <div class="login-link-area">
            <div class="login-link-text">Hesabınız var mı?</div>
            <a href="login.php" class="btn btn-login-link">Giriş Yap</a>
        </div>
    </div>
</body>
</html>