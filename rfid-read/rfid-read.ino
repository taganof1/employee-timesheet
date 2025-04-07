// Both necessary libraries
#include <Wire.h>
#include <Adafruit_PN532.h>

#define PN532_IRQ   (2)
#define PN532_RESET (3)  // Only needed for SPI mode, not necessary for I2C mode

Adafruit_PN532 nfc(PN532_IRQ, PN532_RESET);

void setup(void) {
  Serial.begin(115200);
  while (!Serial);
  Serial.println("Initializing PN532 NFC reader in I2C mode...");

  nfc.begin();

  uint32_t versiondata = nfc.getFirmwareVersion();
  if (!versiondata) {
    Serial.println("Didn't find PN532 board. Check connections and I2C mode.");
    while (1); // Stop Program
  }

  // Version info
  Serial.print("Found PN532 with firmware version: ");
  Serial.print((versiondata >> 16) & 0xFF, DEC);
  Serial.print('.');
  Serial.println((versiondata >> 8) & 0xFF, DEC);

  nfc.SAMConfig(); // Read RFID tags
  Serial.println("Waiting for an NFC card or smartphone...");
}

void loop(void) {
  uint8_t uid[7]; // UID buffer 
  uint8_t uidLength;

  if (nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength)) {
    Serial.print("Card detected with UID: ");
    for (uint8_t i = 0; i < uidLength; i++) {
      Serial.print(uid[i] < 0x10 ? "0" : "");
      Serial.print(uid[i], HEX);
    }
    Serial.println();
    delay(1000);
  }
}
