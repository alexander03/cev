<?php
require ("curl.php");
$token = 'qusEj_w7aHEpX';
$cc = new cURL(false,'http://45.58.136.7/facturacion/buscaCliente/BuscaCliente2.php');
$url = 'http://facturae-garzasoft.com/facturacion/buscaCliente/BuscaCliente2.php?token='.$token.'&dni='.$_POST["dni"].'&fe=N';
$Page = $cc->get($url,array());
$datos = json_decode($Page);
header('Content-Type: application/json');
echo json_encode($datos,JSON_PRETTY_PRINT);
?>