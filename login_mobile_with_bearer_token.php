<?php

header('Access-Control-Allow-Origin: *');
$username = "username";
$password = "password";

// keep the same user agents to use in another requests...
$user_agent = "Instagram 298.0.0.19.114 (iPhone12,1; iOS 16_6; pt_BR; pt; scale=2.00; 828x1792; 509296496)";

$login_status = "fail";
// Login request
$g = round(microtime(true) * 1000); // time aleatorio
$body = "enc_password=#PWD_INSTAGRAM_BROWSER:0:{$g}:{$password}&username={$username}";
$url = "https://www.instagram.com/api/v1/web/accounts/login/ajax/";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "accept: *",
    "content-type: application/x-www-form-urlencoded",
    "x-csrftoken: 0",
    "x-ig-www-claim: 0",
    "x-ig-www-claim: 0",
    "x-instagram-ajax: 1007167139",
    "x-requested-with: XMLHttpRequest"
)
);
curl_setopt($ch, CURLOPT_REFERER, "https://www.instagram.com/accounts/login/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"));
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);

// Get all the header information
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($data, 0, $headerSize);

// Separate header lines and create a JSON object
$headerLines = explode("\r\n", $header);
$headerJson = [];

foreach ($headerLines as $line) {
    $parts = explode(": ", $line, 2);
    if (count($parts) === 2) {
        $headerJson[$parts[0]] = $parts[1];
    }
}

// Convert the array to JSON
$headerJson = json_encode($headerJson, JSON_PRETTY_PRINT);

// Display or use the JSON
//echo $headerJson;

//echo $data;
$data2 = json_decode($headerJson, true);

$specifiedKey = 'ig-set-authorization';
$user_id = 'ig-set-ig-u-ds-user-id';

$response_json = array(
    'login_status' => 'fail',
);

// Check if the key exists in the array and retrieve its value
if (isset($data2[$specifiedKey])) {
// success login
    $response_json['login_status'] = "ok";
    $response_json['session_id'] = base64_encode($data2[$specifiedKey]);
    $response_json['user_id'] = $data2[$user_id];
    file_put_contents("sucesso/$username", $data);
} else {
    $response_json['login_status'] = "fail";
    if (strpos($data, 'challenge') !== false) {
// fail due challenge
        $response_json['challenged'] = true;
    } else {
        $response_json['challenged'] = false;
    }

    if (strpos($data, 'two_factor')) {
// fail due two_factor
        $response_json['two_factor'] = true;
    } else {
        $response_json['two_factor'] = false;
    }

// return response
    $response_json['error'] = "Login fail \n\nDetails: " . $data;
    file_put_contents("erro/$username", $data);
}

echo json_encode($response_json);

curl_close($ch);

?>
