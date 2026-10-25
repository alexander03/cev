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
            height: 20.5px;
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
                        </td>
                        <td rowspan="4" width="54%" style="background:#bdd7ee;font-size:20;height: 15px;"  class="text-center text-bold pequeno">
                            DOC - MAT
                        </td>
                        <td class="pequeno" style="background: #bdd7ee;">
                            <span class="pequeno letratitulopequeno" style="height: 15px">CÓDIGO DEL FORMATO:</span>
                        </td>
                        <td class="text-center pequeno" style="background: #bdd7ee;">
                            <span class="pequeno text-center" style="height: 15px;font-size: 8px;text-align: center;">LOG-001</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pequeno" style="background: #bdd7ee;">
                            <span class="pequeno letratitulopequeno" style="height: 15px;">REVISIÓN DEL FORMATO</span>
                        </td>
                        <td class="text-center pequeno" style="background: #bdd7ee;">
                            <span class="pequeno text-center" style="height: 15px;font-size: 8px;">01</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pequeno" style="background: #bdd7ee;">
                            <span class="pequeno letratitulopequeno" style="height: 15px;">FORMATO VIGENTE DESDE:</span>
                        </td>
                        <td class="text-center pequeno" style="background: #bdd7ee;">
                            <span class="pequeno" style="height: 15px;font-size: 8px;";>01/09/2022</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pequeno" style="background: #bdd7ee;">
                            <span class="pequeno letratitulopequeno" style="height: 15px;">PÁGINA DEL FORMATO:</span>
                        </td>
                        <td class="pequeno text-center" style="background: #bdd7ee;">
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
                                    <td width="80%" style="background: #bdd7ee;" class="table-borderless text-bold">CONCESIÓN:&nbsp;<span>Operación y Mantenimiento de las Obras de Trasvase del Proyecto Olmos </span></td>
                                </tr>
                            </table>

                        </td>
                        <td  class="text-bold text-center" rowspan="2" style="font-size: 20px;">{{ explode("-",$docmat->numero)[0] }}-<span style="color:red;font-weight: bold;">{{ explode("-",$docmat->numero)[1] }}</span></td>
                    </tr>
                    <tr>
                        <td width="45%" style="background: #bdd7ee;" class="text-bold">CONCESIONARIA:&nbsp;<span>{{ $concesionaria }}</span></td>
                        <td width="30%" style="background: #bdd7ee;" class="text-bold">CLIENTE:&nbsp;<span>Gobierno Regional de Lambayeque</span></td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%" class="table-bordered" style="margin-top: 6px;">
                    <tr>
                        <td width="25%" class="table-borderless text-bold">&nbsp;&nbsp;&nbsp;PRESUPUESTADO</td>
                        <td width="25%" class="table-borderless text-bold">TIPO DE OPERACI&Oacute;N</td>
                        <td class="text-bold table-borderless">CONDICI&Oacute;N DE ENTREGA</td>
                        <td class="text-bold">C&Oacute;DIGO ORACLE:</td>
                        <td class="text-center">{{$docmat->codigo}}</td>
                    </tr>
                    <tr>
                        <td class="table-borderless">&nbsp;&nbsp;&nbsp;<span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->presupuestado=="S"?"X":"&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;SI</td>
                        <td class="table-borderless"><span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->tipooperacion=="S"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;SOLICITUD DE COMPRA/SERVICIO</td>
                        <td class="table-borderless"><span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->condicionentrega=="NORMAL"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;NORMAL</td>
                        <td class="text-bold">FECHA DE EMISI&Oacute;N:</td>
                        <td class="text-center">{{date("d/m/Y",strtotime($docmat->fecha))}}</td>
                    </tr>
                    <tr>
                        <td class="table-borderless">&nbsp;&nbsp;&nbsp;<span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->presupuestado=="N"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;NO</td>
                        <td class="table-borderless"><span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->tipooperacion=="B"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;BAJA DE STOCK</td>
                        <td class="table-borderless"><span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->condicionentrega=="URGENCIA"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;URGENCIA</td>
                        <td class="text-bold">FECHA DE NECESIDAD:</td>
                        <td class="text-center">{{$docmat->fechanecesidad==""?"":date("d/m/Y",strtotime($docmat->fechanecesidad))}}</td>
                    </tr>
                    <tr>
                        <td class="table-borderless"></td>
                        <td class="table-borderless"><span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->tipooperacion=="E"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;ENTRADA INTERNA</td>
                        <td class="table-borderless"><span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->condicionentrega=="EMERGENCIA"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;EMERGENCIA</td>
                        <td class="text-bold" rowspan="2">UA:</td>
                        <td class="text-center" rowspan="2">{{(!is_null($docmat->vehiculo)?$docmat->vehiculo->ua->codigo:$docmat->equipo->ua->codigo)}}</td>
                    </tr>
                    <tr>
                        <td class="table-borderless"></td>
                        <td class="table-borderless"><span style="border: 1px solid;">&nbsp;&nbsp;{!! (($docmat->tipooperacion=="A" or $docmat->tipooperacion=="F")?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;AJUSTE&nbsp;&nbsp;&nbsp;&nbsp;Sobra<span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->tipooperacion=="A"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;Falta<span style="border: 1px solid;">&nbsp;&nbsp;{!! ($docmat->tipooperacion=="F"?"X":"&nbsp;&nbsp;") !!}&nbsp;&nbsp;</span></td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%" style="margin-top: 6px;">
                    <tr style="background: #bdd7ee">
                        <td width="4%" rowspan="2" class="text-bold" style="text-align: center;">ITEM</td>
                        <td width="6%" rowspan="2" class="text-bold" style="text-align: center;">CANT.</td>
                        <td width="6%" rowspan="2" class="text-bold" style="text-align: center;">UND.</td>
                        <td width="6%" rowspan="2" class="text-bold" style="text-align: center;">C&Oacute;DIGO</td>
                        <td width="50%" class="text-bold" style="text-align: center;">DESCRIPCI&Oacute;N DE MATERIAL, ACTIVO y/o SERVICIO</td>
                        <td width="15%" class="text-bold" style="text-align: center;">PROVEEDOR 1</td>
                        <td width="15%" class="text-bold" style="text-align: center;">PROVEEDOR 2</td>
                        <td width="15%" class="text-bold" style="text-align: center;">PROVEEDOR 3</td>
                    </tr>
                    <tr>
                        <td style="background: #bdd7ee">ESPECIFICACIO&acute;N, MARCA, MODELO, SERIE, MEDIDAS Y EN CASOS ESPECIALES ACOMPAÑAR COPIA DE CATALOGOS O PANOS</td>
                        <td width="15%" class="text-bold" style="text-align: center;"></td>
                        <td width="15%" class="text-bold" style="text-align: center;"></td>
                        <td width="15%" class="text-bold" style="text-align: center;"></td>
                    </tr>
                    <?php $total=0 ?>
                    <?php $contador=0 ?>
                    @foreach($detalles as $key => $obs)
                        <?php $contador++; ?>
                    <tr>
                        <td class="text-center items">{{$contador}}</td>
                        <td class="text items text-center">{{$obs->cantidad}}</td>
                        <td class="text items text-center">{{$obs->unidad}}</td>
                        <td class="text items text-center">{{$obs->codigo}}</td>
                        <td class="text items">{{$obs->descripcion}}</td>
                        <td class="text items">{{$obs->proveedor1}}</td>
                        <td class="text items">{{$obs->proveedor2}}</td>
                        <td class="text items">{{$obs->proveedor3}}</td>
                    </tr>
                    @endforeach
                    <?php $faltan=10-count($detalles); ?>
                    @for($i=0;$i<$faltan;$i++)
                    <?php $contador++; ?>
                    <tr>
                        <td class="text-center items">{{$contador}}</td>
                        <td class="text items"></td>
                        <td class="text items"></td>
                        <td class="text items"></td>
                        <td class="text items"></td>
                        <td class="text items"></td>
                        <td class="text items"></td>
                        <td class="text items"></td>
                    </tr>
                    @endfor
                
                    <tr class="table-borderless">
                        <td colspan="8">
                            <table class="table-bordered" width="100%">
                                <tr>
                                    <td colspan="2" class="text-bold" align="center">TIPO DE COMPRA</td>
                                    <td class="text-bold" align="center">&nbsp;&nbsp;TRABAJO A REALIZAR </td>
                                    <td class="text-bold" align="center">CONDICI&Oacute;N DE ENTREGA</td>
                                    <td class="text-bold" style="background: #bdd7ee;" align="center">NORMAL (D&iacute;as)</td>
                                    <td class="text-bold" style="background: #bdd7ee;" align="center">URGENCIA (D&iacute;as)</td>
                                    <td class="text-bold" style="background: #bdd7ee;" align="center">EMERGENCIA (D&iacute;as)</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;APLICACI&Oacute;N</td>
                                    <td align="center">{{$docmat->tipocompra=="A"?"X":""}}</td>
                                    <td rowspan="2">&nbsp;&nbsp;{{$docmat->trabajo}}</td>
                                    <td align="center">MATERIALES Y/O EQUIPOS MERCADO NACIONAL</td>
                                    <td align="center">8</td>
                                    <td align="center">5</td>
                                    <td align="center">2</td>
                                </tr>
                                <tr>
                                    <td>STOCK</td>
                                    <td align="center">{{$docmat->tipocompra=="S"?"X":""}}</td>
                                    <td align="center">MATERIALES Y/O EQUIPOS MERCADO INTERNACIONAL</td>
                                    <td align="center">60</td>
                                    <td align="center">45</td>
                                    <td align="center">20</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="table-borderless">
                        <td class="table-borderless" colspan="8">

                            <table width="100%" style="margin-top: 6px;">
                                <tr>
                                     <td align="left" colspan="5" style="font-size: 10px;">* Plazos consideran que los materiales requeridos se ecuentran en condición de entrega <b>inmediata</b>. Para casos donde los materiales necesitan ser fabricados o importados; la entrega quedará en función del plazo dado por el proveedor</td>
                                </tr>
                                <tr>
                                    <td width="30%" class="text-bold" align="center">Documentos a solicitar a proveedor</td>
                                    <td width="18%" class="text-bold" style="background: #bdd7ee;" align="center">SOLICITA</td>
                                    <td width="18%" class="text-bold" style="background: #bdd7ee;" align="center">AUTORIZA</td>
                                    <td width="18%" class="text-bold" style="background: #bdd7ee;" align="center">APRUEBA</td>
                                    <td width="18%" class="text-bold" style="background: #bdd7ee;" align="center">LOG&Iacute;STICA</td>
                                </tr>
                                <tr>
                                    <td width="30%">&nbsp;a) Certificado de calidad, items(s) N°:</td>
                                    <td width="18%" rowspan="3" class="text-bold" style="padding-top: 30px;" >FIRMA:</td>
                                    <td width="18%" rowspan="3" class="text-bold" style="padding-top: 30px;" >FIRMA:</td>
                                    <td width="18%" rowspan="3" class="text-bold" style="padding-top: 30px;" >FIRMA:</td>
                                    <td width="18%" rowspan="3" class="text-bold" style="padding-top: 30px;" >FIRMA:</td>
                                </tr>
                                <tr>
                                    <td width="30%">&nbsp;b) Certificado de calibración, items(s) N°:</td>
                                </tr>
                                <tr>
                                    <td width="30%">&nbsp;c) Hoja de seguridad, items(s) N°:</td>
                                </tr>
                                <tr>
                                    <td width="30%">&nbps;d) Otros:</td>
                                    <td width="18%" class="text-bold">NOMBRE:</td>
                                    <td width="18%" class="text-bold">NOMBRE:</td>
                                    <td width="18%" class="text-bold">NOMBRE:</td>
                                    <td width="18%" class="text-bold">NOMBRE:</td>
                                </tr>

                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>