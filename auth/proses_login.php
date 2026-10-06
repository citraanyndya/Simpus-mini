<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$batasPercobaan = 3;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [];
}

$percobaanSaatIni = $_SESSION['login_attempts'][$username] ?? 0;

if ($percobaanSaatIni >= $batasPercobaan) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Terlalu banyak percobaan gagal. Coba lagi nanti.'
    ];

    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    unset($_SESSION['login_attempts'][$username]);

    if (isset($_POST['remember'])) {
    setcookie('remember_user_id', $user['id'], time() + 30 * 24 * 60 * 60, '/');
    }

    header('Location: ../index.php');
    exit;
}

$_SESSION['login_attempts'][$username] =
    ($_SESSION['login_attempts'][$username] ?? 0) + 1;

$_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah. Percobaan ke-' .
        $_SESSION['login_attempts'][$username] . ' dari ' . $batasPercobaan . '.'
];
header('Location: login.php');
exit;