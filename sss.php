<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Sıkça Sorulan Sorular</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', Arial, sans-serif;
      background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
      min-height: 100vh;
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
      margin-bottom: 28px;
      text-shadow: 1px 1px 3px #fff3;
    }
    .faq-list {
      text-align: left;
      margin: 0;
      padding: 0;
    }
    .faq-item {
      background: linear-gradient(90deg, #43cea2 60%, #fffb7d 100%);
      border-radius: 13px;
      margin-bottom: 18px;
      padding: 18px 18px 12px 18px;
      box-shadow: 0 2px 8px #0001;
      transition: background 0.2s;
      cursor: pointer;
    }
    .faq-item:hover {
      background: linear-gradient(90deg, #fbc2eb 60%, #a18cd1 100%);
    }
    .faq-question {
      font-weight: bold;
      font-size: 1.12rem;
      margin-bottom: 10px;
      color: #185a9d;
    }
    .faq-answer {
      font-size: 1rem;
      color: #333;
      margin-left: 8px;
    }
    .back-link {
      display: block;
      margin-top: 32px;
      color: #185a9d;
      text-decoration: none;
      font-size: 1.08rem;
      font-weight: bold;
      transition: color 0.2s;
    }
    .back-link:hover {
      color: #43cea2;
      text-decoration: underline;
    }
    @media (max-width: 700px) {
      .container {
        max-width: 97vw;
        margin: 20px 5px 0 5px;
        padding: 15px 4vw 10px 4vw;
      }
      .faq-item {
        padding: 12px 8px 10px 8px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Sıkça Sorulan Sorular</h1>
    <div class="faq-list">
      <div class="faq-item">
        <div class="faq-question">Otobüs ücretleri ne kadar?</div>
        <div class="faq-answer">
          Tek bilet 25 TL, tam kart 23 TL, öğrenci 15 TL, iki okul arası 5 TL, diş hastanesi servisi 5 TL'dir.
        </div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Otobüs saatlerini nereden öğrenebilirim?</div>
        <div class="faq-answer">Seferler sayfamızdan tüm hatların güncel saatlerine ulaşabilirsiniz.</div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Şikayet veya önerimi nasıl iletebilirim?</div>
        <div class="faq-answer">Şikayet &amp; Öneri sayfasından kolayca geri bildirim gönderebilirsiniz.</div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Öğrenci kartı almak için ne yapmalıyım?</div>
        <div class="faq-answer">Öğrenci belgeniz ile birlikte başvuru noktalarımıza başvurabilirsiniz.</div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Kayıp eşyamı nasıl bulabilirim?</div>
        <div class="faq-answer">Kayıp eşya bildirimleri için iletişim sayfamızdan bize ulaşabilirsiniz.</div>
      </div>
    </div>
    <a class="back-link" href="anasayfa.php">&larr; Anasayfaya Dön</a>
  </div>
</body>
</html>