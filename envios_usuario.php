<?php
	include "config.php";
?>

<?php 
$html = file_get_contents('header.html');
echo $html;

?>
			<div>
          <h2>Envíos</h2>
          <p>
				<?php
					//Conectamos a base de datos.
					$con = connection_query();


					$usuario = $_GET["usuario"];


					echo 'Problemas enviados por: ' . $usuario . '<br><br>';
					echo 'Si el problema fue enviado a tiempo ganara 10 puntos, de lo contrario ganara 5<br><br>';

					$result = mysqli_query($con, "SELECT usuario_problema.usuario AS usuario, usuario_problema.problema AS prob, usuario_problema.fecha AS fecha, problema.nombre AS nombre, problema.fecha_maxima AS maxi, problema.id AS id
													FROM usuario_problema, problema WHERE usuario = '$usuario' AND usuario_problema.problema = id");


					echo '<table border="2"><tr><td> Nombre problema </td><td>Fecha de envío</td><td>Fecha máxima de envío</td><td>Puntos</td></tr>';
					$puntos = 0;
					$puntos_total = 0;
					while($row = mysqli_fetch_array($result)) {
					  $fecha_envio = strtotime( $row['fecha'] );
					  $fecha_maxi = strtotime( $row['maxi'] );
					  if($fecha_maxi - $fecha_envio >= 0){
						$puntos = 10;
					  }else{
						$puntos = 5;
					  }
					  echo '<tr><td>' . $row['nombre'] . '</td>' . '<td>' . $row['fecha'] . '</td><td>' . $row['maxi']  . '</td><td>' . $puntos . '</td></tr>';

					  $puntos_total += $puntos;
					}

					echo '</table>';

					echo '<br><br><br>';
					echo 'Tienes un total de: ' . $puntos_total . ' puntos. <br> ';

					?>

          </p>
			</div>

        </div>
      </div>
    </div>
    <div class="cleaner">&nbsp;</div>
  </div>
</div>
<div align=center>Juez creado por: Daniel Serrano, Lenguaje creado por Alfredo Santamaria y Daniel Serrano</div></body>
</html>






