#!/data/data/com.termux/files/usr/bin/bash

PIDFILE="/data/local/tmp/php-mobile-server.pid"

su -c '

if [ ! -f "'"$PIDFILE"'" ]; then
    echo "PID tidak ditemukan."
    exit 1
fi

PID=$(cat "'"$PIDFILE"'")

if kill -0 "$PID" 2>/dev/null; then
    kill "$PID"
    sleep 2

    if kill -0 "$PID" 2>/dev/null; then
        kill -9 "$PID"
    fi

    echo "Server dihentikan."
else
    echo "Proses sudah tidak berjalan."
fi

rm -f "'"$PIDFILE"'"
'