<?php

$username = $_GET['username'];
$password = $_GET['password'];

$user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36";

// Try get csrf_token
$url = "https://www.instagram.com/";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
curl_setopt($ch, CURLOPT_HTTPHEADER, array());
curl_setopt($ch, CURLOPT_REFERER, "https://www.instagram.com/accounts/login/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie_".$username.".txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie_".$username.".txt");
$data = curl_exec($ch);
curl_close($ch);
$csrf_token  = "";
$cookie_file = "cookie_".$username.".txt";
$cookie_data = file_get_contents($cookie_file);
preg_match("/instagram.com\s+TRUE\s+\/\s+TRUE\s+\d+\s+csrftoken\s+([^\s]+)/", $cookie_data, $matches);
if (isset($matches[1])) {
    $csrf_token = $matches[1];
} else {
     die("Falha ao obter token");
}


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
    "x-csrftoken: ${csrf_token}",
    "x-ig-www-claim: 0",
    "x-ig-www-claim: 0",
    "x-instagram-ajax: 1007167139",
    "x-requested-with: XMLHttpRequest"
));
curl_setopt($ch, CURLOPT_REFERER, "https://www.instagram.com/accounts/login/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie_".$username.".txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie_".$username.".txt");
$data = curl_exec($ch);
curl_close($ch);

echo $data;

//$json = json_decode($data, true);
//$checkpointUrl = $json['checkpoint_url'];
//echo $checkpointUrl;
//file_get_contents($checkpointUrl);

?>
