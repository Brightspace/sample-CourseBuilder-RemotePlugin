<!doctype html>
<?php
  session_start();
  $contextLabel = $_SESSION['context_label'];
  $contextTitle = $_SESSION['context_title'];
  $name = $_SESSION['name'];
  session_write_close();
?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device.width, initial-scale=1.0">
  <title>LTI Example Tool</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script src="js/bootstrap.min.js"></script>

  <style>
    @media (min-width:990px) {
      body {
        padding-top: 60px;
      }
    }
  </style>
</head>
<body>
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="#">Company</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <li><a href="#link1">Feature Link 1</a></li>
            <li><a href="#link2">Feature Link 2</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <h3>Adding Links to <?php echo $contextLabel.': '.$contextTitle; ?></h3>
    <p>Welcome <?php echo $name; ?></p>
    <form action="createLinks.php" method="post">
      <fieldset>
        <legend>Select Links</legend>

        <dl>

          <dt><label class="checkbox"><input type="checkbox" name="math101"><strong>MATH 101: Introduction to Math</strong></label></dt>
          <dd>Page for external course "MATH 101"</dd>

          <dt><label class="checkbox"><input type="checkbox" name="whmis"><strong>WHMIS Training Quiz</strong></label></dt>
          <dd>Take a quiz about the Workplace Hazardous Materials Information System (WHMIS)</dd>

          <dt><label class="checkbox"><input type="checkbox" name="grade"><strong>Grade Test Page</strong></label></dt>
          <dd>Set, update and delete a grade</dd>

          <dt><label class="checkbox"><input type="checkbox" name="test"><strong>Test page</strong></label></dt>
          <dd>Test page that prints out diagnostic information.</dd>

        </dl>

        <button type="submit" class="btn">Submit</button>
      </fieldset>
    </form>
  </div>
</body>
