<!DOCTYPE html>
<html lang="es">
<head>
    <title>{{ $namefile }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
    <style type="text/css">
        body{
            font-size: 8px;
            font-family: "Arial";
        }
        table{
            border-collapse: collapse;
            border: 2px;
        }
        tr{
            border-collapse: collapse;
            border: 2px solid;
        }
        td{
            border-collapse: collapse;
            border: 2px solid;
            height: 19px;
        }
        span{
            font-weight: normal;
        }
        .table-bordered{
            border-collapse: collapse;
            border: 2px solid; 
        }
        .table-borderless{
            border-collapse: collapse;
            border: none;
        }
        .table-borderlight{
            border-collapse: collapse;
            border: 0.5px;
        }
        .bg-blue{
            background-color: #0070c0;
        }
        .h1{
            font-size: 16px;
        }
        .h2{
            font-size: 14px;
        }
        .h3{
            font-size: 12px;
        }
        .h4{
            font-size: 9px;
        }
        .text-danger{
            color: red;
        }
        .text-primary{
            color: blue;
        }
        .text-top{
            vertical-align: text-top;
        }
        .text-center{
            text-align: center;
        }
        .text-white{
            color: white;
        }
        .text-bold{
            font-weight: bold;
        }
        .fsize{
            font-size: 8px;
        }
        .text-normal{
            font-weight: normal;
        }
        .title{
            background:#c1e2fc;
        }
        .yellow{
            background:yellow;
        }
    </style>
</head>
<body>
    <table width="100%">
        
        <tr class="table-borderless">
            <td class="table-borderless" colspan="3">
                
                <table width="100%">
                    <tr style="background-color:#fdff8f">
                        <td width="20%" class="text-center table-borderless">
                            @if($concesionaria_id=="2")
                                <img src="{{ asset('/assets/img/logo-main.png') }}" width="130" height="70">
                            @elseif($concesionaria_id=="1")
                                <img src="{{ asset('/assets/img/logo.jpg') }}" width="130" height="70">
                            @endif
                        </td>
                        <td width="60%" class="text-center table-borderless">
                            <h1>MEDICIÓN DE ALQUILER DE EQUIPO LIVIANO</h1>
                        </td>
                        <td width="20%" class="table-borderless"></td>
                    </tr>
                </table>

            </td>
        </tr>

        <tr class="table-borderless">
            <td class="table-borderless" colspan="3">
                
                <table width="70%" class="table-borderless">
                    <tr class="table-borderless">
                        <td width="30%" class="text-left table-borderless text-bold h3">CONCESIONARIA:</td>
                        <td width="70%" class="text-left table-borderless h3 text-danger">{{ $concesionaria }}</td>
                    </tr>
                    <tr class="table-borderless">
                        <td width="30%" class="text-left table-borderless text-bold h3">SUBCONTRATISTA:</td>
                        <td width="70%" class="text-left table-borderless h3 text-danger">{{ $subcontratista }}</td>
                    </tr>
                    <tr class="table-borderless">
                        <td width="30%" class="text-left table-borderless text-bold h3">PERIODO:</td>
                        <td width="70%" class="text-left table-borderless h3 text-danger">{{ $periodo }}</td>
                    </tr>
                </table>

            </td>
        </tr>

        <tr class="table-borderless">
            <td class="table-borderless">
                
                <table width="100%">
                    <thead>
                        <?php $moneda = 0;
                        foreach ($listSummaryEquipos as $equipo){
                            $moneda = $equipo["moneda"];
                        }
                        if($moneda==0){
                            $mon = "S/";
                        }else{
                            $mon = "US$";
                        }
                        ?>
                        <tr>
                            <td class="text-center text-bold title" style="height: 50px;width: 80px;">CODIGO</td>
                            <td class="text-center text-bold title" style="width: 100px;">UA TRABAJO</td>
                            <td class="text-center text-bold title" style="width: 100px;">EQUIPO</td>
                            <td class="text-center text-bold title">PLACA</td>
                            <td class="text-center text-bold title">UNIDAD</td>
                            <td class="text-center text-bold title">CANTIDAD</td>
                            <td class="text-center text-bold title">VALOR UNITARIO ({{ $mon }})</td>
                            <td class="text-center text-bold title">SUBTOTAL ({{ $mon }})</td>
                        </tr>
                        <tr class="table-borderless"><td class="table-borderless" style="height: 5px;"></td></tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($listSummaryEquipos as $equipo)

                        <tr>
                            <td class="text-center text-bold" style="height: 30px;width: 50px;">{{ $equipo['codigo'] }}</td>
                            <td class="text-center text-bold" style="width: 100px;">{{ $equipo['ua'] }}</td>
                            <td class="text-center text-bold" style="width: 100px;">{{ $equipo['descripcion'] }}</td>
                            <td class="text-center text-bold">{{ $equipo['placa'] }}</td>
                            <td class="text-center text-bold">{{ $equipo['unidad'] }}</td>
                            <td class="text-center text-bold">{{ $equipo['cantidad'] }}</td>
                            <td class="text-center text-bold">{{ $equipo['valorUnitario'] }}</td>
                            <td class="text-center text-bold">{{ $equipo['subtotal'] }}</td>
                        </tr>
                        <tr class="table-borderless"><td class="table-borderless" style="height: 5px;"></td></tr>

                        @endforeach

                    </tbody>
                    <tFoot class="table-borderless">
                        <tr>
                            <td colspan="6" class="table-borderless"></td>
                            <td class="text-center text-primary text-bold yellow">SUBTOTAL</td>
                            <td class="text-center text-primary text-bold yellow">{{ $mon." ".$subtotal }}</td>
                        </tr>
                    </tFoot>
                </table>

            </td>
            <td class="table-borderless" style="width: 5px;"></td>
            <td class="table-borderless">
                <?php /*
                <table width="100%">
                    <thead>
                        <tr>
                            <td class="text-center text-bold text-primary title" style="height: 50px;">RESPONSABLE</td>
                        </tr>
                        <tr class="table-borderless"><td class="table-borderless" style="height: 5px;"></td></tr>
                    </thead>
                    <tbody>
                        @foreach ($listSummaryEquipos as $equipo)

                        <tr>
                            <td class="text-center text-bold text-primary" style="height: 30px;">{{ $equipo['responsable'] }}</td>
                            <td class="text-center" style="height: 30px;">_____________________________</td>
                        </tr>
                        <tr class="table-borderless"><td class="table-borderless" style="height: 5px;"></td></tr>

                        @endforeach
                    </tbody>
                    <tFoot>
                        <tr class="table-borderless">
                            <td class="table-borderless"></td>
                        </tr>
                    </tFoot>
                </table>
                */ ?>
            </td>
        </tr>

        <tr class="table-borderless">
            <td class="table-borderless" colspan="3">
                
                <table width="100%" class="table-borderless">
                    <tr class="table-borderless">
                    @foreach($listapiepagina as $k=>$v)
                        <td class="text-bold text-center text-normal table-borderless" width="33%" style="vertical-align: bottom; height: 120px;">_________________________________________</td>
                    @endforeach
                    </tr>
                    <tr class="table-borderless">
                    @foreach($listapiepagina as $k=>$v)
                        <td class="h4 text-center text-bold table-borderless">{{ $v->nombre }}</td>
                    @endforeach
                    </tr>
                    <tr class="table-borderless">
                    @foreach($listapiepagina as $k=>$v)
                        <td class="h4 text-center text-bold table-borderless">{{ $v->cargo }}</td>
                    @endforeach
                    </tr>
                </table>

            </td>
        </tr>
        
    </table>
</body>
</html>