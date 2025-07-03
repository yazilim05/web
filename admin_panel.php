<?php
// Admin Paneli Ana Sayfa
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #F7971E 0%, #FFD200 100%);
            min-height: 100vh;
        }
        .custom-card {
            max-width: 450px;
            margin: 4rem auto 0 auto;
            box-shadow: 0 6px 32px rgba(0,0,0,0.13);
            border-radius: 2rem;
            border: none;
        }
        .custom-header {
            background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            border-radius: 2rem 2rem 0 0;
            padding: 2.5rem 1.5rem 1.7rem 1.5rem;
            text-align: center;
            font-size: 2.2rem;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .custom-btn {
            font-weight: bold;
            font-size: 1.2rem;
            letter-spacing: 1px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .btn-primary {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
        }
        .btn-secondary {
            background: #fff;
            color: #185a9d;
            border: 2px solid #185a9d;
        }
        .btn-secondary:hover {
            background: #43cea2;
            color: #fff;
            border: 2px solid #43cea2;
        }
        .btn-danger {
            background: linear-gradient(90deg, #ff512f 0%, #dd2476 100%);
            border: none;
            font-weight: bold;
        }
        .btn-danger:hover {
            background: linear-gradient(90deg, #dd2476 0%, #ff512f 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card custom-card">
            <div class="custom-header">
                Admin Paneli
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3 align-items-center">
                    <a href="add.php" class="btn btn-primary custom-btn w-100">Otobüs Ekle</a>
                    <a href="list.php" class="btn btn-secondary custom-btn w-100">Otobüsleri Listele ve Sil</a>
                    <a href="logout.php" class="btn btn-danger custom-btn w-100">Çıkış Yap</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>