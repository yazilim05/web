<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];

// Favori kaldırma işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fav_id'])) {
    $favId = $_POST['fav_id'];
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $favId, ':user_id' => $userId]);
    $deleted = true;
}

// Favori seferleri çek
$stmt = $pdo->prepare("SELECT f.id as fav_id, s.bus_number, s.route, s.departure_times, s.departure_time
                       FROM favorites f
                       JOIN schedules s ON f.schedule_id = s.id
                       WHERE f.user_id = :user_id
                       ORDER BY f.added_at DESC");
$stmt->execute([':user_id' => $userId]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Favori Seferlerim</title>
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
        .main-card {
            max-width: 1050px;
            margin: 120px auto 40px auto;
            box-shadow: 0 8px 32px rgba(0,0,0,0.14);
            border-radius: 2rem;
            border: none;
            background: #fff9;
            padding: 0 0 24px 0;
        }
        .main-header {
            background: linear-gradient(90deg, #ffd200 0%, #43cea2 100%);
            color: #473a7c;
            border-radius: 2rem 2rem 0 0;
            padding: 2.1rem 1rem 1.2rem 1rem;
            text-align: center;
            font-size: 2.1rem;
            font-weight: bold;
            letter-spacing: 2px;
            box-shadow: 0 4px 18px #8881;
        }
        .btn-danger {
            background: linear-gradient(90deg, #ff512f 0%, #dd2476 100%);
            border: none;
            font-weight: bold;
            color: #fff;
            border-radius: 14px;
            font-size: 1.05rem;
            box-shadow: 0 1px 8px #ff512f44;
            transition: background 0.2s;
        }
        .btn-danger:hover {
            background: linear-gradient(90deg, #dd2476 0%, #ff512f 100%);
            color: #fff;
        }
        .btn-secondary, .btn-secondary:visited {
            font-weight: bold;
            letter-spacing: 1px;
            background: linear-gradient(90deg, #2193b0 0%, #6dd5ed 100%);
            border: none;
            color: #fff;
            border-radius: 15px;
            font-size: 1.11rem;
            box-shadow: 0 1px 10px #185a9d22;
        }
        .btn-secondary:hover {
            background: linear-gradient(90deg, #6dd5ed 0%, #2193b0 100%);
            color: #fff;
        }
        .alert-success {
            background: linear-gradient(90deg, #43cea2 0%, #ffd200 100%);
            color: #473a7c;
            border: none;
            font-weight: bold;
            text-align: center;
            border-radius: 8px;
            font-size: 1.06rem;
        }
        .alert-warning {
            background: linear-gradient(90deg, #ffaf7b 0%, #d76d77 100%);
            color: #fff;
            border: none;
            font-weight: bold;
            text-align: center;
            border-radius: 8px;
            font-size: 1.08rem;
        }
        .table thead {
            background: linear-gradient(90deg, #ffaf7b 0%, #d76d77 100%);
            color: #fff;
            font-size: 1.07rem;
        }
        .table tbody tr {
            background: #fffbe7;
        }
        .table tbody tr:nth-child(even) {
            background: #fff3e1;
        }
        .table th, .table td {
            vertical-align: middle !important;
        }
        @media (max-width: 1100px) {
            .main-card { max-width: 98vw; }
            .table th, .table td { font-size: 0.95rem; }
        }
        @media (max-width: 700px) {
            .main-card { margin: 80px auto 10px auto; }
            .main-header { font-size: 1.2rem; padding: 1.1rem 0.4rem 1rem 0.4rem;}
            .bus-header { font-size: 1.3rem; padding: 16px 0 13px 0;}
            .table th, .table td { font-size: 0.89rem; }
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
        AmasyaBus <span style="font-size:1.08rem; font-weight:400;">| Favori Seferlerim</span>
    </div>
    <div class="container">
        <div class="card main-card">
            <div class="main-header">
                <span style="font-size:1.3rem;">⭐</span> Favori Otobüs Seferlerim
            </div>
            <div class="card-body p-4">
                <?php if (!empty($deleted)): ?>
                    <div class="alert alert-success mb-4">Favori sefer başarıyla kaldırıldı!</div>
                <?php endif; ?>
                <?php if (empty($favorites)): ?>
                    <div class="alert alert-warning text-center mb-4">Henüz favori seferiniz yok.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle rounded-3 overflow-hidden">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Otobüs</th>
                                    <th>Otobüs No</th>
                                    <th>Güzergah</th>
                                    <th>Çoklu Sefer Saatleri</th>
                                    <th>Tekil Sefer Saati</th>
                                    <th>Favoriden Kaldır</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($favorites as $i => $fav): ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td>
                                          <span style="display:inline-block;vertical-align:middle;">
                                            <svg width="50" height="30" viewBox="0 0 56 34" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                        </td>
                                        <td><?= htmlspecialchars($fav['bus_number']) ?></td>
                                        <td><?= htmlspecialchars($fav['route']) ?></td>
                                        <td><?= htmlspecialchars($fav['departure_times']) ?></td>
                                        <td><?= htmlspecialchars($fav['departure_time']) ?></td>
                                        <td>
                                            <form method="POST" action="" style="display:inline;">
                                                <input type="hidden" name="fav_id" value="<?= $fav['fav_id'] ?>">
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Favoriden kaldırmak istediğinize emin misiniz?')">Kaldır</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="row mt-4">
                    <div class="col-12 mb-2">
                        <a href="anasayfa.php" class="btn btn-secondary w-100"><span style="font-size:1.2rem;">🏠</span> Ana Sayfa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>