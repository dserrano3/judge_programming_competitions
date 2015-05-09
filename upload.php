<?php
include "config.php";

$mensaje = 'sin cambiar';
function comparar_resultados($array1, $array2){
	global $mensaje;
	$array_bueno1 = array();
	$array_bueno2 = array();
	//EL 2 es el del usuario y el 1 es el que esta bien.
	for($i = 0; $i < count($array1); $i ++)
	{
		if(!empty($array1[$i])){
			array_push($array_bueno1, $array1[$i]);
		}
	}
	for($i = 0; $i < count($array2); $i ++)
	{
		if(!empty($array2[$i])){
			array_push($array_bueno2, $array2[$i]);
		}
	}
	//Si son diferentes se puede devolver false, no se hace para saber
	//donde esta la diferencia y poder mostrarla.
	if(count($array_bueno1) == 0){
		$mensaje = '<table style="background-color: #FF0000;"><tr><td> Hay un problema con este problema, contacte al administrador </td></tr></table>';
		return false;
	}
	if(count($array_bueno2) == 0){
		$mensaje = '<table style="background-color: #FF0000;"><tr><td>Tu codigo no imprime nada, verificalo </td></tr></table>';
		return false;
	}


	if(count($array_bueno2) != count($array_bueno1)) {
		$mensaje = '<table style="background-color: #FF0000;"><tr><td> La respuesta tiene más o menos lineas de lo que debería</td></tr></table>';
		return false;
	}
	for($i = 0; $i < count($array_bueno1); $i ++)
	{
		if($array_bueno1[$i] != $array_bueno2[$i] ){
			$mensaje = '<table style="background-color: #FF0000;"><tr><td> En alguna linea se está imprimiendo ';
			$mensaje .= $array_bueno2[$i] . " ";
			$mensaje .= "y deberia ser ";
			$mensaje .= $array_bueno1[$i] . " </td> </tr></table>";
			return false;
		}
	}
	$mensaje = ' <table style="background-color: #00FF00;" ><tr><td>Muy bien =), la solución del problema es correcta </td></tr></table>';
	return true;
}




//COnectamos a base de datos.
$con = connection_query();


$usuario = $_POST["usuario"];
$pass = sha1($_POST["pass"]);

$result = mysqli_query($con, "SELECT usuario, password FROM Usuario WHERE usuario = '$usuario' AND password = '$pass'");
$entro = false;

while($row = mysqli_fetch_array($result)) {
  $entro = true;
}

if(!$entro){
//	die('El usuario o la contraseña son incorrectos ');
}



$target_path = "uploads/";

//Saber cual es el problema
$problema = $_POST["problema"];


$result = mysqli_query($con, "SELECT id, nombre, lenguaje FROM problema WHERE id = $problema");

$problema_nombre = 'Sin_nombre';
$lenguaje = "ninguno";
while($row = mysqli_fetch_array($result)) {
  $problema_nombre = $row['nombre'];
  $lenguaje = $row['lenguaje'];
}
?>


<?php 
$html = file_get_contents('header.html');
echo $html;

?>
          <h2>Resultados</h2>
          <p>
					<?php
					if(!$entro){
						die('El usuario o la contraseña son incorrectos ');
					}
					echo "<br>Está intentando resolver el problema: " . $problema_nombre . '<br>';

					//Subir el archivo al servidor, todos con el mismo nombre, para que no se llene.

					//$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
					$target_path = $target_path . 'code.dis';

					if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
						echo "El archivo ".  basename( $_FILES['uploadedfile']['name']).
						" ha sido subido al servidor con éxito<br>";
					} else{
						die ('Hubo un problema subiendo el archivo al servidor, por favor intenta de nuevo.');
					}

					/*Converts to unix format*/
					$file = file_get_contents("uploads/code.dis");
					$file = str_replace("\r", "", $file);
					file_put_contents("uploads/code.dis", $file);




					//Ejecutar los dos programas a ver como queda.

					$codigo_bueno = "problemas/" .$problema_nombre . '.dis';
					$entrada = "problemas/" . $problema_nombre . '.in';


					$respuestaUsuario = "before";
					$respuestaCorrecta = "before";
					if($lenguaje == "dis"){
						exec('java -jar Discant_linux.jar "uploads/code.dis" <' . '"' . $entrada . '" & sleep 4 ; kill $!', $respuestaUsuario);
						exec('java -jar Discant_linux.jar "' . $codigo_bueno . '" <' . '"' . $entrada . '"', $respuestaCorrecta);
					}else if($lenguaje == "py"){
						exec('python "uploads/code.dis" <' . '"' . $entrada . '" & sleep 4 ; kill $!', $respuestaUsuario);
						exec('python "' . $codigo_bueno . '" <' . '"' . $entrada . '"', $respuestaCorrecta);
					}


					
					//print_r($respuestaCorrecta);

					$resultado = comparar_resultados($respuestaCorrecta, $respuestaUsuario);

					echo $mensaje;


					if($resultado ) {
						$result = mysqli_query($con, "SELECT usuario, problema FROM usuario_problema WHERE usuario = '$usuario' AND problema = '$problema' AND paso = 1");
						$entro = false;

						while($row = mysqli_fetch_array($result)) {
						  $entro = true;
						}
						mysqli_close($con);
						if($entro){
							echo "<p>Ud ya habia completado este problema, no hay puntos</p>";
						}else{
							 //Lo actualiza en la base de datos.
							$con = connection_update();


							 $fecha = date("Y-m-d H:i:s");
							 $sql = "INSERT INTO usuario_problema (usuario, problema, fecha, paso)
							 VALUES ('$usuario', '$problema', '$fecha', 1)";

							$retval = mysql_query( $sql );
							if(! $retval )
							{
							  die('Could not enter data: ' . mysql_error());
							}
							//$ar = addslashes (file_get_contents($_FILES['uploadedfile']['tmp_name']));
							$ar = file_get_contents("uploads/code.dis");
							$sql = "INSERT INTO archivo_solucion(archivo, usuario) VALUES('$ar', '$usuario')";
							$retval = mysql_query( $sql );
							if(! $retval )
							{
							  die('Could not enter data of file: ' . mysql_error());
							}else{
								//echo 'Lo he insertado';
							}
							
							echo "<p>Felicitaciones has hecho un problema más, has ganado puntos.</p>";
							echo '<br><br><br>';
							echo '<img src="happy.png" width="300">';
						}

					}else{
						
						$result = mysqli_query($con, "SELECT usuario, problema FROM usuario_problema WHERE usuario = '$usuario' AND problema = '$problema' AND paso = 1");
						$entro = false;

						while($row = mysqli_fetch_array($result)) {
						  $entro = true;
						}
						mysqli_close($con);
						if($entro){
							echo "<p>Ud ya habia completado este problema, no hay puntos</p>";
						}else{
						
						
							//Tuvo algun error.
							$con = connection_update();


							 $fecha = date("Y-m-d H:i:s");
							 $sql = "INSERT INTO usuario_problema (usuario, problema, fecha, paso)
							 VALUES ('$usuario', '$problema', '$fecha', 0)";

							$retval = mysql_query( $sql );
							if(! $retval )
							{
							  die('Could not enter data: ' . mysql_error());
							}
							echo '<br>';
						   echo "<p>Se le han sumado minutos de penalizacion.</p>";
						   
						   echo '<br><br><br>';
							echo '<img src="soclose.jpg"  width="300">';
						   
						}

					}						
						

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



















