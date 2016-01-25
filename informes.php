<?php
require_once 'db.php';

if (isset($_GET['año'])) {
    $result = queryMysql("SELECT id,numero,DATE_FORMAT(fecha,'%d/%m/%Y') as fecha,asunto,referencias,observacion,filename FROM informe where fecha between '" . $_GET['año'] . ".01.01'" . " and '". $_GET['año'] . ".12.31'");
} else {
  
  $result = queryMysql("SELECT id,numero,DATE_FORMAT(fecha,'%d/%m/%Y') as fecha,asunto,referencias,observacion,filename FROM informe where fecha between '" . date("Y") . ".01.01'" . " and '". date("Y") . ".12.31'");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Informes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/bootstrap.min.css" >
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <style type="text/css">
   		body { background: #ffffff !important; } /* Adding !important forces the browser to overwrite the default style applied by Bootstrap */
	</style>
</head>
<body>

<nav class="navbar navbar-default">
  <div class="navbar-inner">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="informes.php"><span><img src="escudo.png" alt="" width="28"></span> UNMSM-CEPREDIM</a>
    </div>

  </div><!-- /.container-fluid -->
</div>
</nav>

<div class="container">
  	<div class="page-header" align="center">
  		<h3>Informes de la Gerencia de Administración</h3>
	</div>

  <ul class="nav nav-tabs">
    <li <?php if(isset($_GET['año'])): ?>
          <?php if($_GET['año'] == 2016): ?>
            <?php echo 'class="active"' ?>
          <?php endif; ?> 
        <?php else: ?>
          <?php echo 'class="active"' ?>
        <?php endif; ?>
    >

      <a href="informes.php?año=2016">2016</a>


    </li>
    <li <?php if(isset($_GET['año'])): ?>
          <?php if($_GET['año'] == 2015): ?>
            <?php echo 'class="active"' ?>
          <?php endif; ?> 
        <?php endif; ?>
    >
      <a href="informes.php?año=2015">2015</a>

    </li>
  </ul>

  <div class="row">
    <div class="col-md-12">
      <p></p>
    </div>
  </div>

  <table class="table table-striped table-bordered table-hover table-condensed">
    <thead>
      <tr>
        <th>Nº</th>
        <th>Fecha</th>
        <th>Asunto</th>
        <th>Elaborado por</th>
        <th>Referencias</th>
        <th>Observación</th>
        <th>Archivo</th>
      </tr>
    </thead>
    <tbody>
    <?php
    	while($row = $result->fetch_array(MYSQLI_ASSOC)){
    		echo "<tr>";
    		
    		echo '<td>' . $row["numero"] . '</td>' . '<td style="white-space: nowrap;">' . $row["fecha"] . '</td>' . '<td>' . utf8_encode($row["asunto"]) . '</td>' . '<td style="white-space: nowrap;">' . obtenerAutores($row["id"]) . '</td>' . '<td>' . utf8_encode($row["referencias"]) . '</td>' .  '<td>' . utf8_encode($row["observacion"]) . '</td>' . '<td>' . '<a href=download.php?id=' . $row['id'] . '>' . $row['filename'] . '</a>' . '</td>';
    		
    		echo "</tr>";
    	}
    ?>
    </tbody>
  </table>
</div>
<script type="text/javascript">

$(document).ready(function(){
  $("#submit").click(function(){
    $("#form").submit();  // jQuey's submit function applied on form.
  });
});
</script>


</body>
</html>
