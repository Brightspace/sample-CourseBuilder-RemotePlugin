<?php
require_once 'libsrc/D2LAppContextFactory.php';
require_once 'config.php';

session_start();

$orgId = $_SESSION['orgId'];
$ou = $_SESSION['ou'];

$appKey = $config['appKey'];
$appId = $config['appId'];
$apiUserKey =  $_SESSION['apiUserKey'];
$apiUserId =  $_SESSION['apiUserId'];

$parentNode = $_SESSION['parentNode'];
$lmsUrl = parse_url( $_SESSION['lmsUrl'] );

$host = $lmsUrl['host'];
$port = $lmsUrl['port'];
$scheme = $lmsUrl['scheme'];


$linkHost = preg_replace('/\/([a-zA-Z0-9-_]*.php)?$/', '', $_SERVER['HTTP_REFERER']); // this should be done better in a real world application
$links = Array("math101" => Array("MATH 101: Introduction to Math", $linkHost."/pages/math.php"),
               "test" => Array("POST Test Page", $linkHost."/pages/print.php"),
               "grade" => Array("Grade Test Page", $linkHost."/pages/grade.php"),
               "whmis" => Array("WHMIS Training", $linkHost."/pages/whmis/index.php"));

$authContextFactory = new D2LAppContextFactory();
$authContext = $authContextFactory->createSecurityContext($appId, $appKey);
$hostSpec = new D2LHostSpec($host, $port, $scheme);
if ($authContext == null) {
  die("auth context is null");
}

$opContext = $authContext->createUserContextFromHostSpec($hostSpec, $apiUserId, $apiUserKey);
if ($opContext == null) {
  die("opContext is null");
}

function getData( $apiRoute, $opContext, &$returnData ) {
  $ch = curl_init();
  $options = array(
                   CURLOPT_RETURNTRANSFER => true,
                   CURLOPT_CAINFO => getcwd().'/cacert.pem'
  );
  curl_setopt_array($ch, $options);

  $uri = $opContext->createAuthenticatedUri($apiRoute, "GET");
  curl_setopt($ch, CURLOPT_URL, $uri);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  $responseCode = $opContext->handleResult($response, $httpCode, $contentType);

  if ($responseCode == D2LUserContext::RESULT_OKAY) {
    $returnData = $response;
  }

  return $httpCode;
}

function postData( $apiRoute, $data, $opContext, &$returnData ) {
  $ch = curl_init();
  $options = array(
                   CURLOPT_RETURNTRANSFER => true,
                   CURLOPT_CAINFO => getcwd().'/cacert.pem'
  );
  curl_setopt_array($ch, $options);

  $uri = $opContext->createAuthenticatedUri($apiRoute, "POST");
  curl_setopt($ch, CURLOPT_URL, $uri);

  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data))
  );
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  $responseCode = $opContext->handleResult($response, $httpCode, $contentType);

  if ($responseCode == D2LUserContext::RESULT_OKAY) {
    $returnData = $response;
  }

  return $httpCode;
}

function createLTILink( $title, $url ) {
  global $ou, $opContext, $parentNode;
  $dataArray = array(
    "Title" => $title,
    "Url" => $url,
    "Description" => "$title mydescription",
    "Key" => "",
    "PlainSecret" => "",
    "IsVisible" => true,
    "SignMessage" => true,
    "SignWithTc" => true,
    "SendTcInfo" => true,
    "SendContextInfo" => true,
    "SendUserId" => true,
    "SendUserName" => true,
    "SendUserEmail" => true,
    "SendLinkTitle" => true,
    "SendLinkDescription" => true,
    "SendD2LUserName" => true,
    "SendD2LOrgDefinedId" => true,
    "SendD2LOrgRoleId" => true,
    "CustomParameters" => Array(
      Array("Name" => "TimeCreated", "Value" => time()),
      Array("Name" => "AnotherCustomParameter", "Value" => "foobar")
    )
  );

  $data = json_encode($dataArray);

  $route = "/d2l/api/le/".$_SESSION["LEVersion"]."/lti/link/$ou";
  $code = postData( $route, $data, $opContext, $returnData );
  if($code != 200) {
    echo "POST to ".$route." failed!";
    exit();
  }

  $returnJson = json_decode( $returnData, true );
  $ltiLinkId = $returnJson['LtiLinkId'];

  $route = "/d2l/api/le/".$_SESSION["LEVersion"]."/lti/quicklink/$ou/$ltiLinkId";
  $code = postData( $route, $data, $opContext, $returnData );
  if($code != 200) {
    echo "POST to ".$route." failed!";
    exit();
  }

  $returnJson = json_decode( $returnData, true );
  $qlUrl = $returnJson[ 'PublicUrl' ];

  $dataArray = array (
    "TopicType" => 3,
    "Url" => $qlUrl,
    "StartDate" => null,
    "EndDate" => null,
    "IsHidden" => false,
    "IsLocked" => false,
    "Title" => $title,
    "ShortTitle" => "",
    "Type" => 1
  );

  $data = json_encode( $dataArray );

  postData( "/d2l/api/le/".$_SESSION["LEVersion"]."/$ou/content/modules/$parentNode/structure/", $data, $opContext, $returnData );
}

$code = getData("/d2l/api/versions/", $opContext, $versionData);
if($code != 200) {
  echo "Couldn't fetch versions from the LE. HTTP Status: ".$code;
  exit();
}
$versionData = json_decode($versionData);
foreach($versionData as $ver) {
  if($ver->ProductCode == 'le') {
    if(!in_array('1.3', $ver->SupportedVersions)) {
      echo 'LE does not support version 1.3; this plugin will not function correctly.';
      exit();
    }
    $_SESSION['LEVersion'] = $ver->LatestVersion;
  }
}
session_write_close();

foreach($_POST as $key => $value) {
  $title = $links[$key][0];
  $url = $links[$key][1];
  createLTILink($title, $url);
}

header('Location: '.$_SESSION['returnUrl']);
?>
