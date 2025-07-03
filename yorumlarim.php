<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];

// Yorum silme işlemi
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['sil_id'])) {
    $sil_id = intval($_POST['sil_id']);
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->execute([$sil_id, $userId]);
    header("Location: yorumlarim.php?silindi=1");
    exit;
}

// Kullanıcının yaptığı yorumları ve ilgili sefer bilgisini çek
$stmt = $pdo->prepare(
    "SELECT c.id, c.comment_text, c.created_at, s.route, s.bus_number
     FROM comments c
     JOIN schedules s ON c.schedule_id = s.id
     WHERE c.user_id = ?
     ORDER BY c.created_at DESC"
);
$stmt->execute([$userId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yorumlarım</title>
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
            font-size: 2.5rem;
            font-family: 'Montserrat', Arial, sans-serif;
            letter-spacing: 2px;
            font-weight: bold;
            margin-bottom: 0;
            position: relative;
        }
        .bus-header .bus-icon {
            margin-right: 18px;
            vertical-align: middle;
            display: inline-block;
            filter: drop-shadow(0 2px 6px #fff6);
        }
        .bus-header .bus-icon svg {
            width: 60px;
            height: 36px;
        }
        .main-card {
            max-width: 1000px;
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
        .comment-list-area {
            margin-top: 0;
        }
        .comment-card {
            background: linear-gradient(90deg, #fbc2eb 0%, #a18cd1 100%);
            border-radius: 1.2rem;
            margin-bottom: 1.7rem;
            box-shadow: 0 2px 16px #a18cd133;
            padding: 1.1rem 1.4rem 1.1rem 2.2rem;
            position: relative;
            border-left: 14px solid #43cea2;
            transition: transform 0.13s;
        }
        .comment-card:hover {
            transform: scale(1.01) translateY(-2px);
            box-shadow: 0 8px 32px #a18cd155;
        }
        .comment-meta {
            color: #185a9d;
            font-size: 1.07rem;
            margin-bottom: 0.25rem;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .comment-date {
            color: #ffd200;
            background: #473a7c;
            font-size: 0.97rem;
            margin-left: 10px;
            padding: 2px 10px 3px 10px;
            border-radius: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        .comment-text {
            font-size: 1.19rem;
            color: #473a7c;
            margin-bottom: 0.2rem;
            margin-top: 0.2rem;
            word-break: break-word;
            font-weight: bold;
            letter-spacing: 0.2px;
            text-shadow: 1px 1px 0 #fff6;
        }
        .alert-warning {
            background: linear-gradient(90deg, #ffaf7b 0%, #d76d77 100%);
            color: #fff;
            border: none;
            font-weight: bold;
            text-align: center;
            border-radius: 8px;
            font-size: 1.1rem;
        }
        .alert-success {
            background: linear-gradient(90deg, #43cea2 0%, #ffd200 100%);
            color: #473a7c;
            border: none;
            font-weight: bold;
            text-align: center;
            border-radius: 8px;
            font-size: 1.08rem;
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
        .btn-delete {
            position: absolute;
            top: 16px;
            right: 26px;
            background: linear-gradient(90deg, #ff5858 5%, #fbc2eb 100%);
            color: #fff;
            border: none;
            border-radius: 22px;
            padding: 2px 18px 4px 18px;
            font-size: 1.06rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px #d5336944;
        }
        .btn-delete:hover {
            background: linear-gradient(90deg, #d53369 0%, #ffd200 100%);
            color: #fff;
        }
        .bus-art {
            width: 65px;
            height: 38px;
            position: absolute;
            left: -80px;
            top: 50%;
            transform: translateY(-50%) rotate(-4deg);
            display: none;
        }
        @media (min-width: 650px) {
            .comment-card .bus-art { display: block; }
        }
        @media (max-width: 1000px) {
            .main-card { max-width: 98vw; }
        }
        @media (max-width: 600px) {
            .main-card { margin: 80px auto 10px auto; }
            .main-header { font-size: 1.2rem; padding: 1.1rem 0.4rem 1rem 0.4rem;}
            .comment-card {
                padding: 0.7rem 0.5rem 0.7rem 1.2rem;
                border-left-width: 8px;
            }
            .btn-delete { top: 8px; right: 12px; font-size: 0.95rem; padding: 1.5px 10px 2px 10px;}
            .bus-header { font-size: 1.6rem; padding: 16px 0 13px 0;}
            .bus-header .bus-icon svg { width: 38px !important; height: 18px !important; }
            .bus-art { width: 40px; height: 24px; left: -50px; }
        }
    </style>
</head>
<body>
    <div class="bus-header">
        <span class="bus-icon">
          <svg width="60" height="36" viewBox="0 0 56 34" fill="none" xmlns="http://www.w3.org/2000/svg">
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
        AmasyaBus <span style="font-size:1.1rem; font-weight:400;">| Yorumlarım</span>
    </div>
    <div class="container">
        <div class="card main-card">
            <div class="main-header">
                <span style="font-size:1.5rem;">📝</span>
                Otobüs Yorumlarım
            </div>
            <div class="card-body p-4 comment-list-area">
                <?php if (isset($_GET['silindi'])): ?>
                    <div class="alert alert-success mb-4">Yorum başarıyla silindi.</div>
                <?php endif; ?>
                <?php if (empty($comments)): ?>
                    <div class="alert alert-warning mb-4">Henüz hiç yorum yapmadınız.</div>
                <?php else: ?>
                    <?php foreach ($comments as $c): ?>
                        <div class="comment-card">
                            <span class="bus-art">
                              <svg width="65" height="38" viewBox="0 0 56 34" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                            <div class="comment-meta">
                                <?= htmlspecialchars($c['route']) ?>
                                (Otobüs: <?= htmlspecialchars($c['bus_number']) ?>)
                                <span class="comment-date"><?= htmlspecialchars(date("d.m.Y H:i", strtotime($c['created_at']))) ?></span>
                            </div>
                            <div class="comment-text"><?= nl2br(htmlspecialchars($c['comment_text'])) ?></div>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="sil_id" value="<?= htmlspecialchars($c['id']) ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Yorumu silmek istediğinize emin misiniz?');">Sil</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
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