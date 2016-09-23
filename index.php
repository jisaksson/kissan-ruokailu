<?php
error_reporting(E_ALL);


//Declare some variables
//Allowed time between meals (hours)
$mealtime = 4;

$where = array(
		'0' => "Cupboard",
		'1' => "Fridge"
);


//Function for checking that cat is fed properly
function tummy_full(PDO $pdo) {
	global $mealtime;
	//Get latest meal time
	$stmt = $pdo->prepare("SELECT * FROM ruokailu ORDER BY r_id DESC LIMIT 1");
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
	//Format timestamo
	$ts = new DateTime($result['timestamp']);
	$date = $ts->format('d.m.');
	$time = $ts->format('H:i');
	
	//Declare wake hours / feeding hours etc.
	$morning = new DateTime('07:00');
	$morning = $morning->format('H:i');
	$night	 = new DateTime('23:59');
	$night	 = $night->format('H:i');
	$now = new DateTime('NOW');
	$nowhi = $now->format('H:i');
	
	//Interval between now and last meal
	$interval = $ts->diff(new DateTime("NOW"));
	$minutedif = $interval->h * 60;
	$minutedif += $interval->i;
	
	//Check if cat/dog is fed in mannerly time
	if($time > $morning && $time < $night && round($minutediff) < $mealtime){
		return array(true, $time);
	}else{
		return array(false, $time);
	}
	
}

//SQL Connect
$dsn = "mysql:host=localhost;dbname=voldemort;charset=utf8";
$opt = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);
$pdo = new PDO($dsn, '', '', $opt);

session_start();

//Fetch all meal times
$stmt = $pdo->prepare("SELECT * FROM ruokailu ORDER BY r_id DESC");
$stmt->execute();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Voldemortin ruokailuajat">
    <meta name="author" content="@JJIsaksson">
    <link rel="icon" href="http://v4-alpha.getbootstrap.com/favicon.ico">

    <title>Voldemort</title>

    <!-- Bootstrap core CSS -->
    <link href="src/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="src/dashboard.css" rel="stylesheet">
  <style type="text/css">
:root #content > #center > .dose > .dosesingle,
:root #content > #right > .dose > .dosesingle
{ display: none !important; }</style><script>try {  for(var lastpass_iter=0; lastpass_iter < document.forms.length; lastpass_iter++){    var lastpass_f = document.forms[lastpass_iter];    if(typeof(lastpass_f.lpsubmitorig)=="undefined"){      if (typeof(lastpass_f.submit) == "function") {        lastpass_f.lpsubmitorig = lastpass_f.submit;        lastpass_f.submit = function(){          var form = this;          try {            if (document.documentElement && 'createEvent' in document)            {              var forms = document.getElementsByTagName('form');              for (var i=0 ; i<forms.length ; ++i)                if (forms[i]==form)                {                  var element = document.createElement('lpformsubmitdataelement');                  element.setAttribute('formnum',i);                  element.setAttribute('from','submithook');                  document.documentElement.appendChild(element);                  var evt = document.createEvent('Events');                  evt.initEvent('lpformsubmit',true,false);                  element.dispatchEvent(evt);                  break;                }            }          } catch (e) {}          try {            form.lpsubmitorig();          } catch (e) {}        }      }    }  }} catch (e) {}</script>
  </head>

  <body>

    <nav class="navbar navbar-dark navbar-fixed-top bg-inverse">
      <button type="button" class="navbar-toggler hidden-sm-up" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Voldemort</a>
      <div id="navbar">
        <nav class="nav navbar-nav pull-xs-left">
          <a class="nav-item nav-link" href="#">Dashboard</a>
        </nav>
      </div>
    </nav>
	 <?php
	  $tummy = tummy_full($pdo);
	  if($tummy[0]) {
	 	echo '<div class="alert alert-success" role="alert">
			<strong>Cat feeded!</strong> Don\'t worry, last meal was @ '.$tummy[1].'!
		</div>';
	  }else {
		echo '<div class="alert alert-danger" role="alert">
			<strong>Feed your cat now!</strong> You fed the cat previously @ '.$tummy[1].'!
		</div>';
	    
	  }
	  ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12 main">
          <h1>Voldemortin ruokailu</h1>
			<p>
			<?php 
				tummy_full($pdo); 
			?>
			</p>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Kellonaika</th>
                  <th>Päivä</th>
                  <th>Kaappi</th>
                </tr>
              </thead>
              <tbody> 
                <?php
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $date = new DateTime($row['timestamp']);
                  echo "<tr>";
                  echo '<td>'.$row['r_id'].'</td><td>'.$date->format('H:i').'</td><td>'.$date->format('d.m.').'</td><td>'.($row['kaappi'] == 0 ? 'Jääkaappi' : 'Kuivakaappi').'</td>'; //etc...
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="src/jquery.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="src/bootstrap.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="src/ie10-viewport-bug-workaround.js"></script>
  

</body></html>