#include <Wire.h>
#include <Adafruit_PN532.h>

#define PN532_IRQ   (2)
#define PN532_RESET (3)  // Not always needed, but define it anyway depending on setup
#define BUTTON_PIN  (4)  // Button pin for mode switching

Adafruit_PN532 nfc(PN532_IRQ, PN532_RESET);

// Mode tracking
bool clockInMode = true;  // Default to clock-in mode
unsigned long lastButtonPress = 0;
const unsigned long DEBOUNCE_DELAY = 300;  // Debounce time in milliseconds

void setup(void) {
  Serial.begin(115200);
  while (!Serial);
  Serial.println("Initializing PN532 NFC reader in I2C mode...");

  // Set up button pin
  pinMode(BUTTON_PIN, INPUT_PULLUP);

  nfc.begin();

  uint32_t versiondata = nfc.getFirmwareVersion();
  if (!versiondata) {
    Serial.println("Didn't find PN532 board. Check connections and I2C mode.");
    while (1); // halt
  }

  // Display version info
  Serial.print("Found PN532 with firmware version: ");
  Serial.print((versiondata >> 16) & 0xFF, DEC);
  Serial.print('.');
  Serial.println((versiondata >> 8) & 0xFF, DEC);

  nfc.SAMConfig(); // Configure board to read RFID tags
  Serial.println("Waiting for an NFC card or smartphone...");
  
  // Send initial mode
  Serial.print("MODE:");
  Serial.println(clockInMode ? "clockIn" : "clockOut");
}

void loop(void) {
  // Check for button press to switch modes
  if (digitalRead(BUTTON_PIN) == LOW) {
    unsigned long currentTime = millis();
    if (currentTime - lastButtonPress > DEBOUNCE_DELAY) {
      lastButtonPress = currentTime;
      
      // Toggle mode
      clockInMode = !clockInMode;
      
      // Send mode change to Python script
      Serial.print("MODE:");
      Serial.println(clockInMode ? "clockIn" : "clockOut");
    }
  }

  uint8_t uid[7]; // Buffer to store UID
  uint8_t uidLength;

  if (nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength)) {
    Serial.print("UID Value: ");
    for (uint8_t i = 0; i < uidLength; i++) {
      Serial.print(uid[i] < 0x10 ? "0" : "");
      Serial.print(uid[i], HEX);
    }
    Serial.println();
    delay(1000);
  }
}
