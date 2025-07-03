<?php
date_default_timezone_set('Europe/Istanbul');
include("db.php");

$sql = "SELECT s.id AS schedule_id, r.route_number, r.route_name, r.description, s.day_type, s.departure_time
        FROM schedules s
        JOIN routes r ON s.route_id = r.route_id
        ORDER BY r.route_number, s.day_type, s.departure_time"; //store procedure
$stmt = $pdo->prepare($sql);
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getStopsForSchedule($pdo, $schedule_id) {
    $stmt = $pdo->prepare("CALL GetStopsForSchedule(?)");
    $stmt->execute([$schedule_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    do { $stmt->nextRowset(); } while ($stmt->columnCount());
    return $result;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Canlı Sefer Kartları</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); min-height: 100vh; }
        .menu { display: flex; justify-content: space-between; align-items: center; background: linear-gradient(90deg, #43cea2, #185a9d); padding: 0 40px; height: 60px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .menu .logo { color: #fff; font-size: 2rem; font-weight: bold; letter-spacing: 2px; }
        .menu ul { list-style: none; display: flex; gap: 30px; margin: 0; padding: 0; }
        .menu li a { color: #fff; text-decoration: none; font-size: 1.1rem; padding: 8px 16px; border-radius: 20px; transition: background 0.3s, color 0.3s; }
        .menu li a:hover { background: #fff; color: #185a9d; }
        main { text-align: center; margin-top: 40px; }
        h1 { font-size: 2.5rem; color: #473a7c; margin-bottom: 30px; text-shadow: 1px 1px 3px #fff3; }
        .bus-cards { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-bottom: 30px; }
        .bus-link { text-decoration: none; color: inherit; }
        .bus-card { background: #fff; border-radius: 20px; box-shadow: 0 4px 24px #0001; width: 350px; min-height: 170px; display: flex; flex-direction: column; align-items: center; font-size: 1.1rem; color: #333; font-weight: bold; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; padding: 16px 18px 16px 18px; margin-bottom: 8px; position: relative; }
        .bus-card .bus-route { font-size: 1.08rem; color: #473a7c; margin-bottom: 4px; font-weight: bold; letter-spacing: 1px; margin-top: 0; }
        .bus-card .bus-info { font-size: 0.97rem; color: #444; margin-bottom: 0; margin-top: 0; }
        .bus-card .stops-table { width: 98%; margin-top: 10px; border-collapse: collapse; }
        .bus-card .stops-table th, .bus-card .stops-table td { padding: 4px 6px; border-bottom: 1px solid #eee; font-size: 0.98rem; }
        .bus-card .stops-table th { background: #f3e8fd; color: #473a7c; }
        .bus-card .stops-table tr:last-child td { border-bottom: none; }
        .bus-card .minutes { font-size: 1.07rem; color: #e65100; font-weight: bold; }
        .bus-card .closest-marker { color: #2193b0; font-size: 0.95rem; margin-left: 8px; font-weight: bold; }
        .bus-card:hover { transform: translateY(-3px) scale(1.04); box-shadow: 0 8px 32px #0002; }
        @media (max-width: 700px) {
            .bus-cards { flex-direction: column; align-items: center; }
            .bus-card { width: 95vw; max-width: 390px; min-height: 120px; }
            main { margin-top: 24px; }
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
            <li><a href="iletisim.php">İletişim</a></li>
        </ul>
    </nav>
    <main>
        <h1>Canlı Seferler</h1>
        <div class="bus-cards">
        <?php foreach($schedules as $s): 
            $stops = getStopsForSchedule($pdo, $s['schedule_id']);
        ?>
            <a class="bus-link" href="sefer_detay.php?schedule_id=<?= urlencode($s['schedule_id']) ?>">
            <div class="bus-card">
                <div class="bus-route"><?= htmlspecialchars($s['route_number'] . " - " . $s['route_name']) ?></div>
                <div class="bus-info">Gün türü: <b><?= htmlspecialchars($s['day_type']) ?></b></div>
                <div class="bus-info">Kalkış saati: <b><?= htmlspecialchars(substr($s['departure_time'], 0, 5)) ?></b></div>
                <table class="stops-table">
                    <tr>
                        <th>#</th>
                        <th>Durak</th>
                        <th>Tahmini varış</th>
                        <th>Kalan süre</th>
                    </tr>
                    <?php foreach($stops as $durak): ?>
                        <tr>
                            <td><?= $durak['stop_order'] ?></td>
                            <td><?= htmlspecialchars($durak['stop_name']) ?></td>
                            <td class="arrival-time" data-arrival="<?= htmlspecialchars($durak['arrival_time']) ?>">
                                <?= htmlspecialchars(substr($durak['arrival_time'],0,5)) ?>
                            </td>
                            <td class="minutes" data-arrival="<?= htmlspecialchars($durak['arrival_time']) ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div class="closest-marker" style="display:none;"></div>
            </div>
            </a>
        <?php endforeach; ?>
        </div>
    </main>
    <script>
    function pad(n) { return n < 10 ? "0"+n : n; }

    function updateArrivalTimes() {
        var MAX_MINUTES = 60; // 60 dk üstü için "Bekleniyor"
        document.querySelectorAll('.bus-card').forEach(function(card) {
            let minsArr = [];
            let minObj = {diff: Infinity, cell: null};
            card.querySelectorAll('.minutes[data-arrival]').forEach(function(cell) {
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
                } else if(diffMin > MAX_MINUTES) {
                    cell.textContent = "Bekleniyor";
                } else {
                    cell.textContent = diffMin + " dk";
                    minsArr.push({diff: diffMin, cell: cell});
                    if(diffMin < minObj.diff && diffMin > 0) {
                        minObj = {diff: diffMin, cell: cell};
                    }
                }
            });

            card.querySelectorAll('.closest-marker').forEach(function(el){ el.style.display = "none"; });

            if(minObj.cell && minObj.diff <= MAX_MINUTES) {
                var row = minObj.cell.parentElement;
                var marker = card.querySelector('.closest-marker');
                if (!marker) {
                    marker = document.createElement('div');
                    marker.className = 'closest-marker';
                    minObj.cell.parentElement.appendChild(marker);
                }
                marker.style.display = "inline";
                marker.textContent = "En Yakın!";
                minObj.cell.style.color = "#2193b0";
                card.querySelectorAll('.minutes[data-arrival]').forEach(function(cell){
                    if(cell !== minObj.cell) cell.style.color = "#e65100";
                });
            }
        });

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
    }

    updateArrivalTimes();
    setInterval(updateArrivalTimes, 10000);
    document.addEventListener("DOMContentLoaded", updateArrivalTimes);
    </script>
</body>
</html>