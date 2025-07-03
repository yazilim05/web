<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>İletişim</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', Arial, sans-serif;
      background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
      min-height: 100vh;
    }
    .menu {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: linear-gradient(90deg, #43cea2, #185a9d);
      padding: 0 40px;
      height: 60px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .menu .logo {
      color: #fff;
      font-size: 2rem;
      font-weight: bold;
      letter-spacing: 2px;
    }
    .menu ul {
      list-style: none;
      display: flex;
      gap: 30px;
      margin: 0;
      padding: 0;
    }
    .menu li a {
      color: #fff;
      text-decoration: none;
      font-size: 1.1rem;
      padding: 8px 16px;
      border-radius: 20px;
      transition: background 0.3s, color 0.3s;
    }
    .menu li a:hover,
    .menu li a.active {
      background: #fff;
      color: #185a9d;
    }
    .container {
      max-width: 500px;
      margin: 50px auto 0 auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 6px 24px #0001;
      padding: 36px 28px 28px 28px;
      box-sizing: border-box;
      text-align: center;
    }
    h1 {
      color: #185a9d;
      font-size: 2.1rem;
      margin-bottom: 22px;
      text-shadow: 1px 1px 3px #fff3;
    }
    .contact-info {
      text-align: left;
      margin: 0 auto 28px auto;
      max-width: 96%;
      color: #444;
      font-size: 1.09rem;
      line-height: 1.65;
    }
    .contact-info b {
      color: #185a9d;
    }
    @media (max-width: 700px) {
      .menu { padding: 0 10px; }
      .container {
        max-width: 97vw;
        margin: 20px 5px 0 5px;
        padding: 15px 4vw 10px 4vw;
      }
      .contact-info { font-size: 0.98rem;}
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
</head>
<body>
  <nav class="menu">
    <div class="logo">AmasyaBus</div>
    <ul>
      <li><a href="anasayfa.php">Anasayfa</a></li>
      <li><a href="seferler.php">Seferler</a></li>
      <li><a href="otobus_saatleri.php">Otobüs Saatleri</a></li>
      <li><a href="hakkimizda.php">Hakkımızda</a></li>
      <li><a href="iletisim.php" class="active">İletişim</a></li>
    </ul>
  </nav>
  <div class="container">
    <h1>İletişim</h1>
    <div class="contact-info">
      <b>Adres:</b> Yüzevler, Ferhat Sinan Sağıroğlu Sk. No:4, Kat: 2, Merkez/Amasya <br>
      <b>Telefon:</b> 0(358) 218 23 64<br>
      <b>E-posta:</b> info@amasyaotobus.com<br>
      <b>Çalışma Saatleri:</b> 08:00 - 18:00 (Hafta içi)<br>
    </div>
  </div>
</body>
</html>