#include "wifi_lib.h"
#include "WiFi.h"

#define WIFI_SSID
#define WIFI_PASS

typedef enum
{
  SUCCESS = 0,
  FAIL
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