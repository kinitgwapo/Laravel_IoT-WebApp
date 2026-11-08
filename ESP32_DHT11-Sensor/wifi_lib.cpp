#include "wifi_lib.h"
#include "Arduino.h"
#include "WiFi.h"
#include "time.h"
#include "HTTPClient.h"

static void wifi_mode_config();
static void wifi_sta_connect();
static void time_config();
static void freertos_task_init();
static void http_data_send_task(void *pvParameters);

#define WIFI_SSID "YOUR_WIFI_SSID_NAME"
#define WIFI_PASS "YOUR_WIFI_PASSWORD"
#define WEBAPP_POST_URL "YOUR_WEBAPP_POST_URL"
#define IOT_DEVICE_TOKEN "YOUR_IOT_DEVICE_TOKEN"
#define DEVICE_ID "YOUR_PREFERRED_DEVICE_ID_NAME"
#define HTTP_READ_TIMEOUT 5000

#define TASK_INTERVAL 30000
#define HTTP_SEND_TASK_STACK 4096

typedef enum
{
  CODE_FAIL = 0,
  CODE_SUCCESS
} status_code_t;

void wifi_setup()
{
  Serial.begin(115200);
  randomSeed(micros());

  wifi_mode_config();
  wifi_sta_connect();
  time_config();

  freertos_task_init();
}

static void wifi_mode_config()
{
  status_code_t result = WiFi.mode(WIFI_STA) ? CODE_SUCCESS : CODE_FAIL;
  while(result == CODE_FAIL)
  {
    Serial.println("Failed to set WiFi mode, retrying...");
    result = WiFi.mode(WIFI_STA) ? CODE_SUCCESS : CODE_FAIL;
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

  Serial.print("\nESP32's DHCP-assigned IP Address is ");
  Serial.println(WiFi.localIP());
}

static void time_config()
{
  configTzTime("PHT-8", "pool.ntp.org", "time.google.com");

  struct tm timeinfo;
  status_code_t result = getLocalTime(&timeinfo, 15000) ? CODE_SUCCESS : CODE_FAIL;
  if(result == CODE_FAIL)
  {
    Serial.println("NTP synchronization failed, restarting the board...");
    delay(1000);
    ESP.restart();
  }

  Serial.println(&timeinfo, "%Y-%m-%d %H:%M:%S");
}

static void data_send()
{
  if(WiFi.status() != WL_CONNECTED)
  {
    Serial.println("WiFi disconnected");
    return;
  }

  float temperature = random(-4000, 8001) / 100.0;
  float humidity = random(0, 10001) / 100.0;
  struct tm timeinfo;
  char timestamp[26];

  if (!getLocalTime(&timeinfo, 2000)) {
    Serial.println("Time not synchronized; reading skipped.");
    return;
  }
  strftime(timestamp, sizeof(timestamp), "%Y-%m-%dT%H:%M:%S+08:00", &timeinfo);

  HTTPClient http;
  status_code_t result = http.begin(WEBAPP_POST_URL) ? CODE_SUCCESS : CODE_FAIL;
  if(result == CODE_FAIL)
  {
    Serial.println("Failed to initialize HTTP Client");
    return;
  }
  String authorization = "Bearer " + IOT_DEVICE_TOKEN;
  http.addHeader("Authorization", authorization);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");

  String body = "{";
  body += "\"device_id\":\"" DEVICE_ID "\",";
  body += "\"temperature\":" + String(temperature, 2) + ",";
  body += "\"humidity\":" + String(humidity, 2) + ",";
  body += "\"recorded_at\":\"" + String(timestamp) + "\"";
  body += "}";

  http.setTimeout(HTTP_READ_TIMEOUT);

  int httpCode = http.POST(body);
  Serial.println("Temperature: " + String(temperature, 2) + "\tHumidity: " + String(humidity, 2));
  Serial.println("HTTP Status Code: " + String(httpCode));
  if(httpCode > 0)
  {
    Serial.println("Laravel Response:");
    Serial.println(http.getString());
  }
  else
  {
    Serial.println("HTTP Client Error:");
    Serial.println(http.errorToString(httpCode));
  }

  http.end();
}

static void freertos_task_init()
{
  status_code_t result = xTaskCreate(
    http_data_send_task,
    "Data Send Task",
    HTTP_SEND_TASK_STACK,
    NULL,
    1,
    NULL
  ) == pdPASS ? CODE_SUCCESS : CODE_FAIL;
  if(result == CODE_FAIL)
  {
    Serial.println("Failed to create HTTP data transmission task, restarting the board...");
    delay(1000);
    ESP.restart();
  }
}

static void http_data_send_task(void *pvParameters)
{
  TickType_t previousWakeTime = xTaskGetTickCount();

  while(true)
  {
    data_send();
    vTaskDelayUntil(&previousWakeTime, pdMS_TO_TICKS(TASK_INTERVAL));
  }
}