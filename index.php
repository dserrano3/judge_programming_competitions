<html>

imprimiendo

<?php 
	
	$returnCode = "before";
	exec("java -jar Discant_java.jar", $returnCode);
	print_r($returnCode); 
	
?>



</html>
