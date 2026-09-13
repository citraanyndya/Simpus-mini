function muatDaftarBuku() {
    muatDataGenerik("../data/buku.json", ["judul", "pengarang", "tahun", "kategori", "stok"]);
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku();

    const btnReload = document.getElementById("btn-reload");
    if (btnReload) {
        btnReload.addEventListener("click", muatDaftarBuku);
    }
});