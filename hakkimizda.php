<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Hakkımızda</title>
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
      max-width: 650px;
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
      margin-bottom: 18px;
      text-shadow: 1px 1px 3px #fff3;
    }
    .about-text {
      color: #444;
      text-align: left;
      font-size: 1.07rem;
      line-height: 1.7;
      margin: 0 auto 24px auto;
      max-width: 96%;
    }
    .team-section {
      margin-top: 30px;
      text-align: left;
    }
    .team-title {
      font-size: 1.09rem;
      color: #185a9d;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .team-list {
      list-style: disc;
      margin-left: 28px;
      padding-left: 0;
      color: #444;
      font-size: 1rem;
    }
    @media (max-width: 700px) {
      .menu { padding: 0 10px; }
      .container {
        max-width: 97vw;
        margin: 20px 5px 0 5px;
        padding: 15px 4vw 10px 4vw;
      }
      .about-text { font-size: 0.99rem; }
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
      <li><a href="hakkimizda.php" class="active">Hakkımızda</a></li>
      <li><a href="iletisim.php">İletişim</a></li>
    </ul>
  </nav>
  <div class="container">
    <h1>Hakkımızda</h1>
    <div class="about-text">
      <p>
        Amasya Şehir İçi Otobüsleri olarak amacımız, şehir içi ulaşımı güvenli, ekonomik ve konforlu hale getirmektir. Yolcularımızın memnuniyetini ön planda tutarak, modern araç filomuz ve güler yüzlü ekibimizle Amasya'nın her noktasına hizmet veriyoruz.
      </p>
      <p>
        Sefer saatlerimiz, ücretlendirmelerimiz ve hizmet kalitemizle ilgili sürekli iyileştirmeler yapmakta, sizlerden gelen geri bildirimleri dikkate almaktayız. Şikayet ve önerileriniz için <a href="sikayet.php" style="color:#185a9d;text-decoration:underline;">buraya tıklayarak</a> bize ulaşabilirsiniz.
      </p>
      <p>
        Sağlıklı, güvenli ve rahat bir ulaşım için tüm ekibimizle çalışmaya devam ediyoruz. Bizi tercih ettiğiniz için teşekkür ederiz!
      </p>
    </div>
    <div class="team-section">
      <div class="team-title">Ekibimiz</div>
      <ul class="team-list">
        <li>Yönetim ve Operasyon Ekibi</li>
        <li>Sürücülerimiz</li>
        <li>Müşteri Destek ve İletişim Birimi</li>
        <li>Teknik ve Bakım Personeli</li>
      </ul>
    </div>
  </div>
</body>
</html>