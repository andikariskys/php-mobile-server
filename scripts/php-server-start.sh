#!/data/data/com.termux/files/usr/bin/bash

PIDFILE="/data/local/tmp/php-mobile-server.pid"
LOGFILE="/data/local/tmp/php-mobile-server.log"

su -c '
export PATH=/data/data/com.termux/files/usr/bin:$PATH

cd /

cd sdcard

cd php-mobile-server || exit 1

if [ -f "'"$PIDFILE"'" ]; then
    PID=$(cat "'"$PIDFILE"'")
    if kill -0 "$PID" 2>/dev/null; then
        echo "Server sudah berjalan (PID $PID)"
        exit 0
    else
        rm -f "'"$PIDFILE"'"
    fi
fi

nohup php -S 0.0.0.0:80 \
> "'"$LOGFILE"'" 2>&1 &

echo $! > "'"$PIDFILE"'"

echo "Server dijalankan. PID $(cat "'"$PIDFILE"'")"
'