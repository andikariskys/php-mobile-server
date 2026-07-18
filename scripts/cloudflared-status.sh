#!/data/data/com.termux/files/usr/bin/bash

PIDFILE="$HOME/.cloudflared/cloudflared.pid"

if [ ! -f "$PIDFILE" ]; then
    echo "Status: OFF"
    exit
fi

PID=$(cat "$PIDFILE")

if kill -0 "$PID" 2>/dev/null; then
    echo "Status: ON (PID $PID)"
else
    echo "Status: OFF"
fi