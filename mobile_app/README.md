# Power Audit Android App

Fast Python/Kivy Android client for field employees.

## Features

- Employee login through Laravel Sanctum
- Saved login session; the app reopens directly to the reading form while the token is valid
- Fetch assigned active sites
- Fill only Active Power and Unit
- Read GPS using Android location provider via Plyer
- Attach meter photo and equipment photo
- Submit multipart data to `/api/submission/store`
- Live tracking button sends location pings to `/api/location/update`
- Does not block outside-radius submissions; server performs distance audit

## Live Location

On Android, tap **Start Live Tracking** after login. The app sends a GPS ping every 60 seconds while it is running. The Laravel dashboard shows these pings at:

```text
/live-employees
```

Desktop/macOS has no Plyer GPS provider, so use **Use Manual Location** for testing.

## Local Desktop Test

```bash
cd /Users/dhiman/Documents/projects/energyProject/mobile_app
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
python main.py
```

The API base URL is intentionally hidden from the app UI. Configure it in `main.py` or with `POWER_AUDIT_API_BASE`.

Production default is:

```text
http://182.95.33.114:8989/api
```

For Mac desktop testing against the Laravel dev server, set:

```bash
export POWER_AUDIT_API_BASE=http://127.0.0.1:8000/api
```

For Android emulator testing against the Laravel dev server, set:

```bash
export POWER_AUDIT_API_BASE=http://10.0.2.2:8000/api
```

For physical phone on same network, use your computer LAN IP:

```bash
export POWER_AUDIT_API_BASE=http://192.168.1.10:8000/api
```

## Build APK

Buildozer works best on Linux/Ubuntu.

```bash
cd /Users/dhiman/Documents/projects/energyProject/mobile_app
pip install buildozer
buildozer android debug
```

APK output:

```text
bin/poweraudit-0.1-arm64-v8a-debug.apk
```

## Android Permissions

Configured in `buildozer.spec`:

- INTERNET
- ACCESS_FINE_LOCATION
- ACCESS_COARSE_LOCATION
- CAMERA
- READ_MEDIA_IMAGES
- READ_EXTERNAL_STORAGE
