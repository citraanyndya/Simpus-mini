<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$anggotaId = (int) ($_POST['anggota_id'] ?? $_POST['anggota_id'] ?? 0);
$bukuId    = (int) ($_POST['buku_id'] ?? $_POST['buku_id'] ?? 0);

if ($anggotaId <= 0 || $bukuId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Anggota dan buku wajib dipilih.'];
    header('Location: tambah.php');
    exit;
}

try {
    $stmt_cek = $pdo->prepare("
        SELECT COUNT(*) 
        FROM peminjaman 
        WHERE anggota_id = :anggota_id 
          AND tanggal_kembali IS NULL 
          AND (CURRENT_DATE - tanggal_pinjam) > 14
    ");
    $stmt_cek->execute(['anggota_id' => $anggotaId]);
    if ($stmt_cek->fetchColumn() > 0) {
        $_SESSION['flash'] = [
            'type' => 'error', 
            'pesan' => 'Gagal meminjam: Anggota ini memiliki pinjaman yang terlambat lebih dari 14 hari!'
        ];
        header('Location: tambah.php');
        exit;
    }

    $pdo->beginTransaction();

    $cek = $pdo->prepare("SELECT stok FROM buku WHERE id = :id FOR UPDATE");
    $cek->execute(['id' => $bukuId]);
    $buku = $cek->fetch(PDO::FETCH_ASSOC);

    if (!$buku || $buku['stok'] < 1) {
        throw new Exception('Stok buku tidak tersedia.');
    }

    $insert = $pdo->prepare("
        INSERT INTO peminjaman (buku_id, anggota_id, tanggal_pinjam, tanggal_jatuh_tempo)
        VALUES (:buku_id, :anggota_id, CURRENT_DATE, CURRENT_DATE + INTERVAL '14 days')
    ");
    $insert->execute([
        'buku_id'    => $bukuId, 
        'anggota_id' => $anggotaId
    ]);

    $update = $pdo->prepare("UPDATE buku SET stok = stok - 1 WHERE id = :id");
    $update->execute(['id' => $bukuId]);

    $pdo->commit();

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Peminjaman berhasil dicatat.'];
    header('Location: ../index.php');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal mencatat peminjaman: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}