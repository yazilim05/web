<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcı bilgilerini çek
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Parola değiştirme işlemi
$success = $error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $new_password2 = $_POST['new_password2'] ?? '';

    // Mevcut parola kontrolü
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || !password_verify($old_password, $row['password'])) {
        $error = "Mevcut parola hatalı!";
    } elseif ($new_password !== $new_password2) {
        $error = "Yeni parolalar eşleşmiyor!";
    } elseif (strlen($new_password) < 6) {
        $error = "Yeni parola en az 6 karakter olmalı!";
    } else {
        // Parolayı güncelle
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([password_hash($new_password, PASSWORD_DEFAULT), $_SESSION['user_id']]);
        $success = "Parolanız başarıyla değiştirildi.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ayarlar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            overflow-x: hidden;
        }
        .bus-header {
            background: linear-gradient(90deg, #43cea2 20%, #185a9d 100%);
            color: #fff;
            padding: 30px 0 24px 0;
            text-align: center;
            border-radius: 0 0 40px 40px;
            box-shadow: 0 4px 18px #0001;
            font-size: 2.3rem;
            font-family: 'Montserrat', Arial, sans-serif;
            letter-spacing: 2px;
            font-weight: bold;
            margin-bottom: 0;
            position: relative;
        }
        .bus-header .bus-icon {
            vertical-align: middle;
            margin-right: 16px;
            filter: drop-shadow(0 2px 6px #fff6);
            display: inline-block;
        }
        .settings-card {
            max-width: 550px;
            margin: 120px auto 40px auto;
            border-radius: 2rem;
            box-shadow: 0 8px 32px #0002;
            background: #fff9;
            padding: 0 0 24px 0;
        }
        .main-header {
            background: linear-gradient(90deg, #ffd200 0%, #43cea2 100%);
            color: #473a7c;
            border-radius: 2rem 2rem 0 0;
            padding: 2.1rem 1rem 1.1rem 1rem;
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 2px;
            box-shadow: 0 4px 18px #8881;
        }
        label {
            font-weight: 600;
            color: #185a9d;
            font-size: 1.08rem;
        }
        .form-control:focus {
            border-color: #a18cd1;
            box-shadow: 0 0 0 0.2rem #a18cd188;
        }
        .btn-main {
            background: linear-gradient(90deg,#43cea2 0%, #185a9d 100%);
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 15px;
            font-size: 1.08rem;
            box-shadow: 0 1px 10px #185a9d22;
        }
        .btn-main:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
            color: #fff;
        }
        .alert-success {
            background: linear-gradient(90deg, #43cea2 0%, #ffd200 100%);
            color: #473a7c;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            font-size: 1.06rem;
        }
        .alert-danger {
            background: linear-gradient(90deg, #ffaf7b 0%, #d76d77 100%);
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            font-size: 1.06rem;
        }
        .section-title {
            font-size: 1.23rem;
            color: #185a9d;
            font-weight: bold;
            margin-bottom: 14px;
            margin-top: 8px;
            letter-spacing: 1px;
        }
        .bg-light {
            background: #fbc2eb33 !important;
            color: #473a7c;
            font-weight: bold;
            border: 1px solid #a18cd122;
        }
        @media (max-width: 600px) {
            .settings-card { margin: 80px auto 10px auto; }
            .main-header { font-size: 1.2rem; padding: 1.1rem 0.4rem 1rem 0.4rem;}
            .bus-header { font-size: 1.3rem; padding: 16px 0 13px 0;}
            .bus-header .bus-icon svg { width: 38px !important; height: 18px !important; }
        }
    </style>
</head>
<body>
    <div class="bus-header">
        <span class="bus-icon" style="vertical-align:middle;">
          <svg width="56" height="34" viewBox="0 0 56 34" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline;">
            <rect x="4" y="8" width="42" height="17" rx="5.5" fill="#2196f3" stroke="#1565c0" stroke-width="2"/>
            <rect x="46.5" y="14.5" width="5.5" height="10" rx="2" fill="#b3e5fc" stroke="#1565c0" stroke-width="1.3"/>
            <rect x="10" y="12" width="10" height="7" fill="#e3f2fd" stroke="#1565c0" stroke-width="1"/>
            <rect x="23" y="12" width="10" height="7" fill="#e3f2fd" stroke="#1565c0" stroke-width="1"/>
            <rect x="36" y="12" width="7" height="7" fill="#e3f2fd" stroke="#1565c0" stroke-width="1"/>
            <circle cx="14" cy="27" r="4" fill="#222" stroke="#b0bec5" stroke-width="1.7"/>
            <circle cx="40" cy="27" r="4" fill="#222" stroke="#b0bec5" stroke-width="1.7"/>
            <ellipse cx="7.6" cy="25.8" rx="1.5" ry="0.7" fill="#a18cd1" opacity="0.4"/>
            <ellipse cx="50.3" cy="25.8" rx="1.5" ry="0.7" fill="#a18cd1" opacity="0.4"/>
            <rect x="52.2" y="17" width="2.1" height="2.8" fill="#ffd200" stroke="#1565c0" stroke-width="0.8"/>
          </svg>
        </span>
        AmasyaBus <span style="font-size:1.08rem; font-weight:400;">| Ayarlar</span>
    </div>
    <div class="settings-card">
        <div class="main-header">
            <span style="font-size:1.25rem;">⚙️</span> Hesap Ayarları
        </div>
        <div class="p-4">
            <div class="section-title">Kullanıcı Bilgileri</div>
            <div class="mb-3">
                <label>Kullanıcı Adı:</label>
                <div class="form-control bg-light"><?= htmlspecialchars($user['username']) ?></div>
            </div>
            <div class="mb-4">
                <label>E-Posta:</label>
                <div class="form-control bg-light"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <hr>
            <div class="section-title">Parola Değiştir</div>
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label>Mevcut Parola</label>
                    <input type="password" class="form-control" name="old_password" required>
                </div>
                <div class="mb-3">
                    <label>Yeni Parola</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label>Yeni Parola (Tekrar)</label>
                    <input type="password" class="form-control" name="new_password2" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-main w-100">Parolayı Değiştir</button>
            </form>
            <hr>
            <a href="anasayfa.php" class="btn btn-outline-secondary mt-3 w-100" style="font-weight:bold;letter-spacing:1px;"><span style="font-size:1.15rem;">🏠</span> Ana Sayfa</a>
        </div>
    </div>
</body>
</html>