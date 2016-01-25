
<?php

require_once 'db.php';

$errFecha = $errAsunto = $errFile = $errAutor = "";
$fecha = $asunto = $referencias = $userfile = "";

$result_numero = queryMysql("select * from informe where fecha between '2016.01.01' and '2016.12.31'");
$cantidad = $result_numero->num_rows + 1;

$result_autores = queryMysql("SELECT id,autor_nombre FROM autor");

$autores = array();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$referencias= $_POST['referencias'];
	$observacion= $_POST['observacion'];
	
	if (empty($_POST["fecha"])) 
	{
		$errFecha = "Se requiere una fecha";
		} 
	elseif (!checkmydate($_POST["fecha"])) {
		$errFecha = "Fecha incorrecta; formato requerido: dd/mm/yyyy";
	}
	else {
		$fecha = $_POST["fecha"];
	}

	//chekear si ha seleccionado un autor

	if (empty($_POST["autores"])) {
		$errAutor = "No ha seleccionado autor";
	} else {
		$autores = $_POST["autores"];
	}
	
	if (empty($_POST["asunto"])) {
		$errAsunto = "Se requiere un asunto";
	} else {
		$asunto = $_POST["asunto"];
	}
	
	if(!isset($_FILES['userfile']) || $_FILES['userfile']['error'] == UPLOAD_ERR_NO_FILE) {
	    $errFile = "Se requiere que adjunte un archivo"; 
	} else {
		$filename = $_FILES['userfile']['name'];
		$tempName = $_FILES['userfile']['tmp_name'];
		$fileSize = $_FILES['userfile']['size'];
		$fileType = $_FILES['userfile']['type'];
		
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $_FILES['userfile']['tmp_name']);
		
		if ($mime != 'application/pdf')
		{
			$errFile = "Se requiere archivo en formato pdf";
		}
		
		$fp = fopen($tempName, 'r');
		$content = fread($fp, filesize($tempName));
		$content = addslashes($content);
		fclose($fp);
		
		if (!get_magic_quotes_gpc())
		{
			$filename = addslashes($filename);
		}
	}
	
	if ($errFecha == "" && $errAsunto == "" && $errFile == "" && $errAutor == "")
	{
		queryMysql("INSERT INTO informe(numero,fecha,asunto,referencias,observacion,filename,type,size,content) VALUES ('$cantidad',STR_TO_DATE('$fecha','%d/%m/%Y'),'$asunto','$referencias','$observacion','$filename','$fileType','$fileSize','$content')");

		foreach ($autores as $selected) {
			queryMysql("INSERT INTO informe_has_autor(informe_id,autor_id) VALUES ( " . obtenerUltimoId() . ",'$selected')");
		}

		header("Refresh:0");
	}	

}

?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<title>Cepredim - Administración</title>

	<link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="stylesheet" href="js/bootstrap-datepicker-1.5.0-dist/css/bootstrap-datepicker.min.css" >
	
	<!-- <script type="text/javascript" src="js/jquery.min.js"></script> -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>


	<!-- Include the plugin's CSS and JS: -->
	<script type="text/javascript" src="js/bootstrap-multiselect.js"></script>
	<link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>


	<script type="text/javascript" src="js/bootstrap-datepicker-1.5.0-dist/js/bootstrap-datepicker.min.js"> </script>
	<script type="text/javascript" src="js/bootstrap-datepicker-1.5.0-dist/locales/bootstrap-datepicker.es.min.js"> </script>
	<script type="text/javascript" src="js/bootstrap-filestyle.min.js"> </script>

	<style type="text/css">
   		body { background: #ffffff !important; } 
	</style>


	<script type="text/javascript">
		$(document).ready(function () {

	        $('#fecha').datepicker({
	            format: "dd/mm/yyyy",
	            language: 'es',
	            autoclose: true,
	            todayHighlight: true
	        });   

	        $('#autores-select').multiselect();
	    
	    });
	</script>
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
      <a class="navbar-brand" href="#"><span><img src="escudo.png" alt="" width="28"></span> UNMSM-CEPREDIM</a>
    </div>

  </div><!-- /.container-fluid -->
</div>
</nav>
     
<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="page-header" align="center">
		  		<h4><strong>Informe Nº <?php echo $cantidad ?>-2016-CEPREDIM-UNMSM/GG/ADMIN</strong></h4>
			</div>
			<form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
				<div class="form-group">
					<label for="fecha" class="col-sm-2 control-label">Fecha</label>	
					<div class="col-sm-10">
						<input type="text" class="form-control" value="<?php echo htmlentities(isset($_POST['fecha'])? $_POST['fecha']:''); ?>" name="fecha" id="fecha">
						<span class="text-danger"> <?php echo $errFecha;?></span>
					</div>
				</div>

				<div class="form-group">
			        <label for="autor" class="col-sm-2 control-label">Autor</label>	
			        <div class="col-sm-10">
			            <select  id="autores-select" class="form-control" name="autores[]" multiple="multiple">
			            	<?php
			            		foreach ($result_autores as $row) {
						  			$dropdown .= "\r\n<option value='{$row['id']}'>{$row['autor_nombre']}</option>";
								}	
								echo $dropdown;
			            	?>
			            </select>
			            <span class="text-danger"> <?php echo $errAutor;?></span>
			        </div>
			    </div>
				
				<div class="form-group">
					<label for="asunto" class="col-sm-2 control-label">Asunto</label>
					<div class="col-sm-10">
					<input type="text" class="form-control" value="<?php echo htmlentities(isset($_POST['asunto'])? $_POST['asunto']:''); ?>" name="asunto" id="asunto">
					<span class="text-danger"> <?php echo $errAsunto;?></span>
					</div>
					
				</div>
		
		
				<div class="form-group">
					<label for="referencias" class="col-sm-2 control-label">Referencias</label>
					<div class="col-sm-10">
					<input type="text" class="form-control" value="<?php echo htmlentities(isset($_POST['referencias'])? $_POST['referencias']:''); ?>" name="referencias" id="referencias">
					</div>
				</div>

				<div class="form-group">
					<label for="observacion" class="col-sm-2 control-label">Observación</label>
					<div class="col-sm-10">
					<textarea class="form-control" rows="5" value="<?php echo htmlentities(isset($_POST['observacion'])? $_POST['observacion']:''); ?>" name="observacion" id="observacion"></textarea>
					<!--<input type="text" class="form-control" value="<?php echo htmlentities(isset($_POST['observacion'])? $_POST['observacion']:''); ?>" name="observacion" id="observacion">-->
					</div>
				</div>
				
				<!--<input type="hidden" name="MAX_FILE_SIZE" value="2000000">-->
				
				<div class="form-group">
					<div class="col-sm-10 col-sm-offset-2">
						<input name="userfile" value="<?php echo htmlentities(isset($_POST['userfile'])? $_POST['userfile']:''); ?>" type="file" class="filestyle" data-buttonText="Seleccionar Archivo" id="userfile">
						<span class="text-danger"> <?php echo $errFile;?></span>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-10 col-sm-offset-2">
						<button type="submit" class="btn btn-primary">Enviar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

 
</body>


</html>
