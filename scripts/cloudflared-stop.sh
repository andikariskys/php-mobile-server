#!/data/data/com.termux/files/usr/bin/bash

PIDFILE="$HOME/.cloudflared/cloudflared.pid"

if [ ! -f "$PIDFILE" ]; then
    echo "Tunnel tidak sedang berjalan."
    exit 1
fi

PID=$(cat "$PIDFILE")

if kill -0 "$PID" 2>/dev/null; then
    kill "$PID"
    sleep 2

    if kill -0 "$PID" 2>/dev/null; then
        kill -9 "$PID"
    fi

    echo "Tunnel dihentikan."
else
    echo "Proses sudah mati."
fi

rm -f "$PIDFILE"