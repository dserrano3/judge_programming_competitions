<?php
$user = "***USER***";     //CHANGE THIS LINE
$password = "***PASSWORD***"; //CHANGE THIS LINE
$db = "***DATA_BASE***"; //CHANGE THIS LINE

$url_db = "127.0.0.1"; //Only change this if the DB is not local.

//TODO: warning if not set.


//This method should be used only to get queries, in general it shouldnt be modified.
function connection_query(){
	//Change the scope of the variables.
	global $user, $password, $db, $url_db;

	//Connect to the database.
	$con = mysqli_connect($url_db,$user,$password, $db);

	if (mysqli_connect_errno()) {
 		 echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	//We return the connection.
	return $con;


}

function connection_update(){
  global $user, $password, $db, $url_db;
  $con = mysql_connect($url_db,$user,$password);
  if (!$con)
  {
	die('Could not connect: ' . mysql_error());
  }

  mysql_select_db($db)or die("cannot select DB");

  if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  return $con;


}




?>
