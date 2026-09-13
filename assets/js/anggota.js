function muatDaftarAnggota() {
    muatDataGenerik("../data/anggota.json", ["no_anggota", "nama", "alamat", "no_hp"]);
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarAnggota();

    const btnReload = document.getElementById("btn-reload");
    if (btnReload) {
        btnReload.addEventListener("click", muatDaftarAnggota);
    }
});