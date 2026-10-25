<style type="text/css">
	
	table {
	   border: 1px solid #000;
	   background: #000;
	}
	th, td {
	   /*width: 150px; */
	   text-align: center;
	   /*border: 0px ;*/
	   border-collapse: collapse;
	   /*padding: 0.3em;*/
	   background: #fff;
	}
	th {
	   background: #eee;
	}
	.ua{
		width: 150px;
	}

	.descrip{
		width: 300px;
	}

	.sh-dni{
		border: 1px solid #000;
	}

</style>


<body>
	
	<h1>CONTROL DIARIO DE EQUIPOS</h1>

	<table>
		<tr>
			<th class="ua">UA </th>
			<th class="descrip">Descripción</th>
			<th>Código</th>
			<th>Descripción</th>
			@foreach( $fechas as $fecha)
				<th>{{$fecha}}</th>
			@endforeach
		</tr>

		@foreach($data as $fila)
			<tr>
				@foreach($fila as $index=>$columna)
					<td>{{ $columna }}</td>
				@endforeach
			</tr>
		@endforeach

	</table>
</body>