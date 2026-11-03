# SIM UPPM Kampus SBH

Aplikasi Laravel terpisah untuk pengelolaan penelitian dan Pengabdian kepada Masyarakat (PkM). Identitas pengguna berasal dari PAS, sedangkan seluruh transaksi UPPM disimpan di database `sim-uppm`.

## Menjalankan aplikasi

1. Aktifkan Apache dan MySQL melalui XAMPP.
2. Buka `http://localhost/sim-uppm/public`.
3. Untuk mode pengembangan gunakan akun yang tercantum pada halaman login.

## Menghubungkan PAS asli

Ubah konfigurasi `.env`:

```env
PAS_DRIVER=api
PAS_AUTH_ENDPOINT=https://alamat-pas-kampus/api/authenticate
PAS_API_TOKEN=
PAS_USERNAME_FIELD=username
PAS_PASSWORD_FIELD=password
```

SIM-UPPM mengirim permintaan `POST` JSON berisi username dan password ke endpoint tersebut. Token bearer hanya dikirim jika `PAS_API_TOKEN` diisi. Respons PAS dapat membungkus identitas di properti `data`, `user`, atau langsung sebagai objek JSON.

Contoh respons yang didukung:

```json
{
  "data": {
    "id": "PAS-12345",
    "username": "nama.pengguna",
    "name": "Nama Pengguna",
    "email": "pengguna@kampus.ac.id",
    "nidn": "0123456789",
    "unit_kerja": "Fakultas Ilmu Komputer",
    "prodi": "Sistem Informasi",
    "roles": ["dosen", "reviewer"],
    "is_active": true
  }
}
```

Nomor identitas juga dapat memakai `identity_number`, `nim`, atau `nip`. Nama dapat memakai `name` atau `nama`. Password PAS tidak pernah disimpan di database SIM-UPPM.

## Pemeriksaan

```powershell
php artisan migrate
npm.cmd run build
php artisan test
```
