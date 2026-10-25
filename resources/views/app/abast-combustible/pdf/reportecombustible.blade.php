<style type="text/css">
	
	table {
	   border: 0.5px solid;
	   border-collapse: collapse;
	}
	th, td {
	   width: 150px; 
	   text-align: center;
	   border: 0.5px solid ;
	}
	th {
	   background: #0cd3f3;
	}
	.num{
		width: 70px;
	}

	.nombres{
		width: 300px;
	}
	.left{
	   text-align: left; 
	}
	.right{
	   text-align: right; 
	}
	.table-borderless{
        border-collapse: collapse;
        border: none;
    }
    .h1{
        font-size: 10px;
    }
</style>


<body>
    @if($concesionaria_id=="2")
        <img src="{{ asset('/assets/img/logo-main.png') }}" width="90" height="45">
    @elseif($concesionaria_id=="1")
        <img src="{{ asset('/assets/img/logo.jpg') }}" width="90" height="45">
    @endif
    <table>
    <?php
    $total1=0;$total2=0;$lista = array();$lista2 = array();$totalequipo=0;$totalvehiculo=0;$totalequipo2=0;$totalvehiculo2=0;$totaldescuento=0;$totaldescuento2=0;
    ?>
    	    <thead>
    	        <tr>
    	            <th style="background: #0cd3f3;">PERIODO</th>
    	            <td colspan='13' class='left'>{{ date("d/m/Y",strtotime($fechainicial))." AL ".date("d/m/Y",strtotime($fechafinal)) }}</td>
    	        </tr>
    	    </thead>
    		<tr>
    			<th style="background: #0cd3f3;" class="num">FECHA</th>
    			<th style="background: #0cd3f3;">GRIFO</th>
    			<th style="background: #0cd3f3;">TIPO COMBUSTIBLE</th>
    			<th style="background: #0cd3f3;">CONDUCTOR</th>
    			<th style="background: #0cd3f3;">DNI</th>
    			<th style="background: #0cd3f3;">UA</th>
    			<th style="background: #0cd3f3;">UA CONCESIONARIA</th>
    			<!--th>CONCESIONARIA TRABAJO</th-->
    			<th style="background: #0cd3f3;">UNIDAD</th>
    			<th style="background: #0cd3f3;">MARCA</th>
    			<th style="background: #0cd3f3;">MODELO</th>
    			<th style="background: #0cd3f3;">PLACA/SERIE</th>
    			<th style="background: #0cd3f3;">EMPRESA</th>
    			<th style="background: #0cd3f3;">QTD(Gl)</th>
    			<th style="background: #0cd3f3;">QTD(Lt)</th>
    			<th style="background: #0cd3f3;">FIRMA</th>
    		</tr>
    	@foreach($data as $index=>$value)
    		<?php
    		if($value->tipo=="equipo" && $value->descuento=="N"){
	            $totalequipo = $totalequipo + $value->qtdgl;
	            $totalequipo2 = $totalequipo2 + $value->qtdl;
	        }elseif($value->tipo=="vehiculo" && $value->descuento=="N"){
	            $totalvehiculo = $totalvehiculo + $value->qtdgl;
	            $totalvehiculo2 = $totalvehiculo2 + $value->qtdl;
	        }elseif($value->descuento=="S"){
	            $totaldescuento = $totaldescuento + $value->qtdgl;
	            $totaldescuento2 = $totaldescuento2 + $value->qtdl;
	        }
    		if(isset($lista[$value->code])){
    		    $lista[$value->code]["qtdgl"] = $lista[$value->code]["qtdgl"] + $value->qtdgl;
    		    $lista[$value->code]["qtdl"] = $lista[$value->code]["qtdl"] + $value->qtdl;
    		}else{
    		    $lista[$value->code] = array("ua"=>$value->code,"qtdgl"=>$value->qtdgl,"qtdl"=>$value->qtdl);
    		}
    		if(isset($lista2[$value->tipocombustible])){
    			$lista2[$value->tipocombustible]["qtdgl"] = $lista2[$value->tipocombustible]["qtdgl"] + $value->qtdgl;
    		    $lista2[$value->tipocombustible]["qtdl"] = $lista2[$value->tipocombustible]["qtdl"] + $value->qtdl;
    		}else{
    			$lista2[$value->tipocombustible] = array("tipocombustible"=>$value->tipocombustible,"qtdgl"=>$value->qtdgl,"qtdl"=>$value->qtdl);
    		}
    		$lista[$value->code]["placa"] = $value->placa;
    		$lista[$value->code]["modelo"] = $value->modelo;
    		$lista[$value->code]["descripcion"] = $value->desceq;
    		$total1=$total1+$value->qtdgl;
    		$total2=$total2+$value->qtdl;
    		?>
			<tr>
				<td class="num">{{ date("d/m/Y",strtotime($value->fecha)) }}</td>
				<td>{{ $value->grifo }}</td>
				<td>{{ $value->tipocombustible }}</td>
				<td>{{ $value->res }}</td>
				<td>{{ $value->dni }}</td>
				<td>{{ $value->code }}</td>
				<td>{{ $value->concesionariaua }}</td>
				<!--td>{{ $value->concesionariatrabajo }}</td-->
				<td>{{ $value->desceq }}</td>
				<td>{{ $value->descmc }}</td>
				<td>{{ $value->modelo }}</td>
				<td>{{ $value->placa }}</td>
				<td>{{ $value->contratista }}</td>
				<td>{{ $value->qtdgl }}</td>
				<td>{{ $value->qtdl }}</td>
				@if($tipo=="pdf")
				<td><img src="{{asset('files/documento_conductor/imagenes_firmas')}}/{{$value->firma}}" width="130" height="70"></td>
				@endif
			</tr>
		@endforeach
		<tfoot>
		    <tr>
		        <th style="background: #0cd3f3;" colspan='12'>TOTAL</th>
		        <th style="background: #0cd3f3;">{{ number_format($total1,2,'.','') }}</th>
		        <th style="background: #0cd3f3;">{{ number_format($total2,2,'.','') }}</th>
		    </tr>
		</tfoot>
	</table>
	<br />
	<table class="table-borderless">
	    <thead>
	        <tr>
	            <th style="background: #0cd3f3;" class='h1'>UA</th>
	            <th style="background: #0cd3f3;" class='h1'>DESC</th>
	            <th style="background: #0cd3f3;" class='h1'>PLACA</th>
	            <th style="background: #0cd3f3;" class='h1'>MODELO</th>
	            <th style="background: #0cd3f3;" class='h1'>SUMA DE QTD(GL)</th>
	            <th style="background: #0cd3f3;" class='h1'>SUMA DE QTD(L)</th>
	        </tr>
	    </thead>
	    <tbody>
	        @foreach($lista as $k=>$v)
	        <tr>
	            <td class='h1'>{{ $v["ua"] }}</td>
	            <td class='h1'>{{ $v["descripcion"] }}</td>
	            <td class='h1'>{{ $v["placa"] }}</td>
	            <td class='h1'>{{ $v["modelo"] }}</td>
	            <td class='h1'>{{ $v["qtdgl"] }}</td>
	            <td class='h1'>{{ $v["qtdl"] }}</td>
	        </tr>
	        @endforeach
	    </tbody>
	    <tfoot>
	        <tr>
	            <th style="background: #0cd3f3;" class='h1' colspan='4'>TOTAL</th>
	            <th style="background: #0cd3f3;" class='h1'>{{ number_format($total1,2,'.','') }}</th>
		        <th style="background: #0cd3f3;" class='h1'>{{ number_format($total2,2,'.','') }}</th>
	        </tr>
	    </tfoot>
	</table>
	<br />
	<table class="table-borderless">
	    <thead>
	        <tr>
	            <th style="background: #0cd3f3;" class='h1'>TIPO COMBUSTIBLE</th>
	            <th style="background: #0cd3f3;" class='h1'>SUMA DE QTD(GL)</th>
	            <th style="background: #0cd3f3;" class='h1'>SUMA DE QTD(L)</th>
	        </tr>
	    </thead>
	    <tbody>
	        @foreach($lista2 as $k2=>$v2)
	        <tr>
	        	<td class='h1'>{{ $v2["tipocombustible"] }}</td>
	            <td class='h1'>{{ $v2["qtdgl"] }}</td>
	            <td class='h1'>{{ $v2["qtdl"] }}</td>
	        </tr>
	        @endforeach
	    </tbody>
	    <tfoot>
	        <tr>
	            <th style="background: #0cd3f3;" class='h1'>TOTAL</th>
	            <th style="background: #0cd3f3;" class='h1'>{{ number_format($total1,2,'.','') }}</th>
		        <th style="background: #0cd3f3;" class='h1'>{{ number_format($total2,2,'.','') }}</th>
	        </tr>
	    </tfoot>
	</table>
	<br />
	<br />
	<table class="table-borderless" align='center'>
        <thead>
    	    <tr>
    	        <th style="background: #0cd3f3;" class='h1' colspan="3">COMBUSTIBLE</th>
    	    </tr>
            <tr>
                <th style="background: #0cd3f3;" class='h1'>DESCRIPCION</th>
                <th style="background: #0cd3f3;" class='h1'>SUMA DE QTD(GL)</th>
                <th style="background: #0cd3f3;" class='h1'>SUMA DE QTD(L)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="background: #0cd3f3;" class='h1'>VEHICULOS PESADOS</th>
                <td class='h1'>{{ number_format($totalequipo,2,'.','') }}</td>
                <td class='h1'>{{ number_format($totalequipo2,2,'.','') }}</td>
            </tr>
            <tr>
                <th style="background: #0cd3f3;" class='h1'>VEHICULOS LIVIANOS</th>
                <td class='h1'>{{ number_format($totalvehiculo,2,'.','') }}</td>
                <td class='h1'>{{ number_format($totalvehiculo2,2,'.','') }}</td>
            </tr>
            <tr>
                <th style="background: #0cd3f3;" class='h1'>DESCUENTO</th>
                <td class='h1'>{{ number_format($totaldescuento,2,'.','') }}</td>
                <td class='h1'>{{ number_format($totaldescuento2,2,'.','') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th class='h1' style="background: #0cd3f3;">TOTAL</th>
                <td class='h1'>{{ number_format($totalequipo + $totalvehiculo + $totaldescuento,2,'.','') }}</td>
                <td class='h1'>{{ number_format($totalequipo2 + $totalvehiculo2 + $totaldescuento2,2,'.','') }}</td>
            </tr>
        </tfoot>
	</table>
	<br />
	<table class="table-borderless" width="100%">
        <tr class="table-borderless">
            <td class="table-borderless" colspan="3">
                
                <table width="100%" class="table-borderless">
                    <tr class="table-borderless">
                    @foreach($listapiepagina as $k=>$v)
                        <td class="text-bold text-center text-normal table-borderless" width="33%" style="vertical-align: bottom; height: 120px;">_______________________________</td>
                    @endforeach
                    </tr>
                    <tr class="table-borderless">
                    @foreach($listapiepagina as $k=>$v)
                        <td class="h1 text-center text-bold table-borderless">{{ $v->nombre }}</td>
                    @endforeach
                    </tr>
                    <tr class="table-borderless">
                    @foreach($listapiepagina as $k=>$v)
                        <td class="h1 text-center text-bold table-borderless">{{ $v->cargo }}</td>
                    @endforeach
                    </tr>
                </table>

            </td>
        </tr>
	</table>
</body>