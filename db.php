<?php
$dbhost = 'localhost';
$dbname = 'prueba';
$dbuser = 'root';
$dbpass = 'root';

$connection = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
if ($connection->connect_error) die($connection->connect_error);

function queryMysql($query)
{
	global $connection;

	$result = $connection->query($query);
	if (!$result) die($connection->error);
	return $result;
}

function obtenerUltimoId(){
	global $connection;
	return $connection->insert_id;
}

function checkmydate($date) 
{
	$tempDate = explode('/', $date);
	if (checkdate($tempDate[1], $tempDate[0], $tempDate[2])) {
		return true;
	} else {
		return false;
	}
}

function obtenerAutores($idInforme)
{
	global $connection;

	$stmt = $connection->prepare("SELECT a.autor_nombre FROM autor a, informe_has_autor ia where a.id=ia.autor_id and ia.informe_id=?");
	$stmt->bind_param('i',intval($idInforme));	
	$stmt->execute(); 
	$row = bind_result_array($stmt);
	if (!$stmt->error) {
		while ($stmt->fetch()) {
			$autores[$row['autor_nombre']] = getCopy($row);
		}	
	}

	$lista_autores="";

	$iCount = substr_count(print_r($autores, true), "autor_nombre");

	$i=0;
	foreach ($autores as $fila) {
		$i++;
		if ($i == $iCount) {
			$lista_autores .= $fila['autor_nombre'];
		}else{
			$lista_autores .= $fila['autor_nombre'] . ', ';
		}
		
	}

	return $lista_autores;

}

/*
 * Utility function to automatically bind columns from selects in prepared statements to 
 * an array
 */
function bind_result_array($stmt)
{
    $meta = $stmt->result_metadata();
    $result = array();
    while ($field = $meta->fetch_field())
    {
        $result[$field->name] = NULL;
        $params[] = &$result[$field->name];
    }
 
    call_user_func_array(array($stmt, 'bind_result'), $params);
    return $result;
}
 
/**
 * Returns a copy of an array of references
 */
function getCopy($row)
{
    return array_map(create_function('$a', 'return $a;'), $row);
}

?>
