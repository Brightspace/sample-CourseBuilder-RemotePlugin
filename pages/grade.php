<?php
  require_once('../util/lti_util.php');

  session_start();
  if(isset($_REQUEST['lis_outcome_service_url'])) $_SESSION['lis_outcome_service_url'] = $_REQUEST['lis_outcome_service_url'];
  if(isset($_REQUEST['lis_result_sourcedid'])) $_SESSION['lis_result_sourcedid'] = $_REQUEST['lis_result_sourcedid'];
  if(isset($_REQUEST['oauth_consumer_key'])) $_SESSION['oauth_consumer_key'] = $_REQUEST['oauth_consumer_key'];
  if(isset($_REQUEST['lis_person_name_given'])) $_SESSION['lis_person_name_given'] = $_REQUEST['lis_person_name_given'];
  session_write_close();

  $oauth_consumer_key = $_SESSION['oauth_consumer_key'];
  $endpoint = $_SESSION['lis_outcome_service_url'];
  $sourcedid = $_SESSION['lis_result_sourcedid'];
  $user = $_SESSION['lis_person_name_given'];

  $oauth_consumer_secret = 'secret';

  $method = 'POST';
  $content_type = 'application/xml';
  if(isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    if($action == 'read') {
      $grade = 'Not set';
      $operation = 'readResultRequest';
      $postBody = str_replace(
        array('SOURCEDID', 'OPERATION', 'MESSAGE'),
        array($sourcedid, $operation, uniqid()),
        getPOXRequest());
      $response = parseResponse(sendOAuthBodyPOST($method, $endpoint, $oauth_consumer_key, $oauth_consumer_secret, $content_type, $postBody));
      if($response['imsx_codeMajor'] == 'success' && $response['textString'] != '') {
        $grade = $response['textString'];
      }
    } else if($action == 'write') {
      $grade = $_REQUEST['newgrade'];
      $operation = 'replaceResultRequest';
      $postBody = str_replace(
        array('SOURCEDID', 'GRADE', 'OPERATION', 'MESSAGE'),
        array($sourcedid, $grade, $operation, uniqid()),
        getPOXGradeRequest());
      $response = parseResponse(sendOAuthBodyPOST($method, $endpoint, $oauth_consumer_key, $oauth_consumer_secret, $content_type, $postBody));
      if($response['imsx_codeMajor'] == 'success') {
        $grade = 'success';
      } else {
        $grade = 'failure';
      }
    } else if($action = 'delete') {
      $operation = 'deleteResultRequest';
      $postBody = str_replace(
        array('SOURCEDID', 'OPERATION','MESSAGE'),
        array($sourcedid, $operation, uniqid()),
        getPOXRequest());
      $response = parseResponse(sendOAuthBodyPOST($method, $endpoint, $oauth_consumer_key, $oauth_consumer_secret, $content_type, $postBody));
    }
  }
?>

<!doctype html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device.width, initial-scale=1.0">

  <title>Grade Test Page</title>

  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script>
    function reenable() {

      var ins = document.getElementsByTagName('input');
      for(var i = 0; i < ins.length; i++) {
        ins[i].disabled = false;
      }
      document.getElementById('submit').disabled = false;
    }
  </script>

  <style>
    @media (min-width:990px) {
      body {
        padding-top: 60px;
      }
    }
  </style>
</head>

<body>

  <div class="container">


    <p>Authenticated as <?php echo $user;?></p>
<?php
  if($action == 'read') {
    echo '<h4>Result</h4>';
    echo '<p>Your grade is "'.$grade.'"</p>';
  } else if($action == 'write') {
    echo '<h4>Result</h4>';
    echo '<p>Setting grade: "'.$grade.'"</p>';
    if($grade == 'failure') {
      echo '<pre>';
      print_r($response);
      echo '</pre>';
    }
  } else if($action == 'delete') {
    echo '<h4>Result</h4>';
    echo '<p>Grade deleted</p>';
  }
?>
    <h4>Action</h4>
    <form action="?" method="post" enctype="multipart/form-data">
      <fieldset>
        <input type="text" name="newgrade">

        <label class="radio">
          <input type="radio" name="action" value="read" checked>
          Read
        </label>

        <label class="radio">
          <input type="radio" name="action" value="write">
          Write
        </label>


        <label class="radio">
          <input type="radio" name="action" value="delete">
          Delete
        </label>

        <br><br>
        <button id="submit" type="submit" class="btn">Submit</button>

      </fieldset>
    </form>
  </div>
</body>
