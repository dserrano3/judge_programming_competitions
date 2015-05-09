<?php
	include "config.php";
	$con = connection_update();

$nombre = $_POST["nombre"];
$usuario = $_POST["usuario"];
$pass = sha1($_POST["pass"]);
$colegio = $_POST["colegio"];


  $sql = "INSERT INTO Usuario (nombre, usuario, password, colegio)
  VALUES ('$nombre', '$usuario','$pass', '$colegio')";

$retval = mysql_query( $sql );
?>


<?php 
$html = file_get_contents('header.html');
echo $html;

?>
          <h2>Crear Usuario</h2>
          <p>


					<?php

					if(! $retval )
					{
					  die('Problema con la insercion, es probable que el usuario ya exista: ' . mysql_error());
					}

					echo 'Usuario insertado con exito.'


					?>

          </p>


        </div>
      </div>
    </div>
    <div class="cleaner">&nbsp;</div>
  </div>
</div>
<div align=center>Juez creado por: Daniel Serrano, Lenguaje creado por Alfredo Santamaria y Daniel Serrano</div></body>
</html>


