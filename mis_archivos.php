<?php
	include "config.php";
?>


<?php 
$html = file_get_contents('header.html');
echo $html;

?>
		 <div>
          <h2>Mis Envíos</h2>
          <p>
				<?php
					//Conectamos a base de datos.
					$con = connection_query();
					
					
					$usuario = $_POST["usuario"];
					$pass = sha1($_POST["pass"]);

					$result = mysqli_query($con, "SELECT usuario, password FROM Usuario WHERE usuario = '$usuario' AND password = '$pass'");
					$entro = false;

					while($row = mysqli_fetch_array($result)) {
					  $entro = true;
					}

					if(!$entro){
						die('El usuario o la contraseña son incorrectos ');
					}

					echo 'Problemas enviados por: ' . $usuario . '<br><br>';
					echo 'Si el problema fue enviado a tiempo ganara 10 puntos, de lo contrario ganara 5<br><br>';

					$result = mysqli_query($con, "SELECT archivo, usuario
													FROM archivo_solucion WHERE usuario = '$usuario'");

					while($row = mysqli_fetch_array($result)) {
						
						 echo print_r($row['archivo']);
						 echo '<br><br>';
						  echo '--------------------------------------------------------------------------------------<br><br>';
					}

					

					?>

          </p>
          </div>
      
<div align=center>Juez creado por: Daniel Serrano, Lenguaje creado por Alfredo Santamaria y Daniel Serrano</div></body>
</html>






