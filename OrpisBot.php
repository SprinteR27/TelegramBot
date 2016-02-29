<?php
/**
 * Telegram Bot access token � URL.
 */
$access_token = '203318962:AAGmGjiyIGAF2pphUIduUmL6S0Bhe9yOZXE';
$api = 'https://api.telegram.org/bot' . $access_token;

/**
 * ����� �������� ����������.
 */
$output = json_decode(file_get_contents('php://input'), TRUE);
$chat_id = $output['message']['chat']['id'];
$first_name = $output['message']['chat']['first_name'];
$message = $output['message']['text'];

/**
 * Emoji ��� ������� ����������� ����������.
 */
$emoji = array(
  'preload' => json_decode('"\uD83D\uDE03"'), // ��������.
  'weather' => array(
    'clear' => json_decode('"\u2600"'), // ������.
    'clouds' => json_decode('"\u2601"'), // ������.
    'rain' => json_decode('"\u2614"'), // �����.
    'snow' => json_decode('"\u2744"'), // ����.
  ),
);

/**
 * �������� ������� �� ������������.
 */
switch($message) {
  // API ������ ������������� OpenWeatherMap.
  // @see http://openweathermap.org
  case '/pogoda':
    // ���������� �������������� �����.
    $preload_text = '���� �������, ' . $first_name . ' ' . $emoji['preload'] . ' � ������� ��� ��� ������..';
    sendMessage($chat_id, $preload_text);
    // App ID ��� OpenWeatherMap.
    $appid = '���_ID';
    // ID ��� ������/������/��������� (���� ��� ������ ��).
    $id = '2022890'; // ��� �������: ���������, ����� ������.
    // �������� JSON-����� �� OpenWeatherMap.
    $pogoda = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?appid=' . $appid . '&id=' . $id . '&units=metric&lang=ru'), TRUE);
    // ���������� ��� ������ �� ������ � ������� ��������������� Emoji.
    if ($pogoda['weather'][0]['main'] === 'Clear') { $weather_type = $emoji['weather']['clear'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Clouds') { $weather_type = $emoji['weather']['clouds'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Rain') { $weather_type = $emoji['weather']['rain'] . ' ' . $pogoda['weather'][0]['description']; }
    elseif ($pogoda['weather'][0]['main'] === 'Snow') { $weather_type = $emoji['weather']['snow'] . ' ' . $pogoda['weather'][0]['description']; }
    else $weather_type = $pogoda['weather'][0]['description'];
    // ����������� �������.
    if ($pogoda['main']['temp'] > 0) { $temperature = '+' . sprintf("%u", $pogoda['main']['temp']); }
    else { $temperature = sprintf("%u", $pogoda['main']['temp']); }
    // ����������� �����.
    if ($pogoda['wind']['deg'] >= 0 && $pogoda['wind']['deg'] <= 11.25) { $wind_direction = '��������'; }
    elseif ($pogoda['wind']['deg'] > 11.25 && $pogoda['wind']['deg'] <= 78.75) { $wind_direction = '������-���������, '; }
    elseif ($pogoda['wind']['deg'] > 78.75 && $pogoda['wind']['deg'] <= 101.25) { $wind_direction = '���������, '; }
    elseif ($pogoda['wind']['deg'] > 101.25 && $pogoda['wind']['deg'] <= 168.75) { $wind_direction = '���-���������, '; }
    elseif ($pogoda['wind']['deg'] > 168.75 && $pogoda['wind']['deg'] <= 191.25) { $wind_direction = '�����, '; }
    elseif ($pogoda['wind']['deg'] > 191.25 && $pogoda['wind']['deg'] <= 258.75) { $wind_direction = '���-��������, '; }
    elseif ($pogoda['wind']['deg'] > 258.75 && $pogoda['wind']['deg'] <= 281.25) { $wind_direction = '��������, '; }
    elseif ($pogoda['wind']['deg'] > 281.25 && $pogoda['wind']['deg'] <= 348.75) { $wind_direction = '������-��������, '; }
    else { $wind_direction = ' '; }
    // ������������ ������.
    $weather_text = '������ ' . $weather_type . '. ����������� �������: ' . $temperature . '�C. ����� ' . $wind_direction . sprintf("%u", $pogoda['wind']['speed']) . ' �/���.';
    // �������� ������ ������������ Telegram.
    sendMessage($chat_id, $weather_text);
    break;
  default:
    break;
}

/**
 * ������� �������� ��������� sendMessage().
 */
function sendMessage($chat_id, $message) {
  file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));
}