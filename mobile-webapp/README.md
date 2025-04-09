# Simple Clocking System Web App

This is a minimal web app that displays a static UID and sends it to the server when tapped on an NFC reader.

## Features

- Displays a static UID (743E0304)
- Sends the UID to the server when tapped on the NFC reader
- Works with the existing Python listener script

## Requirements

- A web server with PHP support (Apache, Nginx, etc.)
- A device with NFC capabilities (most modern Android phones)
- Chrome browser on Android (for Web NFC API support)
- ngrok (for HTTPS access, required for Web NFC API)

## Installation

1. Copy the `mobile-webapp` folder to your web server's root directory
2. Make sure the `uid.txt` file exists and is writable
3. Access the app through your web browser at `http://your-server/mobile-webapp/`

## Setting Up ngrok for HTTPS Access

Since the Web NFC API requires HTTPS, you'll need to use ngrok to create a secure tunnel:

1. **Install ngrok** from https://ngrok.com/download
2. **Sign up for a free ngrok account** at https://dashboard.ngrok.com/signup
3. **Get your authtoken** from the ngrok dashboard
4. **Authenticate ngrok** with your authtoken:
   ```
   ngrok config add-authtoken YOUR_AUTH_TOKEN
   ```
5. **Start your local web server** (XAMPP, etc.)
6. **Create an ngrok tunnel** to your local server:
   ```
   ngrok http 80
   ```
   (Use port 80 if that's what your web server is using)
7. **Copy the HTTPS URL** provided by ngrok (e.g., `https://abc123def456.ngrok.io`)
8. **Access your web app** through the ngrok URL followed by `/mobile-webapp/` (e.g., `https://abc123def456.ngrok.io/mobile-webapp/`)

## Usage

1. Open the web app on your phone using Chrome
2. Enable NFC on your phone
3. Tap your phone on the NFC reader
4. The app will send your UID to the server
5. The server will process the UID just like it would with an RFID card

## Customization

To change the static UID, edit the `STATIC_UID` constant in the `index.html` file:

```javascript
const STATIC_UID = "743E0304"; // Change this to your desired UID
```

## Troubleshooting

- Make sure NFC is enabled on your device
- Make sure you're using Chrome on Android
- Make sure your device has NFC hardware
- Check the browser console for errors
- If using ngrok, make sure the tunnel is active and you're using the HTTPS URL
- If the NFC API isn't working, check that you're accessing the site via HTTPS

## License

This project is licensed under the MIT License - see the LICENSE file for details. 