[app]
title = Power Audit
package.name = poweraudit
package.domain = in.poweraudit
source.dir = .
source.include_exts = py,png,jpg,jpeg,kv,json
version = 0.1
requirements = python3,kivy,plyer
p4a.branch = v2024.01.21
orientation = portrait
fullscreen = 0
android.permissions = INTERNET,ACCESS_FINE_LOCATION,ACCESS_COARSE_LOCATION,CAMERA,READ_MEDIA_IMAGES,READ_EXTERNAL_STORAGE
android.api = 35
android.ndk = 25b
android.minapi = 24
android.ndk_api = 24
android.archs = arm64-v8a
android.allow_backup = False
android.gradle_dependencies =
android.add_src =
android.add_jars =
android.add_aars =
android.private_storage = True
android.accept_sdk_license = True

[buildozer]
log_level = 2
warn_on_root = 1
