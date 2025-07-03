<?php
session_start();
include("db.php");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $adsoyad = trim($_POST["adsoyad"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $baslik = trim($_POST["baslik"] ?? "");
    $mesaj = trim($_POST["mesaj"] ?? "");

    if ($adsoyad && $email && $baslik && $mesaj) {
        try {
            $sql = "INSERT INTO sikayet_oneri (adsoyad, email, baslik, mesaj) VALUES (:adsoyad, :email, :baslik, :mesaj)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':adsoyad', $adsoyad, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':baslik', $baslik, PDO::PARAM_STR);
            $stmt->bindParam(':mesaj', $mesaj, PDO::PARAM_STR);
            $stmt->execute();
            $success = "Şikayet/Öneriniz başarıyla kaydedildi. Teşekkür ederiz!";
        } catch (Exception $e) {
            $error = "Bir hata oluştu: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = "Lütfen tüm alanları doldurunuz.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Şikayet &amp; Öneri</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', Arial, sans-serif;
      background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
      min-height: 100vh;
    }
    .container {
      max-width: 450px;
      margin: 50px auto 0 auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 6px 24px #0001;
      padding: 36px 28px 28px 28px; /* padding artırıldı */
      text-align: center;
      box-sizing: border-box;
    }
    h1 {
      color: #f857a6;
      font-size: 2rem;
      margin-bottom: 22px;
      text-shadow: 1px 1px 3px #fff3;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 14px;
    }
    .input-group {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 4px;
      width: 100%;
      box-sizing: border-box;
    }
    label {
      font-weight: bold;
      color: #185a9d;
      margin-left: 2px;
      font-size: 0.98rem;
    }
    input, textarea {
      width: 96%; /* Burada %100 yerine %96 verdik */
      border-radius: 8px;
      border: 1px solid #ddd;
      padding: 9px 12px;
      font-size: 1rem;
      transition: border 0.2s;
      font-family: inherit;
      margin-left: 2%;
      box-sizing: border-box;
    }
    input:focus, textarea:focus {
      outline: none;
      border: 1.5px solid #f857a6;
    }
    textarea {
      min-height: 70px;
      max-height: 200px;
      resize: vertical;
    }
    .form-btn {
      background: linear-gradient(90deg, #f857a6, #ff5858);
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 12px 0;
      font-size: 1.09rem;
      font-weight: bold;
      margin-top: 6px;
      cursor: pointer;
      transition: background 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px #0001;
      width: 100%;
    }
    .form-btn:hover {
      background: linear-gradient(90deg, #ff5858, #f857a6);
    }
    .alert-success {
      background: #43cea2;
      color: #fff;
      border-radius: 9px;
      padding: 10px 0;
      margin-bottom: 10px;
      font-weight: bold;
    }
    .alert-error {
      background: #ff5858;
      color: #fff;
      border-radius: 9px;
      padding: 10px 0;
      margin-bottom: 10px;
      font-weight: bold;
    }
    .back-link {
      display: block;
      margin-top: 28px;
      color: #185a9d;
      text-decoration: none;
      font-size: 1.08rem;
      font-weight: bold;
      transition: color 0.2s;
    }
    .back-link:hover {
      color: #f857a6;
      text-decoration: underline;
    }
    @media (max-width: 600px) {
      .container {
        max-width: 97vw;
        margin: 20px 5px 0 5px;
        padding: 18px 6vw 10px 6vw;
      }
      input, textarea {
        width: 98%;
        margin-left: 1%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Şikayet &amp; Öneri</h1>
    <?php if ($success): ?>
      <div class="alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
      <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <div class="input-group">
        <label for="adsoyad">Ad Soyad</label>
        <input type="text" id="adsoyad" name="adsoyad" required
          value="<?= htmlspecialchars($_SESSION['kullanici_adi'] ?? "") ?>">
      </div>
      <div class="input-group">
        <label for="email">E-posta</label>
        <input type="email" id="email" name="email" required
          value="<?= htmlspecialchars($_SESSION['kullanici_email'] ?? "") ?>">
      </div>
      <div class="input-group">
        <label for="baslik">Başlık</label>
        <input type="text" id="baslik" name="baslik" required placeholder="Konu başlığı">
      </div>
      <div class="input-group">
        <label for="mesaj">Mesajınız</label>
        <textarea id="mesaj" name="mesaj" required placeholder="Şikayet veya önerinizi yazınız"></textarea>
      </div>
      <button type="submit" class="form-btn">Gönder</button>
    </form>
    <a class="back-link" href="anasayfa.php">&larr; Anasayfaya Dön</a>
  </div>
</body>
</html>