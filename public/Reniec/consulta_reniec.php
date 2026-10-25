
<?php
require 'simple_html_dom.php';
error_reporting(E_ALL ^ E_NOTICE);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-socket-id");
header('Access-Control-Allow-Credentials: true');   
$dni = $_POST['dni'];
 
//OBTENEMOS EL VALOR
//$consulta = file_get_html('http://aplicaciones007.jne.gob.pe/srop_publico/Consulta/Afiliado/GetNombresCiudadano?DNI='.$dni)->plaintext;
$consulta = file_get_html('https://eldni.com/buscar-por-dni?dni='.$dni);
 
$datosnombres = array();
foreach($consulta->find('td') as $header) {
 $datosnombres[] = $header->plaintext;
}
//print_r($headlines);

//Logica para H2Olmos
$datos = null;
if($datosnombres[0] != null) {
	$datos = array(
		'dni' => $dni,
		'apellidos' => $datosnombres[1] . ' ' . $datosnombres[2],
		'nombres' => $datosnombres[0],
	);
}

 
echo json_encode($datos);
 
?>
