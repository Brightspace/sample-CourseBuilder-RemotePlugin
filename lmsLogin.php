<?php
require_once 'libsrc/D2LAppContextFactory.php';
require_once 'libsrc/D2LHostSpec.php';
require_once 'config.php';

session_start();
$lmsUrl = parse_url($_SESSION['lmsUrl']);

// Construct our URL that will be used for auth redirect
if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
  $scheme = 'https';
} else {
  $scheme = 'http';
}

$serverPort = $_SERVER['SERVER_PORT'];
$port = "";
if(($scheme == 'http' && $serverPort != 80) || ($scheme == "https" && $serverPort != 443)) {
	$port = ":$serverPort";
}

$myUrl = $scheme . '://' . $_SERVER["SERVER_NAME"] . $port . $_SERVER["REQUEST_URI"];

// Create auth security context and user context
$authContextFactory = new D2LAppContextFactory();
$authContext = $authContextFactory->createSecurityContext($config['appId'], $config['appKey']);
$hostSpec = new D2LHostSpec($lmsUrl['host'], $lmsUrl['port'], $lmsUrl['scheme']);
$opContext = $authContext->createUserContextFromHostSpec($hostSpec, null, null, $myUrl);

if($opContext != null) {
  // We have everything we need to create user context.  Go to the main page after saving user API key and ID.
  $_SESSION['apiUserId'] = $opContext->getUserId();
  $_SESSION['apiUserKey'] = $opContext->getUserKey();
  header("Location: contentSelector.php");
} else {
  // Do the LMS auth.
  $url = $authContext->createUrlForAuthenticationFromHostSpec($hostSpec, $myUrl);
  header("Location: $url");
}
?>
