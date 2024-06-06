#include <SPI.h>
#include <MFRC522.h>
#include <ESP8266WiFi.h>

#define SS_PIN 15
#define RST_PIN 4
#define LED_BUILTIN 5

MFRC522 rfid(SS_PIN, RST_PIN);  // Objek MFRC522 untuk komunikasi RFID

const char *ssid = "edison";
const char *password = "1sampee8";

const char *server = "192.168.43.23"; // Alamat IP server atau nama domain
const int serverPort = 80;
const char *apiEndpoint = "/IoT_Project_RFID/api/create_data.php";

// Definisikan daftar pasangan ID tag dan nama
const int numTags = 3;  // Jumlah tag yang diketahui
String knownTags[numTags] = {"c3d10412", "833bba0d", "f5e54e65"}; // ID tag siswa
String knownNames[numTags] = {"Firman", "Andi", "Budi"};  // Nama yang sesuai

void setup() {
  Serial.begin(9600);     // Mulai serial monitor
  SPI.begin();            // Mulai bus SPI
  rfid.PCD_Init();        // Inisialisasi modul RFID
  pinMode(LED_BUILTIN, OUTPUT);  // Atur pin LED sebagai output

  // Sambungkan ke jaringan WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Sedang menghubungkan ke WiFI");
  }
  Serial.println("Terhubung ke WiFI");
}

void loop() {
  // Cek apakah ada tag yang terdeteksi
  if (rfid.PICC_IsNewCardPresent()) {
    Serial.println("Kartu RFID siswa terdeteksi");
    if (rfid.PICC_ReadCardSerial()) {
      Serial.println("No. RFID siswa terbaca");

      // Mendapatkan ID tag dalam bentuk hexa
      String tagID = "";
      for (byte i = 0; i < rfid.uid.size; i++) {
        tagID += (rfid.uid.uidByte[i] < 0x10 ? "0" : "");
        tagID += String(rfid.uid.uidByte[i], HEX);
      }
      Serial.println("No. RFID: " + tagID);

      if (tagID.length() == 0) {
        Serial.println("Error: UID is empty.");
      } else {
        // Cari nama yang sesuai dengan ID tag
        String name = "(Nama siswa tidak diketahui)";  // Default nama jika ID tidak dikenal
        for (int i = 0; i < numTags; i++) {
          if (tagID.equalsIgnoreCase(knownTags[i])) {
            name = knownNames[i];
            break;
          }
        }

        // Nyalakan LED sebagai indikasi tag terdeteksi
        digitalWrite(LED_BUILTIN, HIGH);

        // Kirim data ke server menggunakan HTTP POST
        if (sendDataToServer(tagID, name)) {
          Serial.println("Data terkirim ke server.");
        } else {
          Serial.println("Gagal mengirim data ke server.");
        }

        delay(500);  // Tunda selama 0.5 detik
        digitalWrite(LED_BUILTIN, LOW);  // Matikan LED
      }
    } else {
      Serial.println("Gagal membaca no. RFID");
    }
  }

  rfid.PICC_HaltA();  // Hentikan operasi saat ini
}

bool sendDataToServer(String tagID, String name) {
  WiFiClient client;
  if (!client.connect(server, serverPort)) {
    Serial.println("Gagal terhubung ke server.");
    return false;
  }

  // Membuat payload untuk dikirim ke API
  String payload = "{\"tag\":\"" + tagID + "\", \"nama\":\"" + name + "\"}";

  // Kirim permintaan POST ke API
  client.print(String("POST ") + apiEndpoint + " HTTP/1.1\r\n");
  client.print(String("Host: ") + server + "\r\n");
  client.print("Content-Type: application/json\r\n");
  client.print("Content-Length: " + String(payload.length()) + "\r\n");
  client.print("\r\n");
  client.print(payload);

  // Tunggu respons dari server
  while (client.connected() && !client.available()) {
    delay(10);
  }

  // Baca respons dari server
  String response = "";
  while (client.available()) {
    response += client.readStringUntil('\r');
  }
  client.stop();

  // Tampilkan respons server
  Serial.println("Respons dari Server:");
  Serial.println(response);

  // Cek respons untuk mengetahui apakah berhasil atau tidak
  if (response.indexOf("Data Created Successfully") != -1) {
    // Tampilkan data yang dikirim dalam bentuk JSON
    Serial.println("Data yang dikirim:");
    Serial.println("{");
    Serial.println("  \"tag\": \"" + tagID + "\",");
    Serial.println("  \"name\": \"" + name + "\"");
    Serial.println("}");
    return true;
  } else {
    return false;
  }
}
