#!/data/data/com.termux/files/usr/bin/bash

PIDFILE="$HOME/.cloudflared/cloudflared.pid"
LOGFILE="$HOME/.cloudflared/cloudflared.log"
TOKENFILE="$HOME/scripts/cloudflared.token"

if [ ! -f "$TOKENFILE" ]; then
    echo "Token tidak ditemukan."
    exit 1
fi

if [ -f "$PIDFILE" ]; then
    PID=$(cat "$PIDFILE")
    if kill -0 "$PID" 2>/dev/null; then
        echo "Cloudflare Tunnel sudah berjalan. PID=$PID"
        exit 0
    else
        rm -f "$PIDFILE"
    fi
fi

TOKEN=$(cat "$TOKENFILE")

nohup cloudflared tunnel run --token "$TOKEN" \
    >"$LOGFILE" 2>&1 &

echo $! > "$PIDFILE"

echo "Cloudflare Tunnel dijalankan. PID=$(cat "$PIDFILE")"