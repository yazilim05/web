<?php
session_start();
include("db.php");

$schedule_id = $_GET['schedule_id'] ?? null;
if (!$schedule_id) die("Sefer bulunamadı.");

// Sefer ve hat bilgisi
$sql = "SELECT s.*, r.route_number, r.route_name, s.day_type, s.route_id, s.departure_time
        FROM schedules s
        JOIN routes r ON s.route_id = r.route_id
        WHERE s.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$schedule_id]);
$sefer = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$sefer) die("Sefer bulunamadı.");

// Duraklar ve varış saatleri (Stored Procedure kullanılıyor!)
$stmt = $pdo->prepare("CALL GetStopsForSchedule(?)");
$stmt->execute([$schedule_id]);
$stops = $stmt->fetchAll(PDO::FETCH_ASSOC);
do { $stmt->nextRowset(); } while ($stmt->columnCount());
if (!$stops) $stops = [["stop_order" => 1, "stop_name" => "DURAK YOK", "arrival_time" => ""]];

// SİMÜLASYON: rastgele şu anki durak/sonraki durak
$currentIndex = rand(0, max(count($stops)-2,0));
$currentStop = $stops[$currentIndex]['stop_name'] ?? '';
$nextStop = $stops[$currentIndex+1]['stop_name'] ?? '';
$nextStopArrival = $stops[$currentIndex+1]['arrival_time'] ?? '';

// Favori kontrolü
$is_fav = false;
$fav_msg = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND schedule_id = ?");
    $stmt->execute([$_SESSION['user_id'], $schedule_id]);
    $is_fav = (bool)$stmt->fetch();
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fav_ekle'])) {
    if (isset($_SESSION['user_id']) && !$is_fav) {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, schedule_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $schedule_id]);
        $is_fav = true;
        $fav_msg = "Sefer favorilerinize eklendi!";
    }
}

// Yorum ekleme
$yorum_msg = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['yorum']) && trim($_POST['yorum']) !== "") {
    if (isset($_SESSION['user_id'])) {
        $yorum = trim($_POST['yorum']);
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, schedule_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $schedule_id, $yorum]);
        header("Location: sefer_detay.php?schedule_id=".$schedule_id."&yorum_eklendi=1");
        exit;
    } else {
        $yorum_msg = "Yorum yapmak için giriş yapmalısınız!";
    }
}
if (isset($_GET['yorum_eklendi']) && $_GET['yorum_eklendi'] == 1) {
    $yorum_msg = "Yorumunuz eklendi!";
}

// Yorumlar
$stmt = $pdo->prepare("SELECT c.comment_text, c.created_at, u.username 
                       FROM comments c 
                       JOIN users u ON c.user_id = u.id 
                       WHERE c.schedule_id = ? 
                       ORDER BY c.created_at DESC");
$stmt->execute([$schedule_id]);
$yorumlar = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Simülasyon için örnek koordinatlar ---
$defaultCoords = [
    [40.654, 35.835], [40.657, 35.837], [40.659, 35.840], [40.662, 35.845],
    [40.665, 35.849], [40.668, 35.852], [40.670, 35.856], [40.674, 35.859]
];
$stopCoords = array_slice($defaultCoords, 0, count($stops));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sefer Detay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body { margin: 0; font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); min-height: 100vh; }
        .menu { display: flex; justify-content: space-between; align-items: center; background: linear-gradient(90deg, #43cea2, #185a9d); padding: 0 40px; height: 60px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .menu .logo { color: #fff; font-size: 2rem; font-weight: bold; letter-spacing: 2px; }
        .menu ul { list-style: none; display: flex; gap: 30px; margin: 0; padding: 0; }
        .menu li a { color: #fff; text-decoration: none; font-size: 1.1rem; padding: 8px 16px; border-radius: 20px; transition: background 0.3s, color 0.3s; }
        .menu li a:hover { background: #fff; color: #185a9d; }
        main { max-width: 900px; margin: 40px auto 0 auto; background: #fff9; border-radius: 20px; box-shadow: 0 8px 32px #0002; padding: 30px; }
        h2 { color: #473a7c; margin-bottom: 18px; text-align:left; }
        .bus-info { font-size: 1.07rem; margin-bottom: 6px; color: #333;}
        .minutes { font-size: 1.2rem; color: #e65100; font-weight: bold; margin-bottom: 20px;}
        #map { height: 330px; width: 100%; margin-bottom: 22px; border-radius: 9px;}
        .stops-list { margin: 0; padding-left: 20px; }
        .stops-list li { color: #473a7c; }
        .section { margin-bottom: 24px; }
        .favorite-btn { background: linear-gradient(90deg, #43cea2, #185a9d); color: #fff; border: none; border-radius: 20px; padding: 8px 24px; font-size: 1.1rem; cursor: pointer; font-weight: bold; margin-bottom: 10px; transition: background 0.3s, color 0.3s;}
        .favorite-btn.active, .favorite-btn:disabled { background: #ffd200; color: #473a7c; cursor: not-allowed;}
        .fav-msg { color: #007820; font-weight: bold; margin-bottom:12px; }
        .comments { margin-top: 30px;}
        .comment-form textarea { width: 100%; min-height: 60px; border-radius: 8px; border: 1px solid #a18cd1; padding: 8px;}
        .comment-form button { margin-top: 4px; background: #43cea2; border: none; color: #fff; padding: 7px 20px; border-radius: 20px; font-size: 1rem; cursor:pointer;}
        .comment { padding: 7px 0; border-bottom: 1px solid #e2e2e2; }
        .comment b { color: #185a9d; }
        @media (max-width: 900px) { main { max-width: 98vw; padding: 12px 2vw 26px 2vw; } #map {height: 200px;} }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="menu">
        <div class="logo">AmasyaBus</div>
        <ul>
            <li><a href="seferler.php">Seferler</a></li>
            <li><a href="hakkimizda.php">Hakkımızda</a></li>
            <li><a href="iletisim.php">İletişim</a></li>
        </ul>
    </nav>
    <main>
        <h2><?= htmlspecialchars($sefer['route_number']." - ".$sefer['route_name']) ?></h2>
        <div class="section">
            <div class="bus-info">Gün türü: <b><?= htmlspecialchars($sefer['day_type']) ?></b></div>
            <div class="bus-info">Kalkış saati: <b><?= htmlspecialchars(substr($sefer['departure_time'], 0, 5)) ?></b></div>
            <div class="bus-info">Şu an: <b><?= htmlspecialchars($currentStop) ?></b></div>
            <div class="bus-info">Sonraki durak: <b><?= htmlspecialchars($nextStop) ?></b></div>
            <?php if (!empty($nextStopArrival)): ?>
                <div class="minutes">
                    Tahmini varış: 
                    <span class="arrival-time" id="next-arrival-time" data-arrival="<?= htmlspecialchars($nextStopArrival) ?>">
                        <?= htmlspecialchars(substr($nextStopArrival, 0, 5)) ?>
                    </span>
                    (<span class="minutes" id="next-minutes-left" data-arrival="<?= htmlspecialchars($nextStopArrival) ?>"></span> sonra)
                </div>
            <?php endif; ?>
        </div>
        <div class="section">
            <div id="map"></div>
        </div>
        <div class="section">
            <b>Güzergah & Varış Saatleri:</b>
            <ul class="stops-list">
                <?php foreach($stops as $stop): ?>
                    <li>
                        <?= htmlspecialchars($stop['stop_order']) ?>. 
                        <?= htmlspecialchars($stop['stop_name']) ?>
                        <?php if (!empty($stop['arrival_time'])): ?>
                            - <span class="arrival-time" data-arrival="<?= htmlspecialchars($stop['arrival_time']) ?>">
                                <?= htmlspecialchars(substr($stop['arrival_time'], 0, 5)) ?>
                              </span>
                            <span class="minutes" data-arrival="<?= htmlspecialchars($stop['arrival_time']) ?>"></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if($fav_msg): ?>
            <div class="fav-msg"><?= htmlspecialchars($fav_msg) ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if ($is_fav): ?>
                <button class="favorite-btn active" disabled>⭐ Favorilerde</button>
            <?php else: ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="fav_ekle" value="1">
                    <button type="submit" class="favorite-btn">⭐ Favorilere Ekle</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p style="margin-top:10px;">Favorilere eklemek için <a href="login.php">giriş yapın</a>.</p>
        <?php endif; ?>

        <div class="comments">
            <h3>Yorumlar</h3>
            <?php if (!empty($yorum_msg)): ?>
                <div class="fav-msg"><?= htmlspecialchars($yorum_msg) ?></div>
            <?php endif; ?>
            <?php if (empty($yorumlar)): ?>
                <div class="comment">Henüz yorum yok.</div>
            <?php else: ?>
                <?php foreach ($yorumlar as $yorum): ?>
                    <div class="comment"><b><?= htmlspecialchars($yorum['username']) ?>:</b> <?= nl2br(htmlspecialchars($yorum['comment_text'])) ?>
                        <span style="color:#999; font-size:0.95em;">(<?= date('d.m.Y H:i', strtotime($yorum['created_at'])) ?>)</span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form class="comment-form" method="post" action="">
                    <textarea name="yorum" placeholder="Yorumunuzu yazın..." required></textarea>
                    <button type="submit">Gönder</button>
                </form>
            <?php else: ?>
                <div style="margin-top:16px;">Yorum yapabilmek için <a href="login.php">giriş yapın</a>.</div>
            <?php endif; ?>
        </div>
    </main>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var stops = <?= json_encode(array_column($stops, 'stop_name')); ?>;
        var stopCoords = <?= json_encode($stopCoords); ?>;
        var map = L.map('map').setView(stopCoords[0], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19}).addTo(map);
        var routeLine = [];
        for (var i = 0; i < stopCoords.length; i++) {
            L.marker(stopCoords[i]).addTo(map).bindPopup(stops[i]);
            routeLine.push(stopCoords[i]);
        }
        L.polyline(routeLine, {color: '#185a9d', weight: 5}).addTo(map);
        var currentIndex = <?= (int)$currentIndex ?>;
        L.marker(stopCoords[currentIndex], {icon: L.icon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/61/61231.png', iconSize:[32,32]})})
            .addTo(map)
            .bindPopup("Şu an buradasınız!<br><b>"+stops[currentIndex]+"</b>")
            .openPopup();

        // --- Canlı kalan süre ve varış saatini güncelle ---
        function pad(n) { return n < 10 ? "0"+n : n; }

        function updateArrivalTimes() {
            // Her durak için kalan süreyi güncelle
            document.querySelectorAll('.minutes[data-arrival]').forEach(function(cell) {
                var arrivalTime = cell.getAttribute('data-arrival');
                if(!arrivalTime) { cell.textContent = "Bekleniyor"; return; }

                var now = new Date();
                var arr = arrivalTime.split(':');
                var arrival = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(arr[0]), parseInt(arr[1]), parseInt(arr[2]));

                // Gece yarısı sonrası için düzeltme
                if(arrival < now && (now.getHours() - arrival.getHours()) > 6) {
                    arrival.setDate(arrival.getDate() + 1);
                }

                var diffMs = arrival - now;
                var diffMin = Math.round(diffMs / 60000);

                if(diffMin <= 0) {
                    cell.textContent = "Geldi";
                } else if(diffMin > 300) { // 5 saatten fazlaysa mantıksız
                    cell.textContent = "Bekleniyor";
                } else {
                    cell.textContent = diffMin + " dk";
                }
            });

            // Tüm varış saatlerini canlı güncelle
            document.querySelectorAll('.arrival-time[data-arrival]').forEach(function(cell) {
                var arrivalTime = cell.getAttribute('data-arrival');
                if(!arrivalTime) { cell.textContent = "Bekleniyor"; return; }
                var arr = arrivalTime.split(':');
                var now = new Date();
                var arrival = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(arr[0]), parseInt(arr[1]), parseInt(arr[2]));
                if(arrival < now && (now.getHours() - arrival.getHours()) > 6) {
                    arrival.setDate(arrival.getDate() + 1);
                }
                cell.textContent = pad(arrival.getHours()) + ":" + pad(arrival.getMinutes());
            });

            // Üstteki "sonraki durak tahmini varış" için
            var nextArrival = document.getElementById('next-arrival-time');
            var nextMinutes = document.getElementById('next-minutes-left');
            if(nextArrival && nextMinutes) {
                var arrivalTime = nextArrival.getAttribute('data-arrival');
                if(!arrivalTime) { nextMinutes.textContent = "Bekleniyor"; nextArrival.textContent = "Bekleniyor"; return; }
                var now = new Date();
                var arr = arrivalTime.split(':');
                var arrival = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(arr[0]), parseInt(arr[1]), parseInt(arr[2]));
                if(arrival < now && (now.getHours() - arrival.getHours()) > 6) {
                    arrival.setDate(arrival.getDate() + 1);
                }
                var diffMs = arrival - now;
                var diffMin = Math.round(diffMs / 60000);

                if(diffMin <= 0) {
                    nextMinutes.textContent = "Geldi";
                } else if(diffMin >= 60) { // 60 ve üstü ise Bekleniyor yazacak
                    nextMinutes.textContent = "Bekleniyor";
                } else {
                    nextMinutes.textContent = diffMin + " dk";
                }
                nextArrival.textContent = pad(arrival.getHours()) + ":" + pad(arrival.getMinutes());
            }
        }

        updateArrivalTimes();
        setInterval(updateArrivalTimes, 10000);
        document.addEventListener("DOMContentLoaded", updateArrivalTimes);
    </script>
</body>
</html>