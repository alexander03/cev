<style type="text/css">
	
	table {
	   border: 1px solid #000;
	   background: #000;
	}
	th, td {
	   width: 150px; 
	   text-align: center;
	   border: 0px ;
	   border-collapse: collapse;
	   padding: 0.3em;
	   background: #fff;
	}
	th {
	   background: #eee;
	}
	.num{
		width: 70px;
	}

	.nombres{
		width: 300px;
	}

	.sh-dni{
		border: 1px solid #000;
	}
	.noborder{
	   border-collapse: collapse;
        border: none; 
	}

</style>


<body>
	
	<h4 style='text-align:center'>CONTROL DIARIO DE COMBUSTIBLE</h4>
	<table class="noborder" width="100%" border="0" style="font-size: 10px; !important;">
	    <tr>
	        <td align='left'>FECHA: {{ date("d/m/Y",strtotime($fecha)) }}</td>
	        <td align='right'>GRIFO: {{ $grifo }}</td>
	    </tr>
	    <tr>
	        <td align='left'>TIPO COMBUSTIBLE: {{ $tipocombustible }}</td>
	        <td align="right">HOROMETRO INICIAL: {{ $horometro_inicial }}</td>
	    </tr>
	    <tr>
	    	<td align='right'>&nbsp;</td>
	    	<td align="right">HOROMETRO FINAL: {{ $horometro_final }}</td>
	    </tr>
	</table>
	<br />
	<table>
		<tr>
			<th class="num">N° </th>
			<th class="nombres">NOMBRES Y APELLIDOS</th>
			<th>DNI</th>
			<th>UA</th>
			<th>VEHICULO</th>
			<th>MARCA</th>
			<th>PLACA</th>
			<th>HORO. / KM</th>
			<th>GALONES</th>
			<th>H-INICIO</th>
			<th>H-TERMINO</th>
			<th>EMPRESA</th>
			<th>FIRMA</th>
		</tr>
        <?php
        $total = 0;
        ?>
		@foreach($data as $index=>$value)
		    <?php
		    $total = $total + $value->qtdgl;
		    ?>
			<tr>
				<td class="num">{{$index + 1 }}</td>
				<td class="nombres" >{{ $value->res }}</td>
				<td>{{ $value->dni }}</td>
				<td>{{ $value->code }}</td>
				<td>{{ $value->desceq }}</td>
				<td>{{ $value->descmc }}</td>
				<td>{{ $value->eqpl }}</td>
				<td>{{ $value->km }}</td>
				<td>{{ $value->qtdgl }}</td>
				<td>{{ $value->hora_inicio }}</td>
				<td>{{ $value->hora_fin }}</td>
				<td>{{ $value->crza }}</td>
				<td><img src="{{asset('files/documento_conductor/imagenes_firmas')}}/{{$value->firma}}" width="130" height="70"></td>
			</tr>
		@endforeach
        <tr>
            <th colspan='8'>TOTAL</th>
            <th>{{ number_format($total,2,'.','') }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
	</table>
	<br />
	<br />
	<br />
	<table align="center" style="" width="100%" class="noborder">
	    <tr>
	        <td> _________________________________</td>
	        <td> _________________________________</td>
	        <td> _________________________________</td>
	    </tr>
	    <tr>
	        <td> RESP. ADMINISTRACION</td>
	        <td> RESP. ABASTECIMIENTO</td>
	        <td> RESP. GRIFO</td>
	    </tr>
	</table>
</body>