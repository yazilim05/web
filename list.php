<?php
include("db.php");

// Otobüs silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: list.php?message=deleted");
    exit;
}

// Tüm otobüs bilgilerini getir
// created_by sütunu olmadığı için kullanıcı bilgisi için join kaldırıldı!
$stmt = $pdo->query("SELECT * FROM schedules ORDER BY bus_number ASC");
$buses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otobüs Listele ve Sil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #c6ffdd 0%, #fbd786 50%, #f7797d 100%);
            min-height: 100vh;
        }
        .custom-card {
            max-width: 1100px;
            margin: 4rem auto 0 auto;
            box-shadow: 0 6px 32px rgba(0,0,0,0.13);
            border-radius: 2rem;
            border: none;
        }
        .custom-header {
            background: linear-gradient(90deg, #ee0979 0%, #ff6a00 100%);
            color: #fff;
            border-radius: 2rem 2rem 0 0;
            padding: 2.2rem 1.5rem 1.5rem 1.5rem;
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .table thead {
            background: linear-gradient(90deg, #ffaf7b 0%, #d76d77 100%);
            color: #fff;
            font-size: 1.07em;
        }
        .btn-danger {
            background: linear-gradient(90deg, #ff512f 0%, #dd2476 100%);
            border: none;
            font-weight: bold;
        }
        .btn-danger:hover {
            background: linear-gradient(90deg, #dd2476 0%, #ff512f 100%);
        }
        .btn-secondary {
            background: #fff;
            color: #d76d77;
            border: 2px solid #d76d77;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .btn-secondary:hover {
            background: #ffaf7b;
            color: #fff;
            border: 2px solid #ffaf7b;
        }
        .alert-danger {
            background: linear-gradient(90deg, #ff512f 0%, #dd2476 100%);
            color: #fff;
            border: none;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card custom-card">
            <div class="custom-header">
                Otobüs Listele ve Sil
            </div>
            <div class="card-body p-4">
                <!-- Mesaj Göster -->
                <?php if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
                    <div class="alert alert-danger mb-4">Otobüs bilgisi başarıyla silindi!</div>
                    <script>
                        if (window.location.search.includes('message=deleted')) {
                            window.history.replaceState({}, document.title, window.location.pathname);
                        }
                    </script>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mt-2 rounded-3 overflow-hidden" style="background: #fff; border-radius: 1rem;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Otobüs Numarası</th>
                                <th>Güzergah</th>
                                <th>Çoklu Sefer Saatleri</th>
                                <th>Tekil Sefer Saati</th>
                                <!--<th>Ekleyen Kullanıcı</th>-->
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($buses as $bus): ?>
                                <tr>
                                    <td><?= htmlspecialchars($bus['id']) ?></td>
                                    <td><?= htmlspecialchars($bus['bus_number']) ?></td>
                                    <td><?= htmlspecialchars($bus['route']) ?></td>
                                    <td><?= htmlspecialchars($bus['departure_times']) ?></td>
                                    <td><?= htmlspecialchars($bus['departure_time']) ?></td>
                                    <!--<td><?= htmlspecialchars($bus['created_by_name'] ?? 'Bilinmiyor') ?></td>-->
                                    <td>
                                        <form method="POST" action="" style="display: inline-block;">
                                            <input type="hidden" name="id" value="<?= $bus['id'] ?>">
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?')">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($buses)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-secondary">Kayıtlı otobüs bulunamadı.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <a href="admin_panel.php" class="btn btn-secondary w-100 mt-3">Geri Dön</a>
            </div>
        </div>
    </div>
</body>
</html>