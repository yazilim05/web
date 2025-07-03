<?php
// --- Veritabanı bağlantısı ---
include("db.php");

// --- Filtre Değişkenleri ---
$bus_number = $_GET['bus_number'] ?? '';
$day_type = $_GET['day_type'] ?? '';

// --- Sorgu ---
$sql = "SELECT * FROM schedules WHERE 1=1";
$params = [];
if ($bus_number != '') {
    $sql .= " AND bus_number = :bus_number";
    $params[':bus_number'] = $bus_number;
}
if ($day_type != '') {
    $sql .= " AND day_type = :day_type";
    $params[':day_type'] = $day_type;
}
$sql .= " ORDER BY bus_number, day_type, departure_time ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Otobüs Saatleri Filtrele</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            margin: 0;
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
        .menu li a:hover {
            background: #fff;
            color: #185a9d;
        }

        main {
            width: 900px;
            max-width: 98vw;
            margin: 60px 0 0 40px;
            padding: 0;
            box-shadow: none;
        }
        h2 {
            font-size: 2rem;
            color: #473a7c;
            margin-bottom: 28px;
            text-align: left;
            text-shadow: 1px 1px 3px #fff3;
        }
        .filtre-form {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 20px;
        }
        .filtre-form label {
            font-size: 1.1rem;
            color: #333;
        }
        .filtre-form select {
            padding: 6px 12px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #a18cd1;
            margin-left: 7px;
        }
        .filtre-form button {
            background: linear-gradient(90deg, #43cea2, #185a9d);
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 20px;
            padding: 10px 28px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s, box-shadow 0.3s;
            box-shadow: 0 2px 10px #a18cd133;
        }
        .filtre-form button:hover {
            background: linear-gradient(90deg, #185a9d, #43cea2);
            color: #fff;
            box-shadow: 0 4px 20px #a18cd155;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 14px;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 14px #0001;
            margin-left: 0;
        }
        th, td {
            border: 1px solid #e4e4e4;
            padding: 10px;
            text-align: left;
            font-size: 1rem;
            vertical-align: top;
        }
        th {
            background: linear-gradient(90deg, #43cea2 30%, #a18cd1 100%);
            color: #fff;
            font-size: 1.1rem;
            font-weight: bold;
        }
        tr:nth-child(even) td {
            background: #f5f8ffbb;
        }
        .saatler {
            max-width: 300px;
            overflow-x: auto;
            white-space: nowrap;
            word-break: keep-all;
            background: #f3f7ff;
            border-radius: 8px;
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 1.03rem;
            font-weight: bold;
            color: #000;
            padding: 7px 5px 7px 8px;
            box-sizing: border-box;
        }
        .saatler::-webkit-scrollbar {
            height: 7px;
            background: #e4e4e4;
        }
        .saatler::-webkit-scrollbar-thumb {
            background: #a18cd1;
            border-radius: 6px;
        }
        @media (max-width: 900px) {
            main { width: 98vw; margin: 30px 0 0 1vw; padding: 0; }
            .filtre-form { flex-direction: column; align-items: flex-start; gap: 10px;}
        }
        @media (max-width: 600px) {
            th, td { font-size: 0.98rem; }
            h2 { font-size: 1.3rem;}
            .menu .logo { font-size: 1.3rem;}
            .menu { padding: 0 8px;}
            .saatler { max-width: 120px; font-size: 0.88rem;}
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
        <h2>Otobüs Saatleri</h2>
        <form class="filtre-form" method="get" action="">
            <label>Hat Numarası:
                <select name="bus_number">
                    <option value="">Hepsi</option>
                    <option value="1" <?= $bus_number=='1'?'selected':''; ?>>1</option>
                    <option value="2" <?= $bus_number=='2'?'selected':''; ?>>2</option>
                    <option value="3" <?= $bus_number=='3'?'selected':''; ?>>3</option>
                    <option value="6" <?= $bus_number=='6'?'selected':''; ?>>6</option>
                    <option value="7" <?= $bus_number=='7'?'selected':''; ?>>7</option>
                    <option value="8" <?= $bus_number=='8'?'selected':''; ?>>8</option>
                    <option value="9" <?= $bus_number=='9'?'selected':''; ?>>9</option>
                    <option value="11" <?= $bus_number=='11'?'selected':''; ?>>11</option>
                    <option value="12" <?= $bus_number=='12'?'selected':''; ?>>12</option>
                    <option value="17" <?= $bus_number=='17'?'selected':''; ?>>17</option>
                    <option value="BELMEBÜK" <?= $bus_number=='BELMEBÜK'?'selected':''; ?>>BELMEBÜK</option>
                </select>
            </label>
            <label>Gün:
                <select name="day_type">
                    <option value="">Hepsi</option>
                    <option value="Hafta İçi" <?= $day_type=='Hafta İçi'?'selected':''; ?>>Hafta İçi</option>
                    <option value="Cumartesi" <?= $day_type=='Cumartesi'?'selected':''; ?>>Cumartesi</option>
                    <option value="Pazar" <?= $day_type=='Pazar'?'selected':''; ?>>Pazar</option>
                    <option value="Hafta Sonu" <?= $day_type=='Hafta Sonu'?'selected':''; ?>>Hafta Sonu</option>
                </select>
            </label>
            <button type="submit">Filtrele</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Hat No</th>
                    <th>Güzergah</th>
                    <th>Gün</th>
                    <th>Saatler</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($rows && count($rows) > 0): ?>
                <?php foreach($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['bus_number']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['route'])) ?></td>
                        <td><?= htmlspecialchars($row['day_type']) ?></td>
                        <td>
                            <div class="saatler" title="<?= htmlspecialchars($row['departure_times']) ?>">
                                <?= htmlspecialchars($row['departure_times']) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Kayıt bulunamadı.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>