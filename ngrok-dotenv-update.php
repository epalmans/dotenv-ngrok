<?php
if (!isset($argv[1]) || strtolower(substr($argv[1], -4))!=".env")
	die('Specify .env filepath as argument');

$envFile = $argv[1];
if (!file_exists($envFile))
	die('Specified .env file not found');

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:4040/api/tunnels");
$result = curl_exec($ch);
if (curl_errno($ch))
  die("Error accessing local ngrok api: \"" . curl_error($ch) . "\"");
else
	curl_close($ch);

$ngrokUrls = [];
$envngrokPrefix = "NGROK_";
foreach(json_decode($result, true)["tunnels"] as $tunnel) {
	$ngrokUrls["NGROK_".strtoupper($tunnel['proto'])] = $tunnel['public_url'];
}

if (count($ngrokUrls) == 0)
	die('No ngrok endpoints');

$fh = fopen($envFile,'r+');

$newEnvConfig = '';
while(!feof($fh)) {
		$envLine = trim(fgets($fh));
    $envKey = explode('=', $envLine, 2)[0];

		if (substr($envKey, 0, strlen($envngrokPrefix))==$envngrokPrefix && array_key_exists($envKey, $ngrokUrls)) {
			$envLine = "{$envKey}={$ngrokUrls[$envKey]}";
			echo "{$envLine} set in {$envFile}\n";
		}

    $newEnvConfig .= $envLine . "\r\n";
}

file_put_contents($envFile, $newEnvConfig);

fclose($fh);