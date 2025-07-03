<?php
include("db.php");
session_start();

// Güzergahları çek
$routes = $pdo->query("SELECT route_id, route_number, route_name FROM routes")->fetchAll(PDO::FETCH_ASSOC);

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busNumber = $_POST['bus_number'] ?? '';
    $routeId = $_POST['route_id'] ?? '';
    $departureTimes = $_POST['departure_times'] ?? '';
    $departureTime = $_POST['departure_time'] ?? '';

    // Seçilen route_id'ye uygun güzergah adını bul
    $routeValue = '';
    foreach ($routes as $route) {
        if ($route['route_id'] == $routeId) {
            $routeValue = $route['route_number'] . ' - ' . $route['route_name'];
            break;
        }
    }

    if ($busNumber === '' || $routeId === '' || $departureTimes === '' || $departureTime === '' || $routeValue === '') {
        $error = "Tüm alanları doldurunuz!";
    } else {
        try {
            // SADECE VAR OLAN ALANLARI EKLE!
            $stmt = $pdo->prepare("INSERT INTO schedules (bus_number, route_id, route, departure_times, departure_time) VALUES (:bus_number, :route_id, :route, :departure_times, :departure_time)");
            $stmt->execute([
                ':bus_number' => $busNumber,
                ':route_id' => $routeId,
                ':route' => $routeValue,
                ':departure_times' => $departureTimes,
                ':departure_time' => $departureTime
            ]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}

// Başarı parametresi varsa mesajı göster
$success = isset($_GET['success']) ? "Kayıt başarıyla eklendi!" : "";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otobüs Ekle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%);
            min-height: 100vh;
        }
        .custom-card {
            max-width: 480px;
            margin: 3rem auto;
            box-shadow: 0 6px 32px rgba(0,0,0,0.18);
            border-radius: 20px;
            border: none;
        }
        .form-label {
            color: #0d6efd;
            font-weight: 600;
        }
        .custom-header {
            background: linear-gradient(90deg,#0d6efd 0%, #70e1f5 100%);
            color: white;
            border-radius: 18px 18px 0 0;
            padding: 2rem 1rem 1.3rem 1rem;
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            border: none;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
        }
        a.btn-secondary {
            margin-top: 1rem;
            background: #fff;
            color: #185a9d;
            border: 2px solid #185a9d;
            font-weight: bold;
            letter-spacing: 1px;
        }
        a.btn-secondary:hover {
            background: #43cea2;
            color: #fff;
            border: 2px solid #43cea2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card custom-card">
            <div class="custom-header">
                Otobüs Ekle
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <script>
                        if (window.location.search.includes('success=1')) {
                            window.history.replaceState({}, document.title, window.location.pathname);
                        }
                    </script>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="busNumber" class="form-label">Otobüs Numarası</label>
                        <input type="number" name="bus_number" class="form-control" id="busNumber" placeholder="Otobüs numarasını giriniz" required>
                    </div>
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Güzergah</label>
                        <select name="route_id" id="route_id" class="form-control" required>
                            <option value="">Güzergah seçiniz</option>
                            <?php foreach($routes as $route): ?>
                                <option value="<?= $route['route_id'] ?>">
                                    <?= htmlspecialchars($route['route_number']) ?> - <?= htmlspecialchars($route['route_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="departure_times" class="form-label">Çoklu Sefer Saatleri <span class="text-secondary" style="font-size:0.95em;">(Virgül ile ayırın)</span></label>
                        <input type="text" name="departure_times" class="form-control" id="departure_times" placeholder="Örn: 08:00,10:00,12:00" required>
                    </div>
                    <div class="mb-3">
                        <label for="departure_time" class="form-label">Tekil Sefer Saati</label>
                        <input type="text" name="departure_time" class="form-control" id="departure_time" placeholder="Örn: 09:00" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ekle</button>
                </form>
                <a href="admin_panel.php" class="btn btn-secondary w-100">Geri Dön</a>
            </div>
        </div>
    </div>
</body>
</html>