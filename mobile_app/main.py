import json
import mimetypes
import os
import threading
import uuid
from pathlib import Path
from urllib import error, request

from kivy.app import App
from kivy.clock import Clock
from kivy.graphics import Color, RoundedRectangle
from kivy.metrics import dp
from kivy.uix.boxlayout import BoxLayout
from kivy.uix.button import Button
from kivy.uix.filechooser import FileChooserIconView
from kivy.uix.gridlayout import GridLayout
from kivy.uix.label import Label
from kivy.uix.popup import Popup
from kivy.uix.scrollview import ScrollView
from kivy.uix.spinner import Spinner
from kivy.uix.textinput import TextInput

try:
    from plyer import gps
except Exception:
    gps = None


API_BASE = os.getenv("POWER_AUDIT_API_BASE", "http://182.95.33.114:8989/api").rstrip("/")
SESSION_FILE = Path.home() / ".power_audit_mobile_session.json"
COLORS = {
    "bg": (0.94, 0.97, 0.98, 1),
    "card": (1, 1, 1, 1),
    "primary": (0.04, 0.16, 0.31, 1),
    "primary_soft": (0.88, 0.95, 1, 1),
    "muted": (0.38, 0.45, 0.55, 1),
    "success": (0.05, 0.55, 0.34, 1),
    "warning": (0.78, 0.46, 0.05, 1),
    "danger": (0.8, 0.12, 0.18, 1),
}


class SessionStore:
    def load(self):
        try:
            return json.loads(SESSION_FILE.read_text())
        except Exception:
            return {}

    def save(self, payload):
        SESSION_FILE.write_text(json.dumps(payload))

    def clear(self):
        try:
            SESSION_FILE.unlink()
        except FileNotFoundError:
            pass


class ApiError(Exception):
    def __init__(self, status_code, message):
        super().__init__(message)
        self.status_code = status_code
        self.message = message


class ApiClient:
    def __init__(self):
        self.token = None
        self.employee = None
        self.base_url = API_BASE

    @property
    def headers(self):
        return {"Authorization": f"Bearer {self.token}", "Accept": "application/json"}

    def restore(self, token, employee=None):
        self.token = token
        self.employee = employee

    def login(self, email, password):
        payload = self.post_json("/login", {"email": email, "password": password, "device_name": "android-field-app"})
        self.token = payload["token"]
        self.employee = payload.get("employee")
        return payload

    def logout(self):
        if self.token:
            try:
                self.post_json("/logout", {}, auth=True)
            except Exception:
                pass
        self.token = None
        self.employee = None

    def sites(self):
        return self.get_json("/sites", auth=True).get("data", [])

    def update_location(self, latitude, longitude, accuracy=None):
        return self.post_json("/location/update", {"latitude": latitude, "longitude": longitude, "accuracy": accuracy}, auth=True)

    def submit(self, data, meter_photo=None, equipment_photo=None):
        files = {}
        if meter_photo:
            files["meter_photo"] = meter_photo
        if equipment_photo:
            files["equipment_photo"] = equipment_photo

        return self.post_multipart("/submission/store", data, files, auth=True)

    def get_json(self, path, auth=False):
        return self.open_json(path, method="GET", auth=auth)

    def post_json(self, path, payload, auth=False):
        body = json.dumps(payload).encode("utf-8")
        return self.open_json(path, method="POST", body=body, auth=auth, content_type="application/json")

    def open_json(self, path, method="GET", body=None, auth=False, content_type=None):
        headers = {"Accept": "application/json"}
        if content_type:
            headers["Content-Type"] = content_type
        if auth:
            headers["Authorization"] = f"Bearer {self.token}"

        req = request.Request(f"{self.base_url}{path}", data=body, headers=headers, method=method)
        try:
            with request.urlopen(req, timeout=30) as response:
                raw = response.read().decode("utf-8")
                return json.loads(raw) if raw else {}
        except error.HTTPError as exc:
            message = exc.read().decode("utf-8", errors="ignore")[:220]
            raise ApiError(exc.code, message or exc.reason)

    def post_multipart(self, path, fields, files, auth=False):
        boundary = f"----PowerAudit{uuid.uuid4().hex}"
        body = bytearray()

        for name, value in fields.items():
            body.extend(f"--{boundary}\r\n".encode())
            body.extend(f'Content-Disposition: form-data; name="{name}"\r\n\r\n'.encode())
            body.extend(str(value).encode())
            body.extend(b"\r\n")

        for name, file_path in files.items():
            path_obj = Path(file_path)
            mime_type = mimetypes.guess_type(str(path_obj))[0] or "application/octet-stream"
            body.extend(f"--{boundary}\r\n".encode())
            body.extend(f'Content-Disposition: form-data; name="{name}"; filename="{path_obj.name}"\r\n'.encode())
            body.extend(f"Content-Type: {mime_type}\r\n\r\n".encode())
            body.extend(path_obj.read_bytes())
            body.extend(b"\r\n")

        body.extend(f"--{boundary}--\r\n".encode())
        headers = {
            "Accept": "application/json",
            "Content-Type": f"multipart/form-data; boundary={boundary}",
        }
        if auth:
            headers["Authorization"] = f"Bearer {self.token}"

        req = request.Request(f"{self.base_url}{path}", data=bytes(body), headers=headers, method="POST")
        try:
            with request.urlopen(req, timeout=60) as response:
                raw = response.read().decode("utf-8")
                return json.loads(raw) if raw else {}
        except error.HTTPError as exc:
            message = exc.read().decode("utf-8", errors="ignore")[:220]
            raise ApiError(exc.code, message or exc.reason)


class Card(BoxLayout):
    def __init__(self, **kwargs):
        super().__init__(padding=dp(14), spacing=dp(10), **kwargs)
        self.bind(pos=self._paint, size=self._paint)

    def _paint(self, *_):
        self.canvas.before.clear()
        with self.canvas.before:
            Color(*COLORS["card"])
            RoundedRectangle(pos=self.pos, size=self.size, radius=[dp(12)])


class LabeledInput(BoxLayout):
    def __init__(self, label, hint="", numeric=False, **kwargs):
        super().__init__(orientation="vertical", spacing=dp(5), size_hint_y=None, height=dp(76), **kwargs)
        self.add_widget(Label(text=label, color=COLORS["muted"], halign="left", size_hint_y=None, height=dp(22)))
        self.input = TextInput(
            hint_text=hint,
            multiline=False,
            input_filter="float" if numeric else None,
            background_color=(0.96, 0.98, 1, 1),
            foreground_color=(0.05, 0.08, 0.12, 1),
            cursor_color=COLORS["primary"],
            padding=[dp(10), dp(10)],
            size_hint_y=None,
            height=dp(44),
        )
        self.add_widget(self.input)

    @property
    def text(self):
        return self.input.text.strip()

    @text.setter
    def text(self, value):
        self.input.text = str(value or "")


class AppButton(Button):
    def __init__(self, text, kind="primary", **kwargs):
        colors = {
            "primary": COLORS["primary"],
            "light": COLORS["primary_soft"],
            "success": COLORS["success"],
            "warning": COLORS["warning"],
            "danger": COLORS["danger"],
        }
        text_colors = {
            "primary": (1, 1, 1, 1),
            "light": COLORS["primary"],
            "success": (1, 1, 1, 1),
            "warning": (1, 1, 1, 1),
            "danger": (1, 1, 1, 1),
        }
        super().__init__(
            text=text,
            background_normal="",
            background_color=colors[kind],
            color=text_colors[kind],
            bold=True,
            size_hint_y=None,
            height=dp(48),
            **kwargs,
        )


class PowerAuditRoot(BoxLayout):
    def __init__(self, **kwargs):
        super().__init__(orientation="vertical", padding=dp(14), spacing=dp(12), **kwargs)
        self.bind(pos=self._paint, size=self._paint)
        self.api = ApiClient()
        self.store = SessionStore()
        self.saved_session = self.store.load()
        self.sites = []
        self.selected_site = None
        self.latitude = None
        self.longitude = None
        self.accuracy = None
        self.meter_photo = None
        self.equipment_photo = None
        self.tracking_event = None
        self.tracking_enabled = False

        self.header = BoxLayout(orientation="vertical", size_hint_y=None, height=dp(76), spacing=dp(4))
        self.header_title = Label(text="Power Audit", font_size=dp(24), bold=True, color=COLORS["primary"], halign="left")
        self.header_subtitle = Label(text="Field reading and live location", color=COLORS["muted"], halign="left")
        self.header.add_widget(self.header_title)
        self.header.add_widget(self.header_subtitle)
        self.add_widget(self.header)

        self.status = Label(text="Starting...", color=COLORS["muted"], size_hint_y=None, height=dp(34))
        self.add_widget(self.status)

        self.try_restore_session()

    def _paint(self, *_):
        self.canvas.before.clear()
        with self.canvas.before:
            Color(*COLORS["bg"])
            RoundedRectangle(pos=self.pos, size=self.size, radius=[0])

    def clear_body(self):
        while len(self.children) > 2:
            self.remove_widget(self.children[0])

    def try_restore_session(self):
        session = self.saved_session
        token = session.get("token")
        if not token:
            self.show_login()
            self.set_status("Please sign in.")
            return
        self.api.restore(token, session.get("employee"))
        self.set_status("Restoring session...")
        self.run_async(self.restore_sites)

    def restore_sites(self):
        self.sites = self.api.sites()
        Clock.schedule_once(lambda *_: self.show_form())
        employee_name = (self.api.employee or {}).get("name", "Employee")
        self.set_status(f"Welcome back, {employee_name}.")

    def show_login(self):
        self.clear_body()
        card = Card(orientation="vertical", size_hint_y=None, height=dp(300))
        card.add_widget(Label(text="Employee Login", font_size=dp(20), bold=True, color=COLORS["primary"], size_hint_y=None, height=dp(36)))
        self.email = LabeledInput("Email", "employee1@poweraudit.local")
        self.password = LabeledInput("Password", "password")
        self.password.input.password = True
        login_btn = AppButton("Login")
        login_btn.bind(on_press=lambda *_: self.run_async(self.do_login))
        card.add_widget(self.email)
        card.add_widget(self.password)
        card.add_widget(login_btn)
        self.add_widget(card)

    def show_form(self):
        self.clear_body()
        employee_name = (self.api.employee or {}).get("name", "Field employee")
        self.header_subtitle.text = employee_name

        scroll = ScrollView()
        form = GridLayout(cols=1, spacing=dp(12), size_hint_y=None)
        form.bind(minimum_height=form.setter("height"))

        site_card = Card(orientation="vertical", size_hint_y=None, height=dp(122))
        site_card.add_widget(Label(text="Assigned Site", font_size=dp(18), bold=True, color=COLORS["primary"], size_hint_y=None, height=dp(28)))
        default_site = self.default_site()
        self.selected_site = default_site
        self.site_spinner = Spinner(
            text=default_site["site_name"] if default_site else "Select site",
            values=[s["site_name"] for s in self.sites],
            background_normal="",
            background_color=COLORS["primary_soft"],
            color=COLORS["primary"],
            size_hint_y=None,
            height=dp(48),
        )
        self.site_spinner.bind(text=self.on_site_selected)
        site_card.add_widget(self.site_spinner)

        reading_card = Card(orientation="vertical", size_hint_y=None, height=dp(220))
        reading_card.add_widget(Label(text="Power Reading", font_size=dp(18), bold=True, color=COLORS["primary"], size_hint_y=None, height=dp(28)))
        self.active_power = LabeledInput("Active Power", "42.5", numeric=True)
        self.energy_reading = LabeledInput("Unit", "1250", numeric=True)
        for widget in [self.active_power, self.energy_reading]:
            reading_card.add_widget(widget)

        location_card = Card(orientation="vertical", size_hint_y=None, height=dp(318))
        location_card.add_widget(Label(text="Location", font_size=dp(18), bold=True, color=COLORS["primary"], size_hint_y=None, height=dp(28)))
        self.manual_latitude = LabeledInput("Latitude", numeric=True)
        self.manual_longitude = LabeledInput("Longitude", numeric=True)
        self.manual_latitude.text = "28.4595"
        self.manual_longitude.text = "77.0266"
        self.gps_label = Label(text="GPS not captured", color=COLORS["muted"], size_hint_y=None, height=dp(28))
        gps_row = BoxLayout(spacing=dp(8), size_hint_y=None, height=dp(48))
        gps_btn = AppButton("Get GPS", kind="light")
        gps_btn.bind(on_press=lambda *_: self.get_gps())
        manual_gps_btn = AppButton("Use Manual", kind="light")
        manual_gps_btn.bind(on_press=lambda *_: self.use_manual_location())
        gps_row.add_widget(gps_btn)
        gps_row.add_widget(manual_gps_btn)
        self.tracking_btn = AppButton("Start Live Tracking", kind="success")
        self.tracking_btn.bind(on_press=lambda *_: self.toggle_live_tracking())
        for widget in [self.manual_latitude, self.manual_longitude, self.gps_label, gps_row, self.tracking_btn]:
            location_card.add_widget(widget)

        photo_card = Card(orientation="vertical", size_hint_y=None, height=dp(210))
        photo_card.add_widget(Label(text="Photo Proof", font_size=dp(18), bold=True, color=COLORS["primary"], size_hint_y=None, height=dp(28)))
        meter_btn = AppButton("Select Meter Photo", kind="light")
        meter_btn.bind(on_press=lambda *_: self.pick_photo("meter"))
        self.meter_label = Label(text="Meter photo not selected", color=COLORS["muted"], size_hint_y=None, height=dp(26))
        equipment_btn = AppButton("Select Equipment Photo", kind="light")
        equipment_btn.bind(on_press=lambda *_: self.pick_photo("equipment"))
        self.equipment_label = Label(text="Equipment photo not selected", color=COLORS["muted"], size_hint_y=None, height=dp(26))
        for widget in [meter_btn, self.meter_label, equipment_btn, self.equipment_label]:
            photo_card.add_widget(widget)

        action_card = Card(orientation="vertical", size_hint_y=None, height=dp(128))
        submit_btn = AppButton("Submit Reading")
        submit_btn.bind(on_press=lambda *_: self.run_async(self.do_submit))
        logout_btn = AppButton("Logout", kind="danger")
        logout_btn.bind(on_press=lambda *_: self.run_async(self.do_logout))
        action_card.add_widget(submit_btn)
        action_card.add_widget(logout_btn)

        for card in [site_card, reading_card, location_card, photo_card, action_card]:
            form.add_widget(card)

        scroll.add_widget(form)
        self.add_widget(scroll)

    def on_site_selected(self, _spinner, text):
        self.selected_site = next((site for site in self.sites if site["site_name"] == text), None)
        if self.selected_site:
            self.saved_session["last_site_id"] = self.selected_site["id"]
            self.store.save(self.saved_session)

    def default_site(self):
        if len(self.sites) == 1:
            return self.sites[0]

        last_site_id = self.saved_session.get("last_site_id")
        if last_site_id:
            return next((site for site in self.sites if site["id"] == last_site_id), None)

        return None

    def do_login(self):
        if not self.email.text or not self.password.text:
            self.set_status("Enter email and password.")
            return
        self.set_status("Logging in...")
        payload = self.api.login(self.email.text, self.password.text)
        self.saved_session = {
            **self.saved_session,
            "token": payload["token"],
            "employee": payload.get("employee"),
        }
        self.store.save(self.saved_session)
        self.sites = self.api.sites()
        Clock.schedule_once(lambda *_: self.show_form())
        self.set_status("Logged in. Select a site and fill readings.")

    def do_logout(self):
        self.stop_live_tracking()
        self.api.logout()
        self.store.clear()
        self.saved_session = {}
        Clock.schedule_once(lambda *_: self.show_login())
        self.set_status("Logged out.")

    def get_gps(self):
        if gps is None:
            self.use_manual_location("Desktop has no GPS provider.")
            return

        self.set_status("Waiting for GPS...")
        try:
            gps.configure(on_location=self.on_location, on_status=lambda stype, status: self.set_status(str(status)))
            gps.start(minTime=1000, minDistance=1)
        except Exception as exc:
            self.use_manual_location(f"GPS unavailable here: {exc}")

    def use_manual_location(self, note="Manual GPS applied."):
        try:
            self.latitude = float(self.manual_latitude.text)
            self.longitude = float(self.manual_longitude.text)
            self.accuracy = None
            self.gps_label.text = f"GPS: {self.latitude}, {self.longitude}"
            self.set_status(note)
            if self.api.token:
                self.run_async(lambda: self.api.update_location(self.latitude, self.longitude, self.accuracy))
        except ValueError:
            self.set_status("Enter valid latitude and longitude.")

    def toggle_live_tracking(self):
        if self.tracking_enabled:
            self.stop_live_tracking()
        else:
            self.start_live_tracking()

    def start_live_tracking(self):
        self.tracking_enabled = True
        self.tracking_btn.text = "Stop Live Tracking"
        self.set_status("Live tracking started. Sending GPS every 60 seconds.")
        self.get_gps()
        if self.tracking_event is None:
            self.tracking_event = Clock.schedule_interval(lambda _dt: self.get_gps(), 60)

    def stop_live_tracking(self):
        self.tracking_enabled = False
        if hasattr(self, "tracking_btn"):
            self.tracking_btn.text = "Start Live Tracking"
        if self.tracking_event is not None:
            self.tracking_event.cancel()
            self.tracking_event = None
        if gps:
            try:
                gps.stop()
            except Exception:
                pass
        self.set_status("Live tracking stopped.")

    def on_location(self, **kwargs):
        self.latitude = kwargs.get("lat")
        self.longitude = kwargs.get("lon")
        self.accuracy = kwargs.get("accuracy")
        Clock.schedule_once(lambda *_: self.after_gps())

    def after_gps(self):
        self.gps_label.text = f"GPS: {self.latitude}, {self.longitude}"
        self.set_status("GPS captured.")
        if self.api.token and self.latitude and self.longitude:
            self.run_async(lambda: self.api.update_location(self.latitude, self.longitude, self.accuracy))
        if gps:
            gps.stop()

    def pick_photo(self, target):
        chooser = FileChooserIconView(filters=["*.jpg", "*.jpeg", "*.png", "*.webp"])
        popup = Popup(title="Select photo", content=chooser, size_hint=(0.95, 0.9))

        def selected(_chooser, selection, *_args):
            if not selection:
                return
            path = selection[0]
            if target == "meter":
                self.meter_photo = path
                self.meter_label.text = f"Meter: {Path(path).name}"
            else:
                self.equipment_photo = path
                self.equipment_label.text = f"Equipment: {Path(path).name}"
            popup.dismiss()

        chooser.bind(on_submit=selected)
        popup.open()

    def do_submit(self):
        if not self.selected_site:
            self.set_status("Select site.")
            return
        if not self.latitude or not self.longitude:
            self.set_status("Capture GPS first.")
            return
        required = [self.active_power.text, self.energy_reading.text]
        if any(not value for value in required):
            self.set_status("Fill all reading fields.")
            return

        self.set_status("Submitting reading...")
        payload = {
            "site_id": self.selected_site["id"],
            "latitude": self.latitude,
            "longitude": self.longitude,
            "active_power": self.active_power.text,
            "energy_reading": self.energy_reading.text,
            "notes": "",
        }
        result = self.api.submit(payload, self.meter_photo, self.equipment_photo)
        data = result.get("data", {})
        self.set_status(f"Submitted. Risk: {data.get('risk_level')} Distance: {data.get('distance_from_site')}m")

    def run_async(self, func):
        def runner():
            try:
                func()
            except ApiError as exc:
                if exc.status_code in {401, 403}:
                    self.store.clear()
                    Clock.schedule_once(lambda *_: self.show_login())
                    self.set_status("Session expired. Please login again.")
                    return
                self.set_status(f"API error: {exc.message}")
            except Exception as exc:
                self.set_status(f"Error: {exc}")

        threading.Thread(target=runner, daemon=True).start()

    def set_status(self, message):
        Clock.schedule_once(lambda *_: setattr(self.status, "text", message))


class PowerAuditApp(App):
    def build(self):
        self.title = "Power Audit"
        return PowerAuditRoot()


if __name__ == "__main__":
    PowerAuditApp().run()
