#!/bin/sh
set -e

# If there are supervisor confs in the repo, copy them into system path
if [ -d /code/docker/supervisor ]; then
  mkdir -p /etc/supervisor/conf.d
  cp -v /code/docker/supervisor/*.conf /etc/supervisor/conf.d/ || true
fi

# Ensure supervisord log dir exists and has sensible permissions
mkdir -p /var/log/supervisor
chown root:root /var/log/supervisor
chmod 755 /var/log/supervisor

# Ensure Laravel writable dirs are owned by www-data
chown -R www-data:www-data /code/storage /code/bootstrap/cache || true
chmod -R 775 /code/storage /code/bootstrap/cache || true

# Optional: ensure /code/vendor readable (usually ok)
chmod -R a+r /code/vendor || true

# Exec the container CMD (so CMD becomes the main process)
exec "$@"
