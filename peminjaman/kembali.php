<?php
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
$page_title = "Pengembalian Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $stmt = $pdo->prepare("
        SELECT p.id, a.nama AS nama_anggota, a.no_hp, b.judul AS judul_buku, p.tanggal_pinjam, p.tanggal_jatuh_tempo
        FROM peminjaman p
        JOIN anggota a ON p.anggota_id = a.id
        JOIN buku b ON p.buku_id = b.id
        WHERE p.tanggal_kembali IS NULL
          AND (b.judul ILIKE :kw OR a.nama ILIKE :kw)
        ORDER BY p.tanggal_pinjam ASC
    ");
    $stmt->execute(['kw' => '%' . $keyword . '%']);
} else {
    $stmt = $pdo->prepare("
        SELECT p.id, a.nama AS nama_anggota, a.no_hp, b.judul AS judul_buku, p.tanggal_pinjam, p.tanggal_jatuh_tempo
        FROM peminjaman p
        JOIN anggota a ON p.anggota_id = a.id
        JOIN buku b ON p.buku_id = b.id
        WHERE p.tanggal_kembali IS NULL
        ORDER BY p.tanggal_pinjam ASC
    ");
    $stmt->execute();
}

$daftarAktif = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section>
    <h2>Pengembalian Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?= htmlspecialchars($flash['type']); ?>"><?= htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <div class="search-box">
        <form method="get" action="kembali.php">
            <span>
                <label for="search-input">Cari anggota/buku</label><br>
                <input type="text" id="search-input" name="q" value="<?= htmlspecialchars($keyword); ?>" placeholder="Nama anggota atau judul buku...">
            </span>
            <button type="submit">Cari</button>
        </form>
    </div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Anggota</th>
                <th>No HP</th>
                <th>Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($daftarAktif)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada transaksi peminjaman aktif.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($daftarAktif as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($row['nama_anggota'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($row['no_hp'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($row['judul_buku'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pinjam'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_jatuh_tempo'] ?? ''); ?></td>
                        <td>
                            <form method="post" action="proses_kembali.php" style="margin: 0;">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="peminjaman_id" value="<?= $row['id']; ?>">
                                <button type="submit">Kembalikan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>