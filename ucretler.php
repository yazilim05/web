<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Otobüs Ücretleri</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', Arial, sans-serif;
      background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
      min-height: 100vh;
    }
    .container {
      max-width: 700px;
      margin: 50px auto 0 auto;
      padding: 24px 0 0 0;
      text-align: center;
    }
    h1 {
      color: #185a9d;
      font-size: 2.2rem;
      margin-bottom: 36px;
      text-shadow: 1px 1px 3px #fff3;
    }
    .fare-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 28px;
      justify-content: center;
    }
    .fare-card {
      width: 220px;
      min-height: 110px;
      border-radius: 18px;
      box-shadow: 0 4px 24px #0001;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: bold;
      font-size: 1.1rem;
      transition: transform 0.18s, box-shadow 0.18s;
      background: #888; /* fallback */
      padding: 20px 0 18px 0;
      position: relative;
    }
    .fare-card span {
      margin: 6px 0;
    }
    .fare-card.bilet { background: linear-gradient(135deg, #00c3ff 50%, #ffff1c 100%); color: #185a9d;}
    .fare-card.tam { background: linear-gradient(135deg, #43cea2 60%, #185a9d 100%);}
    .fare-card.ogrenci { background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); color: #473a7c;}
    .fare-card.okul { background: linear-gradient(135deg, #f857a6 0%, #ff5858 100%);}
    .fare-card.dishastanesi { background: linear-gradient(135deg, #43cea2 0%, #fffb7d 100%); color: #333;}
    .fare-card .ucret {
      font-size: 1.6rem;
      margin-top: 10px;
      background: #fff5;
      border-radius: 12px;
      padding: 6px 18px;
      color: #222;
      font-weight: bold;
      letter-spacing: 1px;
      min-width: 70px;
    }
    @media (max-width: 700px) {
      .container {
        max-width: 96vw;
        margin: 20px 2vw 0 2vw;
        padding: 8px 0 0 0;
      }
      .fare-cards {
        flex-direction: column;
        gap: 18px;
        align-items: center;
      }
      .fare-card {
        width: 90vw;
        max-width: 310px;
        min-height: 80px;
        font-size: 1.06rem;
        padding: 12px 0 10px 0;
      }
    }
    .back-link {
      display: inline-block;
      margin-top: 36px;
      color: #185a9d;
      text-decoration: none;
      font-size: 1.08rem;
      font-weight: bold;
      transition: color 0.2s;
      background: #fff8;
      border-radius: 9px;
      padding: 7px 22px;
    }
    .back-link:hover {
      color: #43cea2;
      background: #fff;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Otobüs Ücretleri</h1>
    <div class="fare-cards">
      <div class="fare-card bilet">
        <span>Tek Bilet</span>
        <span class="ucret">25 TL</span>
      </div>
      <div class="fare-card tam">
        <span>Tam Kart</span>
        <span class="ucret">23 TL</span>
      </div>
      <div class="fare-card ogrenci">
        <span>Öğrenci Kartı</span>
        <span class="ucret">15 TL</span>
      </div>
      <div class="fare-card okul">
        <span>İki Okul Arası</span>
        <span class="ucret">5 TL</span>
      </div>
      <div class="fare-card dishastanesi">
        <span>Diş Hastanesi </span>
        <span class="ucret">5 TL</span>
      </div>
    </div>
    <a class="back-link" href="anasayfa.php">&larr; Anasayfaya Dön</a>
  </div>
</body>
</html>