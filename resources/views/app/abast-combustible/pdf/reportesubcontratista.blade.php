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
	.amarillo{
	    background: yellow;
	}
	.num{
		width: 60px;
	}

	.nombres{
		width: 300px;
	}
	.left{
	   text-align: left; 
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
        <img src="{{ asset('/assets/img/logo-main.png') }}" width="90" height="45" style="padding-bottom:10px;">
    @elseif($concesionaria_id=="1")
        <img src="{{ asset('/assets/img/logo.jpg') }}" width="90" height="45" style="padding-bottom:10px;">
    @endif
    <table>
        <thead>
            <tr>
                <th style="background: #0cd3f3;">PERIODO</th>
                <td colspan='10' class='left'>{{ date("d/m/Y",strtotime($fechainicial))." AL ".date("d/m/Y",strtotime($fechafinal)) }}</td>
            </tr>            
        </thead>
        <tr>
            <th style="background: #0cd3f3;" class="num">FECHA</th>
            <th style="background: #0cd3f3;" class="nombres">EMPRESA</th>
            <th style="background: #0cd3f3;">GRIFO</th>
            <th style="background: #0cd3f3;">VEHICULO/EQUIPO</th>
            <th style="background: #0cd3f3;">PLACA</th>
            <th style="background: #0cd3f3;">MARCA</th>
            <th style="background: #0cd3f3;">MOTIVO</th>
            <th style="background: #0cd3f3;">UA</th>
            <th style="background: #0cd3f3;">QTD(Gl)</th>
            <th style="background: #0cd3f3;">QTD(Lt)</th>
            <th style="background: #0cd3f3;">FIRMA</th>
        </tr>

    <?php
    $contratista="";$total1=0;$total2=0;$totalequipo=0;$totalvehiculo=0;$totalequipo2=0;$totalvehiculo2=0;$totaldescuento=0;$totaldescuento2=0;
    ?>
    @foreach($data as $index=>$value)
        <?php
        $total1=$total1+$value->qtdgl;
    	$total2=$total2+$value->qtdl;
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
        ?>
			<tr>
				<td class="num">{{ date("d/m/Y",strtotime($value->fecha)) }}</td>
				<td class="nombres" >{{ $value->contratista }}</td>
				<td>{{ $value->grifo }}</td>
				<td>{{ $value->desceq }}</td>
				<td>{{ $value->eqpl }}</td>
				<td>{{ $value->descmc }}</td>
				<td>{{ $value->motivo }}</td>
				<td>{{ $value->code }}</td>
				<td>{{ $value->qtdgl }}</td>
				<td>{{ $value->qtdl }}</td>
                @if($tipo=="pdf")
				<td><img src="{{asset('files/documento_conductor/imagenes_firmas')}}/{{$value->firma}}" width="130" height="70"></td>
                @endif
			</tr>
		@endforeach
		<tfoot>
		    <tr>
		        <th style="background: #0cd3f3;" colspan='8'>TOTAL</th>
		        <th style="background: #0cd3f3;">{{ number_format($total1,2,'.','') }}</th>
		        <th style="background: #0cd3f3;">{{ number_format($total2,2,'.','') }}</th>
		    </tr>
		</tfoot>
	</table>
	<p></p>
	<table align='center'>
        <thead>
    	    <tr>
    	        <th style="background: #0cd3f3;" colspan="3">COMBUSTIBLE</th>
    	    </tr>
            <tr>
                <th style="background: #0cd3f3;">DESCRIPCION</th>
                <th style="background: #0cd3f3;">SUMA DE QTD(GL)</th>
                <th style="background: #0cd3f3;">SUMA DE QTD(L)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="background: #0cd3f3;">VEHICULOS PESADOS</th>
                <td>{{ number_format($totalequipo,2,'.','') }}</td>
                <td>{{ number_format($totalequipo2,2,'.','') }}</td>
            </tr>
            <tr>
                <th style="background: #0cd3f3;">VEHICULOS LIVIANOS</th>
                <td>{{ number_format($totalvehiculo,2,'.','') }}</td>
                <td>{{ number_format($totalvehiculo2,2,'.','') }}</td>
            </tr>
            <tr>
                <th style="background: #0cd3f3;">DESCUENTO</th>
                <td>{{ number_format($totaldescuento,2,'.','') }}</td>
                <td>{{ number_format($totaldescuento2,2,'.','') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th style="background: #0cd3f3;">TOTAL</th>
                <td>{{ number_format($totalequipo + $totalvehiculo + $totaldescuento,2,'.','') }}</td>
                <td>{{ number_format($totalequipo2 + $totalvehiculo2 + $totaldescuento2,2,'.','') }}</td>
            </tr>
        </tfoot>
	</table>
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