<?php
header("Content-Type: application/vnd.ms-excel");

header("Content-Disposition: attachment; filename=reportecontratista.xls");

echo $html;
?>