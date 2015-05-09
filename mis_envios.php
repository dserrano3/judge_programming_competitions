<?php
	include "config.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
<title>Juez Virtual Discant</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen, projection, tv" />
<link rel="stylesheet" href="/css/style-print.css" type="text/css" media="print" />
</head>
<body>
<div id="wrapper">
  <div class="title">
    <div class="title-top">
      <div class="title-left">
        <div class="title-right">
          <div class="title-bottom">
            <div class="title-top-left">
              <div class="title-bottom-left">
                <div class="title-top-right">
                  <div class="title-bottom-right">
                    <h1><a href="/">Juez Virtual Discant</a></h1>
                    <p>Juez para el curso de colegios, Capítulo Javeriano ACM</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <hr class="noscreen" />
  <div class="content">
    <div class="column-left">
      <h3>MENU</h3>
      <a href="#skip-menu" class="hidden">Skip menu</a>
      <ul class="menu">
        <li><a href="/index.html" >Inicio</a></li>
        <li><a href="/juez/upload_form.php">Enviar problema</a></li>
        <li><a href="/juez/insertar_usuario.html" >Crear usuario</a></li>
        <li><a href="/juez/puntajes.php">Tabla de posiciones</a></li>
        <li><a href="/juez/insert_problem.html">Agregar problema</a></li>
        <li class="last"><a href="/judge/mis_envios.html" class="active">Mis envios</a></li>
      </ul>
    </div>
    <div id="skip-menu"></div>
    <div class="column-right">
      <div class="box">
        <div class="box-top"></div>
        <div class="box-in">
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
    <div class="cleaner">&nbsp;</div>
  </div>
</div>
<div align=center>Juez creado por: Daniel Serrano, Lenguaje creado por Alfredo Santamaria y Daniel Serrano</div></body>
</html>






