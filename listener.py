import serial
import requests
import time

# --- Configuration ---
SERIAL_PORT = 'COM3'  # Change this to match your Arduino's port
BAUD_RATE = 115200
PHP_ENDPOINT = 'http://localhost/clocking-system/upload.php'  # Endpoint to send data

# --- Start serial connection ---
ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=1)
time.sleep(2)  # Wait for Arduino to reset and connect

print("Listening for card data from Arduino...")

try:
    while True:
        if ser.in_waiting > 0:
            uid_line = ser.readline().decode('utf-8', errors='ignore').strip()
            if uid_line.startswith("UID Value:"):
                uid = uid_line.split(": ")[1].replace(" ", "")
                print(f"UID received: {uid}")

                # Send UID to PHP for database storage
                payload = {'uid': uid}
                response = requests.post(PHP_ENDPOINT, data=payload)

                if response.status_code == 200:
                    try:
                        result = response.json()
                        if result['status'] == 'success':
                            print(f"✅ Employee {result['name']} (ID: {result['employee_id']}) clocked in successfully!")
                        else:
                            print(f"⚠️ Error: {result['message']}")
                    except requests.exceptions.JSONDecodeError:
                        print("⚠️ Server response was not valid JSON")
                else:
                    print(f"⚠️ Failed to send UID to PHP. Status code: {response.status_code}")

except KeyboardInterrupt:
    print("Listener stopped.")
    ser.close()
