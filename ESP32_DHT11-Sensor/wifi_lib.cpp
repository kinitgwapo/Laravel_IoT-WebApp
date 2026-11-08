#include "wifi_lib.h"

#include "WiFi.h"

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