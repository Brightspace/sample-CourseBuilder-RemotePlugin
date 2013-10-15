<?php
require_once 'OAuth1p0.php';
require_once 'libsrc/D2LAppContextFactory.php';
require_once 'config.php';

// Find our URL
$url = 'http';
if ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) {
    $url .= 's';
}

$serverPort = $_SERVER['SERVER_PORT'];
$port = "";
if ( ( $url == 'http' && $serverPort != 80 ) || ( $url == "https" && $serverPort != 443 ) ) {
	$port = ":$serverPort";
}

$url .= '://' . $_SERVER["SERVER_NAME"] . $port . $_SERVER["REQUEST_URI"];

if ( !OAuth1p0::CheckSignatureForFormUrlEncoded( $url, 'POST', $_POST, $config['secret'] ) ){
    exit( "Invalid OAuth signature\n" );
}

if ( empty( $_POST[ 'user_id' ] ) ) {
    exit( "Missing user_id parameter" );
}

// Get user info from the LTI launch
$userId = $_POST[ 'user_id' ];

// D2L LTI launch sends user_id as <LMS installation code>_<LMS user ID>
$userId = substr( $userId, strpos( $userId, '_') + 1 );

$orgId = $_POST[ 'context_id' ];
$name = $_POST[ 'lis_person_name_full' ];

$returnUrl = parse_url( $_POST[ 'launch_presentation_return_url' ] );

$queryParams = array();
parse_str( $returnUrl[ 'query' ], $queryParams );

// we get the lms info from lis_outcome_service_url
$lmsUrl = parse_url($_POST['lis_outcome_service_url']);
if($lmsUrl['port'] == '') {
  $lmsUrl['port'] = $lmsUrl['scheme'] == 'http' ? 80 : 443;
}

// Record the user info
session_start();
$_SESSION['returnUrl'] = $_POST['launch_presentation_return_url'];
$_SESSION['orgId'] = $_POST['context_id'];
$_SESSION['lmsUrl'] = $lmsUrl['scheme'].'://'.$lmsUrl['host'].':'.$lmsUrl['port'];
$_SESSION['lmsUserId'] = $userId;
$_SESSION['name'] = $name;
$_SESSION['ou'] = $queryParams['ou'];
$_SESSION['parentNode'] = $queryParams['parentNode'];
$_SESSION['context_title'] = $_REQUEST['context_title'];
$_SESSION['context_label'] = $_REQUEST['context_label'];
session_write_close();

// Continue with LMS authentication
header("Location: lmsLogin.php");
?>
