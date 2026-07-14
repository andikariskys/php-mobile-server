# Termux:API Command Reference

Dokumentasi singkat perintah-perintah **Termux:API**.

---

Gunakan perintah berikut jika ingin masuk kedalam environtment Termux
```bash
export PATH=/data/data/com.termux/files/usr/bin:$PATH && 
```

---

# termux-battery-status

Menampilkan informasi status baterai perangkat dalam format JSON.

### Sintaks

```bash
termux-battery-status
```

### Contoh

```bash
termux-battery-status
```

## Output

```text
{
  "present": true,
  "technology": "Li-poly",
  "health": "GOOD",
  "plugged": "PLUGGED_AC",
  "status": "CHARGING",
  "temperature": 41.0,
  "voltage": 4228,
  "current": 1000,
  "percentage": 75,
  "level": 75,
  "scale": 100
}
```

---

# termux-brightness

Mengatur tingkat kecerahan layar.

### Sintaks

```bash
termux-brightness VALUE
```

### Contoh

```bash
termux-brightness 128
```

---

# termux-call-log

Menampilkan riwayat panggilan.

### Sintaks

```bash
termux-call-log
```

### Opsi

| Opsi | Keterangan |
|------|------------|
| -l LIMIT | Jumlah data yang ditampilkan |

## Output

```text
[
  {
    "name": "UNKNOWN_CALLER",
    "phone_number": "087842352112",
    "type": "OUTGOING",
    "date": "2026-07-12 15:09:06",
    "duration": "00:00",
    "sim_id": "8962115354049560853F"
  }
]
```
---

# termux-camera-info

Menampilkan daftar kamera yang tersedia.

### Sintaks

```bash
termux-camera-info
```

### Contoh

```bash
termux-camera-info
```
## Output

```text
[
  {
    "id": "0",
    "facing": "back",
    "jpeg_output_sizes": [
      {
        "width": 4160,
        "height": 2340
      },
      {
        "width": 4000,
        "height": 3000
      },
      {
        "width": 4000,
        "height": 2250
      },
      {
        "width": 4000,
        "height": 2000
      },
      {
        "width": 3840,
        "height": 2160
      },
      {
        "width": 3264,
        "height": 2448
      },
      {
        "width": 3200,
        "height": 2400
      },
      {
        "width": 2976,
        "height": 2976
      },
      {
        "width": 2592,
        "height": 1944
      },
      {
        "width": 2592,
        "height": 1458
      },
      {
        "width": 2592,
        "height": 1296
      },
      {
        "width": 2688,
        "height": 1512
      },
      {
        "width": 2048,
        "height": 1536
      },
      {
        "width": 1920,
        "height": 1080
      },
      {
        "width": 1920,
        "height": 960
      },
      {
        "width": 1600,
        "height": 1200
      },
      {
        "width": 1440,
        "height": 1080
      },
      {
        "width": 1280,
        "height": 960
      },
      {
        "width": 1280,
        "height": 768
      },
      {
        "width": 1280,
        "height": 720
      },
      {
        "width": 1200,
        "height": 1200
      },
      {
        "width": 1024,
        "height": 768
      },
      {
        "width": 800,
        "height": 600
      },
      {
        "width": 864,
        "height": 480
      },
      {
        "width": 832,
        "height": 486
      },
      {
        "width": 800,
        "height": 480
      },
      {
        "width": 720,
        "height": 480
      },
      {
        "width": 640,
        "height": 480
      },
      {
        "width": 640,
        "height": 360
      },
      {
        "width": 480,
        "height": 640
      },
      {
        "width": 480,
        "height": 360
      },
      {
        "width": 480,
        "height": 320
      },
      {
        "width": 352,
        "height": 288
      },
      {
        "width": 320,
        "height": 240
      },
      {
        "width": 240,
        "height": 320
      }
    ],
    "focal_lengths": [
      4.920000076293945
    ],
    "auto_exposure_modes": [
      "CONTROL_AE_MODE_OFF",
      "CONTROL_AE_MODE_ON",
      "CONTROL_AE_MODE_ON_AUTO_FLASH",
      "CONTROL_AE_MODE_ON_ALWAYS_FLASH",
      "CONTROL_AE_MODE_ON_AUTO_FLASH_REDEYE"
    ],
    "physical_size": {
      "width": 4.730879783630371,
      "height": 3.512320041656494
    },
    "capabilities": [
      "backward_compatible",
      "manual_sensor",
      "manual_post_processing",
      "read_sensor_settings",
      "burst_capture",
      "constrained_high_speed_video",
      "raw"
    ]
  },
  {
    "id": "1",
    "facing": "front",
    "jpeg_output_sizes": [
      {
        "width": 2592,
        "height": 1944
      },
      {
        "width": 2592,
        "height": 1458
      },
      {
        "width": 2592,
        "height": 1296
      },
      {
        "width": 2048,
        "height": 1536
      },
      {
        "width": 1920,
        "height": 1080
      },
      {
        "width": 1920,
        "height": 960
      },
      {
        "width": 1600,
        "height": 1200
      },
      {
        "width": 1440,
        "height": 1080
      },
      {
        "width": 1280,
        "height": 960
      },
      {
        "width": 1280,
        "height": 768
      },
      {
        "width": 1280,
        "height": 720
      },
      {
        "width": 1200,
        "height": 1200
      },
      {
        "width": 1024,
        "height": 768
      },
      {
        "width": 800,
        "height": 600
      },
      {
        "width": 864,
        "height": 480
      },
      {
        "width": 832,
        "height": 486
      },
      {
        "width": 800,
        "height": 480
      },
      {
        "width": 720,
        "height": 480
      },
      {
        "width": 640,
        "height": 480
      },
      {
        "width": 640,
        "height": 360
      },
      {
        "width": 480,
        "height": 640
      },
      {
        "width": 480,
        "height": 360
      },
      {
        "width": 480,
        "height": 320
      },
      {
        "width": 352,
        "height": 288
      },
      {
        "width": 320,
        "height": 240
      },
      {
        "width": 240,
        "height": 320
      }
    ],
    "focal_lengths": [
      2.1700000762939453
    ],
    "auto_exposure_modes": [
      "CONTROL_AE_MODE_OFF",
      "CONTROL_AE_MODE_ON"
    ],
    "physical_size": {
      "width": 2.9030399322509766,
      "height": 2.1772799491882324
    },
    "capabilities": [
      "backward_compatible",
      "manual_sensor",
      "manual_post_processing",
      "read_sensor_settings",
      "burst_capture",
      "constrained_high_speed_video",
      "raw"
    ]
  }
]
```

---

# termux-camera-photo

Mengambil foto dan menyimpannya sebagai file JPEG.

### Sintaks

```bash
termux-camera-photo [OPTIONS] FILE
```

### Opsi

| Opsi | Keterangan |
|------|------------|
| -c CAMERA_ID | ID kamera (lihat `termux-camera-info`) |

### Contoh

```bash
termux-camera-photo foto.jpg
```

```bash
termux-camera-photo -c 1 selfie.jpg
```

---

# termux-clipboard-get

Mengambil isi clipboard.

### Sintaks

```bash
termux-clipboard-get
```

---

# termux-clipboard-set

Mengisi clipboard.

### Sintaks

```bash
termux-clipboard-set
```

### Contoh

```bash
termux-clipboard-set "Hello from Termux!"
```

---

# termux-contact-list

Menampilkan daftar kontak.

### Sintaks

```bash
termux-contact-list
```
## Output

```text
[
  {
    "name": "Me",
    "number": "0878-4235-2112"
  }
]
```

---

# termux-dialog

Menampilkan dialog Android.

### Sintaks

```bash
termux-dialog
```

### Contoh

```bash
termux-dialog text
```

---

# termux-download

Mengunduh file menggunakan Download Manager Android.

### Sintaks

```bash
termux-download URL
```

---

# termux-fingerprint

Autentikasi menggunakan sidik jari.

### Sintaks

```bash
termux-fingerprint
```

---

# termux-infrared-frequencies

Menampilkan frekuensi infrared yang didukung.

### Sintaks

```bash
termux-infrared-frequencies
```

---

# termux-infrared-transmit

Mengirim sinyal infrared.

### Sintaks

```bash
termux-infrared-transmit FREQUENCY PATTERN
```

---

# termux-job-scheduler

Menjadwalkan pekerjaan menggunakan Android JobScheduler.

### Sintaks

```bash
termux-job-scheduler
```

---

# termux-keystore

Mengelola Android Keystore.

### Sintaks

```bash
termux-keystore
```

---

# termux-location

Mengambil lokasi GPS.

### Sintaks

```bash
termux-location
```

### Opsi

| Opsi | Keterangan |
|------|------------|
| -p provider | gps / network / passive |
| -r | Meminta update lokasi |

---

# termux-media-player

Mengontrol media player.

### Sintaks

```bash
termux-media-player COMMAND
```

### Contoh

```bash
termux-media-player play file.mp3
```

---

# termux-media-scan

Memindai file media agar muncul di galeri.

### Sintaks

```bash
termux-media-scan FILE
```

---

# termux-microphone-record

Merekam suara.

### Sintaks

```bash
termux-microphone-record
```

### Opsi

| Opsi | Keterangan |
|------|------------|
| -f FILE | Nama file output |
| -l LIMIT | Durasi |

---

# termux-nfc

Membaca tag NFC.

### Sintaks

```bash
termux-nfc
```

---

# termux-notification

Menampilkan notifikasi Android.

### Sintaks

```bash
termux-notification
```

### Contoh

```bash
termux-notification \
--title "Hello" \
--content "Testing" \
--id not1
```

---

# termux-notification-remove

Menghapus notifikasi.

### Sintaks

```bash
termux-notification-remove ID
```

---

# termux-open

Membuka file atau URL.

### Sintaks

```bash
termux-open FILE
```

```bash
termux-open https://example.com
```

---

# termux-open-url

Membuka URL.

### Sintaks

```bash
termux-open-url URL
```

---

# termux-reload-settings

Memuat ulang konfigurasi Termux.

### Sintaks

```bash
termux-reload-settings
```

---

# termux-sensor

Membaca data sensor perangkat.

### Sintaks

```bash
termux-sensor
```

---

# termux-share

Membagikan file atau teks.

### Sintaks

```bash
termux-share
```

---

# termux-sms-list

Menampilkan daftar SMS.

### Sintaks

```bash
termux-sms-list
```
## Output

```text
[
  {
    "threadid": 2,
    "type": "inbox",
    "read": true,
    "address": "XL ",
    "number": "XL ",
    "received": "2026-07-12 08:53:04",
    "body": "Yeay! Kamu mendapatkan Hadiah dari Teman Kamu (6287841132265). Cek Hadiah Kamu disini:  myxlapp.page.link/rewardsgift",
    "_id": 33
  },
  {
    "threadid": 2,
    "type": "inbox",
    "read": true,
    "address": "XL ",
    "number": "XL ",
    "received": "2026-07-12 14:23:35",
    "body": "(XL) Kuota Area Pkt Xtra Combo Mini Anda sdh habis. Saat ini berlaku tarif dasar internet. Aktifkan lagi paketnya di aplikasi MyXL.",
    "_id": 45
  },
  {
    "threadid": 3,
    "type": "sent",
    "read": true,
    "sender": "You",
    "address": "0878-4235-2112",
    "number": "0878-4235-2112",
    "received": "2026-07-12 15:47:08",
    "body": "Hello world",
    "_id": 46
  }
]
```

---

# termux-sms-send

Mengirim SMS.

### Sintaks

```bash
termux-sms-send
```

### Contoh

```bash
termux-sms-send \
-n 08123456789 \
"Halo"
```

---

# termux-storage-get

Memilih file dari penyimpanan.

### Sintaks

```bash
termux-storage-get
```

---

# termux-telephony-call

Melakukan panggilan telepon.

### Sintaks

```bash
termux-telephony-call NUMBER
```

---

# termux-telephony-cellinfo

Informasi BTS (Cell Tower).

### Sintaks

```bash
termux-telephony-cellinfo
```

---

# termux-telephony-deviceinfo

Informasi perangkat telepon.

### Sintaks

```bash
termux-telephony-deviceinfo
```
## Output

```text
{
  "data_enabled": "true",
  "data_activity": "none",
  "data_state": "connected",
  "device_id": null,
  "device_software_version": "00",
  "phone_count": 2,
  "phone_type": "gsm",
  "network_operator": "51011",
  "network_operator_name": "XL Axiata",
  "network_country_iso": "id",
  "network_type": "lte",
  "network_roaming": false,
  "sim_country_iso": "id",
  "sim_operator": "51011",
  "sim_operator_name": "XL Axiata",
  "sim_serial_number": null,
  "sim_subscriber_id": null,
  "sim_state": "ready"
}
```

---

# termux-toast

Menampilkan pesan Toast.

### Sintaks

```bash
termux-toast MESSAGE
```

### Contoh

```bash
termux-toast "Halo Dunia"
```

---

# termux-torch

Mengaktifkan atau mematikan flashlight.

### Sintaks

```bash
termux-torch on
```

```bash
termux-torch off
```

---

# termux-tts-engines

Menampilkan mesin Text-to-Speech.

### Sintaks

```bash
termux-tts-engines
```
## Output

```text
[
  {
    "name": "es.codefactory.vocalizertts",
    "label": "Vocalizer TTS",
    "default": true
  }
]
```

---

# termux-tts-speak

Mengucapkan teks menggunakan TTS.

### Sintaks

```bash
termux-tts-speak "Halo Dunia"
```

---

# termux-usb

Mengakses perangkat USB.

### Sintaks

```bash
termux-usb
```

---

# termux-vibrate

Menggetarkan perangkat.

### Sintaks

```bash
termux-vibrate
```

### Contoh

```bash
termux-vibrate -d 1000
```

---

# termux-volume

Menampilkan atau mengatur volume audio.

### Sintaks

```bash
termux-volume
```
## Output

```text
[
  {
    "stream": "call",
    "volume": 5,
    "max_volume": 7
  },
  {
    "stream": "system",
    "volume": 5,
    "max_volume": 7
  },
  {
    "stream": "ring",
    "volume": 5,
    "max_volume": 7
  },
  {
    "stream": "music",
    "volume": 6,
    "max_volume": 15
  },
  {
    "stream": "alarm",
    "volume": 6,
    "max_volume": 7
  },
  {
    "stream": "notification",
    "volume": 5,
    "max_volume": 7
  }
]
```

---

# termux-wallpaper

Mengatur wallpaper.

### Sintaks

```bash
termux-wallpaper
```

---

# termux-wifi-connectioninfo

Informasi koneksi WiFi.

### Sintaks

```bash
termux-wifi-connectioninfo
```

## Output

```text
{
  "bssid": "02:00:00:00:00:00",
  "frequency_mhz": -1,
  "ip": "0.0.0.0",
  "link_speed_mbps": -1,
  "mac_address": "02:00:00:00:00:00",
  "network_id": -1,
  "rssi": -127,
  "ssid": "<unknown ssid>",
  "ssid_hidden": true,
  "supplicant_state": "UNINITIALIZED"
}
```

---

# termux-wifi-enable

Mengaktifkan atau menonaktifkan WiFi.

### Sintaks

```bash
termux-wifi-enable true
```

```bash
termux-wifi-enable false
```

---

# termux-wifi-scaninfo

Hasil pemindaian jaringan WiFi.

### Sintaks

```bash
termux-wifi-scaninfo
```

---

# termux-speech-to-text

Mengubah suara menjadi teks.

### Sintaks

```bash
termux-speech-to-text
```

---

## Referensi

https://wiki.termux.com/wiki/Termux:API