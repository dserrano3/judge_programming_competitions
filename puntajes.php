<?php
include "config.php";
$inicio  = strtotime('2015-05-08 16:00:00');


$con = connection_query();
$array_usuarios = array();
$array_puntos = array();
$array_nombre = array();
$array_colegio = array();

//Este es para la barra de colegios.
$array_colegios = array();
$cuantos_colegio = array();
$puntos_colegio = array();

$result = mysqli_query($con, "SELECT nombre, usuario, colegio FROM Usuario");


while($row = mysqli_fetch_array($result)) {
  array_push($array_usuarios, $row['usuario']);
  $array_puntos[$row['usuario']] = 0;
  $array_nombre[$row['usuario']] = $row['nombre'];
  $array_colegio[$row['usuario']] = $row['colegio'];
  
  //Barra de colegios
 if(!in_array ($row['colegio'], $array_colegios))
	array_push($array_colegios, $row['colegio']);
  $cuantos_colegio[$row['colegio']] += 1;
  $puntos_colegio[$row['colegio']] = 0;
}

$result = mysqli_query($con, "SELECT usuario, problema, fecha_maxima, id, fecha FROM usuario_problema, problema WHERE problema = id");
$puntos = 0;
while($row = mysqli_fetch_array($result)) {
  $fecha_envio = strtotime( $row['fecha'] );
  $fecha_maxi = strtotime( $row['fecha_maxima'] );
  if($fecha_maxi - $fecha_envio >= 0){
	$puntos = 10;
  }else{
	$puntos = 5;
  }
  $array_puntos[$row['usuario']] = $array_puntos[$row['usuario']] + $puntos;
  $puntos_colegio[$array_colegio[$row['usuario']]] += $puntos; 
}





//Empieza codigo para maraton.




$array_nombre_problemas = array();
$array_problemas = array();
$result = mysqli_query($con, "SELECT nombre, id FROM problema");


while($row = mysqli_fetch_array($result)) {
  array_push($array_nombre_problemas, $row['nombre']);
  array_push($array_problemas, $row['id']);
}


//Calcular el mejor.
$total_puntos = array();
$total_tiempo= array();
for($i = 0; $i < count($array_usuarios);$i++)
{
	  $tiempo_total = 0;
	  $problemas_total = 0;
	  $total = 0;
	  
	  
	  $timeToEnd = 0;
	  for($j = 0; $j < count($array_nombre_problemas);$j++)
	  {	  
		  $result = mysqli_query($con, "SELECT usuario, problema, fecha, paso FROM usuario_problema 
		  WHERE usuario = '" . $array_usuarios[$i] . "' AND problema = " . $array_problemas[$j] );
		  $conta = 0;
		  $sumaTiempo = 0;
		  $bien = false;
		  while($row = mysqli_fetch_array($result)) {	
			$conta ++;
			if($row['paso'] == 1){
				$total ++;
				$bien = true;
				$timeToEnd += (( strtotime($row['fecha']) - $inicio) / 60);
			}else{
				$sumaTiempo += 20;
			}
		  }
		  if($bien) $timeToEnd += $sumaTiempo;
		   
	  }
	  $total_puntos[$array_usuarios[$i]] = $total;
	  $total_tiempo[$array_usuarios[$i]] = $timeToEnd;
}






//Ordenamiento.
for($i = 0; $i < count($array_usuarios); $i++){
	for($j = 0 ; $j < count($array_usuarios) - 1; $j++){
		if($total_puntos[$array_usuarios[$j]] < $total_puntos[$array_usuarios[$j+1]] ){
			$auxil = $array_usuarios[$j];
			$array_usuarios[$j] =  $array_usuarios[$j+1];
			$array_usuarios[$j+1] = $auxil;
		}else if($total_puntos[$array_usuarios[$j]] == $total_puntos[$array_usuarios[$j+1]] ){
			if($total_tiempo[$array_usuarios[$j]] > $total_tiempo[$array_usuarios[$j+1]]){
				$auxil = $array_usuarios[$j];
				$array_usuarios[$j] =  $array_usuarios[$j+1];
				$array_usuarios[$j+1] = $auxil;
			}
		}
		
	}
}





?>

<?php 
$html = file_get_contents('header.html');
echo $html;

?>

      <div class="col-xs-12 col-sm-8 col-md-8">
		
        <br><br>

		<?php
			//Calcular el maximo para el porcentaje.
			$maxi = 0;
			foreach ($array_colegios as $val)
			{
				if(($puntos_colegio[$val] / $cuantos_colegio[$val]) > $maxi){
					$maxi = ($puntos_colegio[$val] / $cuantos_colegio[$val]);
				}
			}
			
			foreach ($array_colegios as $val)
			{
				if($puntos_colegio[$val] == 0)continue;
				
				$ancho = (($puntos_colegio[$val] / $cuantos_colegio[$val]) * 100) / $maxi;
				//echo 'nombre: ' . $val . ' cuantos: ' . $cuantos_colegio[$val] . ' puntos: ' . $puntos_colegio[$val] . '<br>';
				echo '<div class="progress progress-striped">';
				echo '<div class="progress-bar  progress-bar-custom" style="width: ' . $ancho . '%">';
				echo  $val ;
			    echo '</div>';
				echo '</div>';
				
			}
		
		?>

        <div class="panel panel-custom filterable">
          <div class="panel-heading">
              <h3 class="panel-title">Tabla de posiciones Usuarios</h3>
          </div>

          <table class="table table-hover" id="dev-table">
            <tr>
              <th><font color="#DB8321"> # </font></th>
              <th><font color="#DB8321"> Usuario </font></th>
              <?php
              for($i = 0; $i < count($array_nombre_problemas);$i++)
      		  {
				   echo '<th><font color="#DB8321">' . $array_nombre_problemas[$i]  . '</font></th>';
			  }
              echo '<th><font color="#DB8321">' . 'Total' . '</font></th>';
              ?>
              
            </tr>
            <p>
      				<?php
						for($i = 0; $i < count($array_usuarios);$i++)
						{
							echo '<tr>' .
							  '<td>' . ($i + 1)  . '</td>' .
							  '<td>' .
								'<a href="envios_usuario.php?usuario=' . $array_usuarios[$i] . '">' .
								$array_usuarios[$i] . '</a>' .
							  '</td>';
							  $tiempo_total = 0;
							  $problemas_total = 0;
							  $total = 0;
							  for($j = 0; $j < count($array_nombre_problemas);$j++)
							  {
								  
								  $result = mysqli_query($con, "SELECT usuario, problema, fecha, paso FROM usuario_problema 
								  WHERE usuario = '" . $array_usuarios[$i] . "' AND problema = " . $array_problemas[$j] );
								  $timeToEnd = 0;
								  $conta = 0;
								  $sumaTiempo = 0;
								  $verde = false;
								  while($row = mysqli_fetch_array($result)) {
									$conta ++;
									if($row['paso'] == 1){
										$total ++;
										$verde = true;
										$timeToEnd += intval(( strtotime($row['fecha']) - $inicio) / 60);
									}else{
										$sumaTiempo += 20;
									}				
								  }
								  if($verde) $timeToEnd += $sumaTiempo;
								  $extra = ' ';
								  if($verde) $extra = ' bgcolor="#00FF00" ';
								  else if($conta > 0) $extra = ' bgcolor="#E76060" ';
								  echo '<td ' . $extra . '>' .  $conta . '/' . $timeToEnd . '</td>';
								  $tiempo_total += $timeToEnd;
							  }
							  echo '<td>' .  $total . '/' .  $tiempo_total . '</td>';
						  '</tr>';
						}
      				?>

            </p>
					</table>
        </div>
      </div>
  </main>
</header>

<div align=center>Juez creado por: Daniel Serrano, Lenguaje creado por Alfredo Santamaria y Daniel Serrano</div></body>
</html>
