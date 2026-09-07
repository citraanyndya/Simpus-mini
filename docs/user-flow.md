# User flow penelusuran dan eksekusi tunggakan annggota

## Petugas
Tamu

## Deskripsi
Petugas memfilter daftar anggota yang memiliki denda untuk ditindaklanjuti

## User-flow

[Petugas Login] ──> [Dashboard] ──> [Menu "Laporan Transaksi"]
                                          │
                                          v
                              [Filter: Status "Terlambat"]
                                          │
                                          v
                              [Data Ditemukan?]
                                /           \
                             Tidak           Ya
                               │              │
                               v              v
                      [Tampilkan Pesan    [Tampilkan Tabel
                       "Nihil Tunggakan"]  Anggota Menunggak]
                               │              │
                               │              v
                               │       [Pilih Nama Anggota]
                               │              │
                               │              v
                               │       [Tampilkan Rincian
                               │        Buku & Denda]
                               │              │
                               │              v
                               │       [Pilih Aksi]
                               │        /        \
                             Bayar Denda      Kirim Teguran
                               │                  │
                               v                  v
                      [Update Status          [Cetak / Kirim
                       "Lunas"]                Notifikasi]
                               \                  /
                                v                v
                              [Kembali ke Dashboard]
                                         │
                                         v
                                     [Selesai]