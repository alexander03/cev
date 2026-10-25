<!DOCTYPE html>
<html lang="es">
<head>
    <title>{{ $namefile }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
    <style type="text/css">
        @page {
            margin-left: 29px;
            margin-top: 65px;
            margin-right: 29px;
        }
        body{
            font-size: 9.5px;
            font-family: "Arial";
            padding: 0px;
        }
        table{
            border-collapse: collapse;
        }
        tr{
            border-collapse: collapse;
        }
        td{
            border-collapse: collapse;
            border: 1.2px solid;
            height: 17.5px;
        }
        span{
            font-weight: normal;
        }
        .table-bordered{
            border-collapse: collapse;
            border: 1.2px solid; 
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
            background-color: blue;
        }
        .h2{
            font-size: 12px;
        }
        .text-top{
            vertical-align: text-top;
        }
        .text-center{
            text-align: center;
        }
        .text-right{
            text-align: right;
        }
        .text-white{
            color: white;
        }
        .text-bold{
            font-weight: bold;
            font-size:9.5px;
        }
        .fsize{
            font-size: 8px;
        }
        .text-normal{
            font-weight: normal;
        }
        .pequeno{
            height: 14px;
        }
        .pequeno2{
            height: 18px;
        }
        .items{
            height: 27.5px;
        }
        .letratitulopequeno{
            font-weight: bold;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr class="table-borderless">
            <td class="table-borderless">
                
                <table width="100%" style="margin-bottom: 0px">
                    <tr>
                        <td rowspan="4" width="16%" style="height: 10px" class="text-center pequeno">
                            @if($concesionaria_id=="2")
                                <img src="{{ asset('/assets/img/logo-main.png') }}" width="130" height="70">
                            @elseif($concesionaria_id=="1")
                                <img src="{{ asset('/assets/img/logo.jpg') }}" width="130" height="70">
                            @endif
                            <!--img src="data:image/jpeg;base64,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" width="110" height="60" /-->
                        </td>
                        <td rowspan="4" width="54%" style="background:#0070C0;font-size:16;height: 15px;"  class="text-center text-white bg-blue text-bold pequeno">
                            REGISTRO DE MANTENIMIENTO VEHICULAR
                            <br>
                            (TALLER CAMPAMENTO)
                        </td>
                        <td class="pequeno">
                            <span class="pequeno letratitulopequeno" style="height: 15px">CÓDIGO DEL FORMATO:</span>
                        </td>
                        <td class="text-center pequeno">
                            <span class="pequeno text-center" style="height: 15px;font-size: 8px;text-align: center;">AD-013</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pequeno">
                            <span class="pequeno letratitulopequeno" style="height: 15px;">REVISIÓN DEL FORMATO</span>
                        </td>
                        <td class="text-center pequeno">
                            <span class="pequeno text-center" style="height: 15px;font-size: 8px;">00</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pequeno">
                            <span class="pequeno letratitulopequeno" style="height: 15px;">FORMATO VIGENTE DESDE:</span>
                        </td>
                        <td class="text-center pequeno">
                            <span class="pequeno" style="height: 15px;font-size: 8px;";>24/JUL/2018</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pequeno">
                            <span class="pequeno letratitulopequeno" style="height: 15px;">PÁGINA DEL FORMATO:</span>
                        </td>
                        <td class="pequeno text-center">
                            <span style="height: 15px;font-size: 8px;text-align: center;">1 DE 1</span>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%" style="margin-top: 6px;">
                    <tr>
                        <td colspan="2">

                            <table width="100%" class="table-borderless">
                                <tr class="table-borderless">
                                    <td width="80%" class="table-borderless text-bold">CONCESIÓN:&nbsp;<span>Operación y Mantenimiento de las Obras de Trasvase del Proyecto Olmos </span></td>
                                </tr>
                            </table>

                        </td>
                        <td  class="text-bold text-center">FECHA DEL REGISTRO</td>
                    </tr>
                    <tr>
                        <td width="45%" class="text-bold">CONCESIONARIA:&nbsp;<span>{{ $concesionaria }}</span></td>
                        <td width="30%" class="text-bold">TALLER:&nbsp;<span>{{ $cliente }}</span></td>
                        <td width="25%" class="text-CENTER"><span>{{ $fecharegistro }}</span></td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%" class="table-bordered" style="margin-top: 6px;">
                    <tr>
                        <td colspan="4" class="text-center text-bold" style="background: #bdd7ee">DATOS GENERALES</td>
                    </tr>
                    
                    <tr>
                        <td width="30%" class="text-bold">UNIDAD:&nbsp;<span>{{ $unidad }}</span></td>
                        <td width="35%" class="text-bold">MARCA:&nbsp;<span>{{ $marca }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-bold">PLACA:&nbsp;<span>{{ $placa }}</span></td>
                        <td class="text-bold">MODELO:&nbsp;<span>{{ $modelo }}</span></td>
                        <?php $pr=" " ?>
                        <?php $co=" " ?>
                        <?php if($tipomantenimiento=="PREVENTIVO"){
                            $pr="X";
                        } else{
                            $co="X";
                        }
                        echo '<td ROWSPAN="2" class="table-borderless text-bold">&nbsp;&nbsp;PREVENTIVO: &nbsp;<span style="border: 1px solid;">&nbsp;&nbsp;'.$pr.'&nbsp;&nbsp;</span>
                        </td>
                        <td ROWSPAN="2" class="table-borderless text-bold">CORRECTIVO: &nbsp;<span style="border: 1px solid;">&nbsp;&nbsp;'.$co.'&nbsp;&nbsp;</span>
                        </td>'?>
                    </tr>
                    <tr>
                        <td class="text-bold">UA:&nbsp;<span>{{ $uacodigo }}</span></td>
                        <td class="text-bold">KM DE MANTENIMIENTO:&nbsp;<span>{{ $kmman }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-bold">KM DE INGRESO:&nbsp;<span>{{ $kminicial }}</span></td>
                        <td class="text-bold">KM DE SALIDA:&nbsp;<span>{{ $kmfinal }}</span></td>
                        <td colspan="2" class="text-bold table-bordered">TELEFONO:</td>
                    </tr>
                    <tr>
                        <td class="text-bold">FECHA DE INGRESO:&nbsp;<span>{{ $fechaentrada }}</span></td>
                        <td class="text-bold">FECHA DE SALIDA:&nbsp;<span>{{ $fechasalida }}</span></td>
                        <td colspan="2" class="table-bordered">&nbsp;<span>{{ $telefono }}</span></td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%" style="margin-top: 6px;">
                    <tr>
                        <td colspan="4" class="text-center text-bold" style="background: #bdd7ee">OBSERVACIONES</td>
                    </tr>
                    <tr style="background: #bdd7ee">
                        <td width="4%" class="text-bold" style="text-align: center;">ITEM</td>
                        <td width="6%" class="text-bold" style="text-align: center;">CANT.</td>
                        <td width="81%" class="text-bold" style="text-align: center;">DESCRIPCIÓN DE TRABAJOS</td>
                        <td width="8%" class="text-bold" style="text-align: center;">COSTO</td>
                    </tr>
                    <?php $total=0 ?>
                    <?php $contador=0 ?>
                    @foreach($observaciones as $key => $obs)
                        <?php $contador++; ?>
                    <tr>
                        <td class="text-center items">{{$contador}}</td>
                        <td class="text items text-center">{{$obs->cantidad}}</td>
                        <td class="text items">{{$obs->descripcion}}</td>
                        <td class="text items text-right">{{number_format($obs->monto*$obs->cantidad, 2, '.', '')}}</td>
                        <?php $total+=$obs->monto*$obs->cantidad ?>
                    </tr>
                    @endforeach
                    <?php $faltan=20-count($observaciones); ?>
                    @for($i=0;$i<$faltan;$i++)
                    <?php $contador++; ?>
                    <tr>
                        <td class="text-center items">{{$contador}}</td>
                        <td class="text items"></td>
                        <td class="text items"></td>
                        <td class="text items"></td>
                    </tr>
                    @endfor
                    <tr >
                        <td class="text table-borderless pequeno2"></td>
                        <td class="text table-borderless pequeno2"></td>
                        <td class="text table-borderless pequeno2" align="right">SUBTOTAL</td>
                        <td class="table-bordered pequeno2 text-right">{{number_format($total, 2, '.', '')}}</td>
                    </tr>
                    <tr>
                        <td class="text table-borderless pequeno2"></td>
                        <td class="text table-borderless pequeno2"></td>
                        <td class="text table-borderless pequeno2" align="right">IGV</td>
                        <td class="table-bordered pequeno2 text-right">{{number_format($total*0.18, 2, '.', '')}}</td>
                    </tr>
                    <tr>
                        <td class="text table-borderless pequeno2"></td>
                        <td class="text table-borderless pequeno2"></td>
                        <td class="text table-borderless pequeno2" align="right">TOTAL</td>
                        <td class="table-bordered pequeno2 text-right">{{number_format($total*1.18, 2, '.', '')}}</td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr></tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%" style="margin-top: 6px;">
                    <tr style="margin-top: 6px;background: #bdd7ee;">
                        <td width="33%" class="text-bold" align="center">CONDUCTOR</td>
                        <td width="33%" class="text-bold" align="center">MECANICO</td>
                        <td width="33%" class="text-bold" align="center">AUTORIZA</td>
                    </tr>
                    <tr>
                        <td width="33%" class="text-bold" style="padding-top:25px;">FIRMA:</td>
                        <td width="33%" class="text-bold" style="padding-top:25px;">FIRMA:</td>
                        <td width="33%" class="text-bold" style="padding-top:25px;">FIRMA:</td>
                    </tr>
                    <tr>
                        <td width="33%" class="text-bold">NOMBRE:</td>
                        <td width="33%" class="text-bold">NOMBRE:</td>
                        <td width="33%" class="text-bold">NOMBRE:</td>
                    </tr>
                    <tr>
                        <td width="33%" class="text-bold">TELEFONO</td>
                        <td width="33%" class="text-bold">TELEFONO</td>
                        <td width="33%" class="text-bold">TELEFONO</td>
                    </tr>

                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td align="center" style="font-size: 7px;padding-top:10.5px;">“Este documento es propiedad de CONCESIONARIA TRASVASE OLMOS S.A. y no podrá ser copiado o transferido a terceros sin autorización de la empresa”</td>
        </tr>
    </table>
</body>
</html>