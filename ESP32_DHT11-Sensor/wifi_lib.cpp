#include "wifi_lib.h"
#include "WiFi.h"
#include "time.h"

static void wifi_mode_config();
static void wifi_sta_connect();
static void time_config();

#define WIFI_SSID "YOUR_WIFI_SSID_NAME"
#define WIFI_PASS "YOUR_WIFI_SSID_PASSWORD"

typedef enum
{
  FAIL = 0,
  SUCCESS
} status_code_t;

void wifi_setup()
{

}

static void wifi_mode_config()
{
  status_code_t result = WiFi.mode(WIFI_STA);
  while(result == FAIL)
  {
    Serial.println("Failed to set WiFi mode, retrying...");
    result = WiFi.mode(WIFI_STA);
    delay(100); 
  }

  Serial.println("Successfully set WiFi mode as \"Station\"");
}

static void wifi_sta_connect()
{
  WiFi.begin(WIFI_SSID, WIFI_PASS);
  Serial.print("Connecting");
  while(WiFi.status() != WL_CONNECTED)
  {
    Serial.print(".");
    delay(100);
  }

  Serial.println("ESP32's DHCP-assigned IP Address is " + WiFi.localIP());
}

static void time_config()
{
  configTzTime("PHT-8", "pool.ntp.org", "time.google.com");

  struct tm timeinfo;
  status_code_t result = getLocalTime(&timeinfo, 15000);
  if(result == FAIL)
  {
    Serial.println("NTP synchronization failed, restarting the board...");
    delay(1000);
    ESP.restart();
  }

  Serial.println(&timeinfo, "%Y-%m-%d %H:%M:%S");
}