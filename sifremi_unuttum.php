<?php
session_start();
include("db.php");

$error = "";
$success = "";
$email = "";
$show_reset = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Kullanıcı e-posta girişi yaptı mı kontrol et
    if (isset($_POST['step']) && $_POST['step'] === 'find') {
        $email = trim($_POST['email'] ?? "");

        if (empty($email)) {
            $error = "Lütfen e-posta adresinizi giriniz!";
        } else {
            // Kullanıcı var mı kontrol et
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $show_reset = true;
            } else {
                $error = "Bu e-posta adresi sistemde bulunamadı!";
            }
        }
    }

    // Yeni şifre belirleme adımı
    if (isset($_POST['step']) && $_POST['step'] === 'reset') {
        $email = trim($_POST['email'] ?? "");
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($password) || empty($confirm_password)) {
            $error = "Lütfen tüm alanları doldurun!";
            $show_reset = true;
        } elseif ($password !== $confirm_password) {
            $error = "Şifreler eşleşmiyor!";
            $show_reset = true;
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            // Şifreyi güncelle
            $update = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $update->bindParam(':password', $hashed, PDO::PARAM_STR);
            $update->bindParam(':email', $email, PDO::PARAM_STR);
            if ($update->execute()) {
                $success = "Şifreniz başarıyla güncellendi! <a href='login.php'>Giriş Yap</a>";
            } else {
                $error = "Şifre güncellenirken bir hata oluştu!";
                $show_reset = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Şifre Sıfırla</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { height: 100%; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #52e5e7 0%, #130cb7 47%, #fff720 100%);
            background-size: 300% 300%;
            animation: bgmove 12s ease-in-out infinite;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
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
        .reset-card {
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
        .reset-title {
            text-align: center;
            font-weight: bold;
            font-size: 2rem;
            margin-bottom: 18px;
            background: linear-gradient(90deg,#130cb7,#52e5e7,#fff720);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-reset {
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
        .btn-reset:hover {
            background: linear-gradient(90deg,#52e5e7 0%,#fff720 100%);
            color: #fff;
            box-shadow: 0 4px 24px #130cb755;
        }
        .form-label { color: #130cb7; font-weight: 600;}
        .form-control { border-radius: 15px;}
        .alert-danger { background: #fff9c4; color: #ad1457; border: none;}
        .alert-success { background: #d0ffd6; color: #10451d; border: none;}
        a { color: #130cb7; }
        a:hover { color: #104080; }
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
    <div class="reset-card">
        <h2 class="reset-title">Şifre Sıfırla</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success text-center"><?= $success ?></div>
        <?php endif; ?>

        <?php if (!$success && !$show_reset): ?>
        <!-- Email girme formu -->
        <form action="" method="POST" class="w-100">
            <input type="hidden" name="step" value="find">
            <div class="mb-3">
                <label for="email" class="form-label">E-posta Adresiniz</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="E-posta adresinizi girin" required>
            </div>
            <button type="submit" class="btn btn-reset w-100">Devam Et</button>
        </form>
        <?php endif; ?>

        <?php if (!$success && $show_reset): ?>
        <!-- Şifre yenileme formu -->
        <form action="" method="POST" class="w-100">
            <input type="hidden" name="step" value="reset">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <div class="mb-3">
                <label for="password" class="form-label">Yeni Şifre</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Yeni şifrenizi girin" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Yeni Şifre (Tekrar)</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Yeni şifrenizi tekrar girin" required>
            </div>
            <button type="submit" class="btn btn-reset w-100">Şifreyi Güncelle</button>
        </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="login.php">Girişe Dön</a>
        </div>
    </div>
</body>
</html>