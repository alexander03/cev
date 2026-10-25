<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Consulta Reniec</title>

<script src="js/jquery.min.js"></script>
</head>

<body>

<form method="post">
	<input type="text" class="dni" id="dni" name="dni">
    <button id="botoncito" class="botoncito">Enviar</button>
</form>

<div id="mostrar_dni">Aqui se mostrara el dni Consultado</div>
<div>APELLIDO PATERNO: <span id="paterno"></span></div>
<div>APELLIDO MATERNO: <span id="materno"></span></div>
<div>NOMBRES COMPLETOS: <span id="nombres"></span></div>

<script>
$(function(){
	$('#botoncito').on('click', function(){
		var dni = $('#dni').val();
		var url = 'consulta_reniec.php';
		$.ajax({
		type:'POST',
		url:url,
		data:'dni='+dni,
		success: function(datos_dni){
			var datos = eval(datos_dni);
				$('#mostrar_dni').text(datos[0]);
				$('#paterno').text(datos[1]);
				$('#materno').text(datos[2]);
				$('#nombres').text(datos[3]);
		}
	});
	return false;
	});
});
</script>

</body>
</html>