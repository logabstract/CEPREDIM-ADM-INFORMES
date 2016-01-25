<?php

require_once 'db.php';

if(isset($_GET['id']))
{
	
	$id    = $_GET['id'];
	
	$result = queryMysql("SELECT filename, type, size, content FROM informe WHERE id = '$id'");

	list($filename, $type, $size, $content) = $result->fetch_array(MYSQLI_NUM);

	header("Content-length: $size");
	header("Content-type: $type");
	header("Content-Disposition: attachment; filename=$filename");
	echo $content;

	exit;
}

?>