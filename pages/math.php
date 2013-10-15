<!doctype html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device.width, initial-scale=1.0">
  <title>Introduction to Math Course Page</title>

  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script src="../js/bootstrap.min.js"></script>

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
        <a class="brand" href="#">&nbsp;MATH 101</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <li><a href="#link1">Quizzes</a></li>
            <li><a href="#link2">Videos</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="hero-unit">
      <h1>Introduction to Math</h1>
      <p>Welcome back, <?php echo $_POST['lis_person_name_given'], ' ', $_POST['lis_person_name_family'], '!';?></p>
    </div>
    <div class="row">
        <div class="span4">
            <!-- image credit: http://www.flickr.com/photos/jayakody2000lk/7216399294/ -->
            <img src="fractal.jpg" class="img-polaroid">
        </div>
        <div class="span8">
<h1>Fractals</h1>
<p>A fractal is a mathematical set that has a fractal dimension that usually exceeds its topological dimension and may fall between the integers. Fractals are typically self-similar patterns, where self-similar means they are "the same from near as from far". Fractals may be exactly the same at every scale, or, as illustrated in Figure 1, they may be nearly the same at different scales. The definition of fractal goes beyond self-similarity per se to exclude trivial self-similarity and include the idea of a detailed pattern repeating itself. [<a href="http://en.wikipedia.org/wiki/Fractal">From Wikipedia</a>]</p>

        </div>
    </div>
  </div>
</body>
