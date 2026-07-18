# Mobile Server Monitoring WebUI

Aplikasi WebUI berbasis PHP dan SQLite yang dirancang untuk berjalan sebagai modul Magisk untuk memantau (monitoring) serta mengontrol perangkat Android secara langsung dari browser. 

## Fitur Utama

*   **Dashboard Real-Time**: Pemantauan penggunaan CPU, memori (RAM & Swap), penyimpanan, dan kekuatan sinyal seluler LTE (`mLte`) secara real-time setiap 1 detik.
*   **Kontrol Sistem**: Eksekusi perintah restart (reboot), matikan daya (shutdown), dan hapus cache sistem (`/cache/*` dan dalvik-cache) dengan hak akses SuperUser (`su`).
*   **Kontrol Konektivitas**: Pengaturan cepat untuk Wi-Fi, Mobile Data, Bluetooth, Mode Pesawat, GPS Lokasi, serta penambahan alamat IP Statis sementara ke interface `wlan0`.
*   **Layanan Audio & SMS**: Pengiriman pesan SMS, pembacaan teks via Text-to-Speech (TTS) perangkat, serta kontrol volume suara stream secara dinamis menggunakan Termux:API.
*   **Foto Snapshot Kamera**: Pemotretan gambar secara berkala via kamera depan/belakang menggunakan perintah termux-camera-photo, lalu menyimpannya ke folder Pictures perangkat serta webroot.
*   **Multi-Tab File Manager**: Akses cepat ke file sistem direktori Root, Storage (SD Card), Termux Home, dan Webroot (`www`) dengan Tiny File Manager terintegrasi.
*   **Developer Sockets Grid**: Pemantauan port jaringan aktif (`ss -lptn`) dengan auto-kategorisasi untuk layanan Node.js, Python, MariaDB, Redis, dll.

## Petunjuk Instalasi

Sekk... developer'e lagi males gawe dokumentasi hehehe... 

# Dukung Pengembangan Proyek

Jika proyek ini bermanfaat dan membantu pekerjaan Anda, Anda dapat memberikan dukungan agar pengembangan dan pemeliharaan proyek dapat terus berlanjut.

Klik tautan di bawah ini untuk memberikan dukungan:

[https://saweria.co/andikarisky](https://saweria.co/andikarisky)

Setiap dukungan, sekecil apa pun, sangat berarti dan membantu menjaga proyek ini tetap aktif dikembangkan.

Terima kasih atas apresiasi dan dukungan Anda!