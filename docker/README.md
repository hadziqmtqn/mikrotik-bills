- Docker menggunakan EasyPanel
- Jalankan lewat terminal setelah rebuild image docker
- Ubah <code>bkn-project_spmb-2.1.ct4e2cydgqakj26yknvwllsd5</code> dengan nama container service

```bash

cat <<'EOF' | docker exec -i bkn-project_spmb-2.1.ct4e2cydgqakj26yknvwllsd5 tee /etc/supervisor/conf.d/queue-worker.conf > /dev/null
[program:laravel-queue-worker]
command=/usr/bin/php /code/artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=60 --memory=128
directory=/code
autostart=true
autorestart=true
startsecs=5
startretries=3
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/queue-worker.log
stderr_logfile=/var/log/supervisor/queue-worker.err
stopwaitsecs=360
stopasgroup=true
killasgroup=true
EOF
```
