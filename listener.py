import serial
import requests
import time
import json
import os

# --- Configuration ---
SERIAL_PORT = 'COM3'  # Change this to match your Arduino's port
BAUD_RATE = 115200
CLOCK_IN_ENDPOINT = 'http://localhost/clocking-system/api/upload.php'  # Endpoint for clock-in
CLOCK_OUT_ENDPOINT = 'http://localhost/clocking-system/api/clock_out.php'  # Endpoint for clock-out
MODE_FILE = 'current_mode.txt'  # File to read the current mode from

# --- Start serial connection ---
ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=1)
time.sleep(2)  # Wait for Arduino to reset and connect

print("Listening for card data from Arduino...")
print("Press Ctrl+C to stop")

# Default to clock-in mode
current_mode = "clockIn"

# Function to read the current mode from the file
def read_mode():
    global current_mode
    try:
        if os.path.exists(MODE_FILE):
            with open(MODE_FILE, 'r') as f:
                mode = f.read().strip()
                if mode in ["clockIn", "clockOut"] and mode != current_mode:
                    current_mode = mode
                    print(f"Mode changed to: {current_mode}")
                    # Send mode to Arduino
                    ser.write(f"MODE:{current_mode}\n".encode())
    except Exception as e:
        print(f"Error reading mode file: {e}")

try:
    while True:
        # Check for mode changes
        read_mode()
        
        if ser.in_waiting > 0:
            data_line = ser.readline().decode('utf-8', errors='ignore').strip()
            
            # Check if this is a mode change command from Arduino
            if data_line.startswith("MODE:"):
                mode = data_line.split(":")[1].strip()
                if mode in ["clockIn", "clockOut"]:
                    current_mode = mode
                    print(f"Mode changed to: {current_mode}")
                continue
                
            # Process UID data
            if data_line.startswith("UID Value:"):
                uid = data_line.split(": ")[1].replace(" ", "")
                print(f"UID received: {uid}")

                # Determine which endpoint to use based on current mode
                endpoint = CLOCK_IN_ENDPOINT if current_mode == "clockIn" else CLOCK_OUT_ENDPOINT
                print(f"Sending to {endpoint} in {current_mode} mode")

                # Send UID to PHP for database storage
                if current_mode == "clockIn":
                    # For clock-in, use form data
                    payload = {'uid': uid}
                    print(f"Sending clock-in request with payload: {payload}")
                    response = requests.post(endpoint, data=payload)
                else:
                    # For clock-out, use form data (same as clock-in)
                    payload = {'uid': uid}
                    print(f"Sending clock-out request with payload: {payload}")
                    response = requests.post(endpoint, data=payload)

                print(f"Response status code: {response.status_code}")
                print(f"Response content: {response.text}")

                if response.status_code == 200:
                    try:
                        result = response.json()
                        if result['status'] == 'success':
                            if current_mode == "clockIn":
                                print(f"✅ Employee {result['name']} (ID: {result['employee_id']}) clocked in successfully!")
                            else:
                                print(f"✅ Employee {result['data']['employeeName']} (ID: {result['data']['employeeId']}) clocked out successfully!")
                                print(f"   Clocked in at: {result['data']['clockedInAt']}")
                                print(f"   Clocked out at: {result['data']['clockedOutAt']}")
                        else:
                            print(f"⚠️ Error: {result['message']}")
                    except requests.exceptions.JSONDecodeError:
                        print("⚠️ Server response was not valid JSON")
                else:
                    print(f"⚠️ Failed to send UID to PHP. Status code: {response.status_code}")
        
        # Small delay to prevent CPU overuse
        time.sleep(0.1)

except KeyboardInterrupt:
    print("Listener stopped.")
    ser.close()
