<?php
include('functions.php');
sqlconnect();
include('auth.php');
sqlconnect();
if (($_GET['export'] == "Y") && ($usergroup == "admin")) {
	$file = "export/" . date("Ymd") . "-classificationExport.csv";
	$result = mysql_query("SELECT autox_numbers.drivernumber,wp_users.display_name,autox_classifications.car_year,autox_classifications.car_model,autox_classifications.points,autox_classifications.class,autox_classifications.pk FROM autox_classifications,wp_users,autox_numbers WHERE wp_users.user_login = autox_classifications.username and wp_users.user_login = autox_numbers.username and autox_classifications.active = 'Y' ORDER BY autox_numbers.drivernumber") or die("Error: " . mysql_error());
	$tds = $_GET[tds];
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$capitalizedname = ucwords($row[1]);
		$lastname = strstr($capitalizedname, " ");
		$firstname = strstr($capitalizedname, " ", true); // As of PHP 5.3.0
		//if ($row[2] == "") { $car = explode(" ", $row[3]);} else { $car[0] = $row[2]; $car[1] = $row[3];}
			if ($tds == "Y") {
				$export = $export . "\"$row[0]\",$firstname,$lastname,$row[2],$row[3],$row[4],$row[5]\n";
			} else {
				if (($row[5] == "Gonzo") && ($row[4] < 80)) { $points = "80"; } else { $points = $row[4]; }
				$export = $export . "\"$row[0]\",$firstname,$lastname,$row[2],$row[3],$points,$row[5]\n";
			}
		}
		file_put_contents($file, $export);
		header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename($file));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    ob_clean();
	    flush();
	    readfile($file);
	    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="initial-scale=1.0"> 
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link rel="apple-touch-icon" href="autoxicon.png" />
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/colorbox.css" rel="stylesheet" media="screen">
    <meta charset="UTF-8">
     <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <title>Show Cars</title>
    <link href="css/bootstrap-responsive.css" rel="stylesheet" media="screen">
        <style>
        body {
         	background-image: url('img/satinweave.png')  /*thanks SubtlePatterns.com */
        }
         @media (max-width: 979px) {
        	.navbar-fixed-top,
        	.navbar-fixed-bottom {
        		position: fixed;
       			margin-left: 0px;
        		margin-right: 0px;
      		}
      		.firstelement { padding-top: 60px; }
      	}
    </style>
</head>
<body>
<?php include('navbar.html');?>
<div class="container firstelement">
<?php
  $result = mysql_query("SELECT autox_numbers.drivernumber,wp_users.display_name,autox_classifications.car_year,autox_classifications.car_model,autox_classifications.points,autox_classifications.class,autox_classifications.pk FROM autox_classifications,wp_users,autox_numbers WHERE wp_users.user_login = autox_classifications.username and wp_users.user_login = autox_numbers.username and autox_classifications.active = 'Y' ORDER BY autox_classifications.points desc, autox_classifications.class, wp_users.display_name") or die("Error: " . mysql_error());
  	echo"<h4>All Classified Cars</h4>
	<table class='table table-condensed table-striped sortable' id='classifytable'>
	<thead>
	<tr><th>Number</th><th>Name</th><Th>Year</th><th>Model</th><th>Points</th><th>Class</th><th>Actions</th></tr>
	</thead><tbody>";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$capitalizedname = ucwords($row[1]);
		echo"<tr><td>$row[0]</td><Td>$capitalizedname</td><Td>$row[2]</td><td>$row[3]</td><Td>$row[4]</td><Td>$row[5]</td><Td><a href='show.php?id=$row[6]&amp;popup=Y'  class='carinfoajax btn'>View Details</a></td></tr>";
	}
	echo"</table>";
  ?>
</div>  <!--container-->
<?php include('bottombar.html');?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script src="js/sorttable.js"></script>
<script>
$(document).ready(function() {
//  $('#button'+activebutton).hide();
		$(".carinfoajax").colorbox();
}); 
</script>
</body></html>