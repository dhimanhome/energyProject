# Field Employee Monitoring and Power Reading Audit System

Laravel 12 system for electricity and solar maintenance teams. It accepts all employee readings, records GPS/photo audit data, calculates distance from the assigned site with the Haversine formula, and flags suspicious activity without blocking field submissions.

## Stack

- Laravel 12, PHP 8.3 target
- MySQL, queues, Laravel Sanctum
- Blade, Livewire, Tailwind CSS
- Leaflet.js maps, ApexCharts
- Laravel Reverb/WebSockets
- CSV, Excel, PDF exports

## Local Installation

```bash
cd /Users/dhiman/Documents/projects/energyProject
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
php artisan serve
php artisan queue:work
```

Seeded accounts:

- `admin@poweraudit.local` / `password`
- `supervisor@poweraudit.local` / `password`
- `employee1@poweraudit.local` / `password`

## API

Base URL for the first deployment:

```text
http://182.95.33.114:8989/api
```

Login:

```http
POST /api/login
```

Submission:

```http
POST /api/submission/store
Authorization: Bearer <token>
Content-Type: multipart/form-data
```

Fields:

- `site_id`
- `latitude`
- `longitude`
- `voltage`
- `current`
- `load_percent`
- `energy_reading`
- `notes`
- `timestamp`
- `meter_photo`
- `equipment_photo`

Location update:

```http
POST /api/location/update
```

Sites/history:

```http
GET /api/sites
GET /api/employee/history
```

## Audit Rules

- 0-100 meters: `normal`
- 101-500 meters: `warning`
- Over 500 meters: `suspicious`

Submissions are never rejected because of distance. Distance, risk level, photo status, duplicate/repeated GPS checks, and late-hour checks are stored for review.

## Production `.env`

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://182.95.33.114:8989

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=energy_audit
DB_USERNAME=energy_user
DB_PASSWORD=strong-password

QUEUE_CONNECTION=database
BROADCAST_CONNECTION=reverb
FILESYSTEM_DISK=public

REVERB_APP_ID=energy-audit
REVERB_APP_KEY=change-me
REVERB_APP_SECRET=change-me
REVERB_HOST=182.95.33.114
REVERB_PORT=8080
REVERB_SCHEME=http

MAIL_MAILER=smtp
TELEGRAM_BOT_TOKEN=
TELEGRAM_ALERT_CHAT_ID=
```

## Ubuntu Deployment

```bash
sudo apt update
sudo apt install nginx mysql-server php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath unzip git
cd /var/www
git clone <repo-url> energyProject
cd energyProject
composer install --no-dev --optimize-autoloader
npm ci
npm run build
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --force --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

## Nginx

```nginx
server {
    listen 8989;
    server_name 182.95.33.114;
    root /var/www/energyProject/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location /app/ {
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header SERVER_PORT $server_port;
        proxy_set_header REMOTE_ADDR $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_pass http://127.0.0.1:8080;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Queue and Reverb Services

`/etc/systemd/system/energy-queue.service`

```ini
[Unit]
Description=Energy audit queue worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
WorkingDirectory=/var/www/energyProject
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=120

[Install]
WantedBy=multi-user.target
```

`/etc/systemd/system/energy-reverb.service`

```ini
[Unit]
Description=Energy audit Reverb server
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
WorkingDirectory=/var/www/energyProject
ExecStart=/usr/bin/php artisan reverb:start --host=0.0.0.0 --port=8080

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable --now energy-queue energy-reverb
```




Seeded login:

Admin: admin@poweraudit.local / password
Supervisor: supervisor@poweraudit.local / password
Employee API users: employee1@poweraudit.local / password

