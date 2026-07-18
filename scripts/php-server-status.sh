#!/data/data/com.termux/files/usr/bin/bash

PIDFILE="/data/local/tmp/php-mobile-server.pid"

su -c '

if [ ! -f "'"$PIDFILE"'" ]; then
    echo "Server OFF"
    exit
fi

PID=$(cat "'"$PIDFILE"'")

if kill -0 "$PID" 2>/dev/null; then
    echo "Server ON (PID $PID)"
else
    echo "Server OFF"
fi
'