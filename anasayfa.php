<?php
session_start();
include("db.php");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Anasayfa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', Arial, sans-serif;
      background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
      min-height: 100vh;
      overflow-x: hidden;
    }
    .menu {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: linear-gradient(90deg, #43cea2, #185a9d);
      padding: 0 40px;
      height: 60px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      position: relative;
    }
    .menu .logo {
      color: #fff;
      font-size: 2rem;
      font-weight: bold;
      letter-spacing: 2px;
      margin-left: 80px;
      transition: margin 0.3s;
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
    .menu li a:hover {
      background: #fff;
      color: #185a9d;
    }
    main {
      text-align: center;
      margin-top: 70px;
    }
    .bus-visual-container {
      display: flex;
      justify-content: center;
      align-items: flex-end;
      height: 220px;
      margin-bottom: 30px;
      position: relative;
      z-index: 1;
      pointer-events: none;
      user-select: none;
      overflow-x: hidden;
      width: 100vw;
      max-width: 100%;
    }
    .bus-road-wrapper {
      position: relative;
      width: 380px;
      height: 60px;
      margin: 0 auto;
      display: flex;
      align-items: flex-end;
      justify-content: center;
    }
    .bus-road {
      width: 380px;
      height: 28px;
      background: linear-gradient(90deg, #555 70%, #222 100%);
      border-radius: 16px/14px;
      position: absolute;
      left: 0; right: 0; margin: auto;
      bottom: 24px;
      box-shadow: 0 6px 32px #0002;
      z-index: 1;
      overflow: hidden;
    }
    .bus-stop {
      position: absolute;
      z-index: 10;
      bottom: 24px;
      width: 36px;
      height: 60px;
      pointer-events: none;
      user-select: none;
      transition: left 0.3s, right 0.3s;
    }
    .stop-left {
      left: -18px;
    }
    .stop-right {
      right: -18px;
    }
    .bus-visual {
      position: absolute;
      left: 0;
      bottom: 30px;
      width: 120px;
      height: 60px;
      animation: busdrive 5s cubic-bezier(.46,.03,.52,.96) infinite;
      z-index: 2;
    }
    @keyframes busdrive {
      0%   { left: -130px; }
      9%   { transform: scaleY(1.03) scaleX(1.01) translateY(-4px); }
      12%  { transform: scaleY(0.98) scaleX(1.02) translateY(2px);}
      14%  { transform: scaleY(1.01) scaleX(1.02) translateY(-2px);}
      16%  { transform: scaleY(1) scaleX(1) translateY(0);}
      90%  { left: 64%; }
      100% { left: 110%; }
    }
    .bus-smoke {
      position: absolute;
      left: 20px;
      bottom: 57px;
      width: 16px; height: 16px;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, #bbb 85%, #fff0 100%);
      opacity: 0.4;
      animation: busSmoke 2.5s linear infinite;
      pointer-events: none;
      z-index: 0;
    }
    @keyframes busSmoke {
      0% { opacity: .4; transform: scale(0.5) translateY(0);}
      60% { opacity: .17;  }
      100% { opacity: 0; transform: scale(1.4) translateY(-24px);}
    }
    h1 {
      font-size: 2.5rem;
      color: #473a7c;
      margin-bottom: 40px;
      text-shadow: 1px 1px 3px #fff3;
      z-index: 2;
      position: relative;
      opacity: 0;
      animation: hgFade 1.3s forwards;
      animation-delay: 0.2s;
    }
    @keyframes hgFade {
      to { opacity: 1; }
    }
    .cards {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
      z-index: 2;
      position: relative;
    }
    .card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 4px 24px #0001;
      width: 180px;
      height: 180px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: #333;
      font-weight: bold;
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
      text-decoration: none;
    }
    .card.ucretler { background: linear-gradient(135deg, #00c3ff, #ffff1c); }
    .card.sikayet { background: linear-gradient(135deg, #ff5858, #f09819); }
    .card.sss { background: linear-gradient(135deg, #43cea2, #fffb7d); }
    .card:hover {
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 8px 32px #0002;
    }
    #sidebarToggle {
      position: fixed;
      top: 8px;
      left: 0; /* EN SOLA YAPIŞIK */
      z-index: 1200;
      background: #43cea2;
      border: none;
      padding: 10px 14px;
      border-radius: 7px;
      cursor: pointer;
      transition: opacity 0.2s;
      box-shadow: 0 2px 8px #0002;
    }
    #sidebarToggle span {
      font-size: 1.6rem;
      color: #fff;
      display: block;
      line-height: 1;
    }
    #sidebarToggle.hidden {
      opacity: 0;
      pointer-events: none;
    }
    #sidebar {
      position: fixed;
      top: 0;
      left: -260px;
      width: 240px;
      height: 100vh;
      background: #fff;
      box-shadow: 2px 0 12px #8883;
      z-index: 1100;
      transition: left 0.3s;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 32px 0 0 0;
    }
    #sidebar img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      border: 2px solid #43cea2;
    }
    #sidebar div {
      text-align: center;
    }
    #sidebar hr {
      width: 80%;
      margin: 20px 0 12px 0;
      border: 0;
      border-top: 1px solid #eee;
    }
    #sidebar a {
      color: #185a9d;
      text-decoration: none;
      font-size: 1.07rem;
      margin: 10px 0;
      display: block;
    }
    #sidebar a:last-child {
      color: #e65100;
    }
    #sidebarClose {
      margin-top: 32px;
      background: #43cea2;
      color: #fff;
      border: none;
      padding: 6px 24px;
      border-radius: 16px;
      font-size: 1rem;
      cursor: pointer;
    }
    .sidebar-email {
      font-size: 0.95rem;
      color: #888;
      margin-bottom: 2px;
      max-width: 180px;
      word-break: break-all;
      overflow-wrap: anywhere;
      text-align: center;
      margin-left: auto;
      margin-right: auto;
      display: block;
    }
    @media (max-width: 700px) {
      .bus-visual-container {
        height: 120px;
      }
      .bus-road-wrapper {
        width: 90vw;
        min-width: 220px;
      }
      .bus-road {
        width: 90vw;
        left: 0; right: 0; margin: auto;
        border-radius: 13px/10px;
      }
      .bus-visual {
        width: 66px;
        height: 33px;
        bottom: 22px;
      }
      .bus-smoke {
        left: 12px;
        bottom: 30px;
        width: 10px;
        height: 10px;
      }
      .stop-left { left: -5px; }
      .stop-right { right: -5px; }
      .sidebar-email {
        max-width: 70vw;
      }
      .menu .logo {
        margin-left: 44px;
      }
      #sidebar {
        width: 90vw;
        left: -92vw;
      }
      .cards {
        flex-direction: column;
        align-items: center;
      }
      .card {
        width: 80vw;
        max-width: 300px;
        height: 120px;
        margin-bottom: 20px;
      }
      main {
        margin-top: 40px;
      }
      #sidebarToggle {
        left: 0; /* Mobilde de en sola yapışık */
      }
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Profil Sidebar Butonu (Hamburger) -->
  <button id="sidebarToggle">
    <span>&#9776;</span>
  </button>

  <!-- Sidebar Menü -->
  <div id="sidebar">
    <img src="<?php echo isset($_SESSION['profil_foto']) ? $_SESSION['profil_foto'] : 'https://www.gravatar.com/avatar/?d=mp&s=80'; ?>" alt="Profil Foto">
    <div style="margin:16px 0 6px 0;font-weight:bold;font-size:1.13rem;">
      <?php
        if (isset($_SESSION["kullanici_adi"]) && $_SESSION["kullanici_adi"] != "") {
          echo htmlspecialchars($_SESSION["kullanici_adi"]);
        } else {
          echo '<a href="login.php" style="color:#e65100;">Giriş Yap</a>';
        }
      ?>
    </div>
    <div class="sidebar-email">
      <?php
        if (isset($_SESSION["kullanici_email"]) && $_SESSION["kullanici_email"] != "") {
          echo htmlspecialchars($_SESSION["kullanici_email"]);
        }
      ?>
    </div>
    <hr>
    <a href="fav.php">⭐ Favori Seferlerim</a>
    <a href="yorumlarim.php">💬 Yorumlarım</a>
    <a href="ayarlar.php">⚙️ Ayarlar</a>
    <a href="logout.php" style="color:#e65100;">⏏️ Çıkış Yap</a>
    <button id="sidebarClose">Kapat</button>
  </div>

  <nav class="menu">
    <div class="logo">AmasyaBus</div>
    <ul>
      <li><a href="seferler.php">Seferler</a></li>
      <li><a href="otobus_saatleri.php">Otobüs Saatleri</a></li>
      <li><a href="hakkimizda.php">Hakkımızda</a></li>
      <li><a href="iletisim.php">İletişim</a></li>
    </ul>
  </nav>
  <main>
    <h1>Amasya Şehir İçi Otobüsleri Sayfasına Hoş Geldiniz!</h1>
    <div class="bus-visual-container" aria-hidden="true">
      <div class="bus-road-wrapper">
        <!-- Sol Durak -->
        <svg class="bus-stop stop-left" viewBox="0 0 36 60">
          <rect x="15" y="18" width="6" height="35" rx="2.5" fill="#888"/>
          <ellipse cx="18" cy="13" rx="13" ry="7" fill="#fff" stroke="#2196f3" stroke-width="2"/>
          <text x="18" y="17" font-size="8" text-anchor="middle" fill="#2196f3" font-family="Arial" font-weight="bold">DURAK</text>
        </svg>
        <div class="bus-road"></div>
        <!-- Otobüs -->
        <svg class="bus-visual" viewBox="0 0 120 60">
          <g>
            <rect x="10" y="20" rx="8" ry="8" width="85" height="30" fill="#2196f3" stroke="#1565c0" stroke-width="2"/>
            <rect x="85" y="30" rx="8" ry="8" width="25" height="18" fill="#b3e5fc" stroke="#1565c0" stroke-width="2"/>
            <rect x="17" y="26" width="26" height="14" fill="#e3f2fd" stroke="#1565c0" stroke-width="1.2"/>
            <rect x="47" y="26" width="20" height="14" fill="#e3f2fd" stroke="#1565c0" stroke-width="1.2"/>
            <rect x="69" y="26" width="13" height="14" fill="#e3f2fd" stroke="#1565c0" stroke-width="1.2"/>
            <circle cx="32" cy="54" r="7" fill="#222" stroke="#b0bec5" stroke-width="2"/>
            <circle cx="90" cy="54" r="7" fill="#222" stroke="#b0bec5" stroke-width="2"/>
            <ellipse cx="18" cy="49" rx="2.5" ry="1.1" fill="#a18cd1" opacity="0.4"/>
            <ellipse cx="105" cy="49" rx="2.5" ry="1.1" fill="#a18cd1" opacity="0.4"/>
            <rect x="102" y="36" width="5" height="4" fill="#ffd200" stroke="#1565c0" stroke-width="1"/>
          </g>
        </svg>
        <div class="bus-smoke"></div>
        <!-- Sağ Durak -->
        <svg class="bus-stop stop-right" viewBox="0 0 36 60">
          <rect x="15" y="18" width="6" height="35" rx="2.5" fill="#888"/>
          <ellipse cx="18" cy="13" rx="13" ry="7" fill="#fff" stroke="#2196f3" stroke-width="2"/>
          <text x="18" y="17" font-size="8" text-anchor="middle" fill="#2196f3" font-family="Arial" font-weight="bold">DURAK</text>
        </svg>
      </div>
    </div>
    <div class="cards">
      <a href="ucretler.php" class="card ucretler">Otobüs Ücretleri</a>
      <a href="sikayet.php" class="card sikayet">Şikayet / Öneri</a>
      <a href="sss.php" class="card sss">Sıkça Sorulan Sorular</a>
    </div>
  </main>
  <script>
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebarClose = document.getElementById("sidebarClose");

    function hideToggle() {
      sidebarToggle.classList.add("hidden");
    }
    function showToggle() {
      sidebarToggle.classList.remove("hidden");
    }

    sidebarToggle.onclick = function(e) {
      e.stopPropagation();
      sidebar.style.left = "0";
      hideToggle();
    }
    sidebarClose.onclick = function() {
      if(window.innerWidth < 700) {
        sidebar.style.left = "-92vw";
      } else {
        sidebar.style.left = "-260px";
      }
      showToggle();
    }
    document.addEventListener('click', function(e) {
      if (!sidebar.contains(e.target) && e.target !== sidebarToggle) {
        if(window.innerWidth < 700) {
          sidebar.style.left = "-92vw";
        } else {
          sidebar.style.left = "-260px";
        }
        showToggle();
      }
    });
    window.addEventListener('resize', function() {
      if(sidebar.style.left === "0px") {
        hideToggle();
      } else {
        showToggle();
      }
    });

    // Bus smoke effect (clone and animate)
    setInterval(function() {
      var container = document.querySelector('.bus-visual-container');
      if (!container) return;
      var smoke = document.createElement('div');
      smoke.className = 'bus-smoke';
      smoke.style.left = (window.innerWidth < 700 ? "10px" : "20px");
      container.appendChild(smoke);
      setTimeout(function() { smoke.remove(); }, 2500);
    }, 900);
  </script>
</body>
</html>