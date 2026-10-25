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
        .h2{
            font-size: 12px;
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
    </style>
</head>
<body>
    <table width="100%">
        <tr class="table-borderless">
            <td class="table-borderless">
                
                <table width="100%">
                    <tr>
                        <td rowspan="4" width="20%" class="text-center">
                            @if($concesionaria_id=="2")
                                <img src="{{ asset('/assets/img/logo-main.png') }}" width="130" height="70">
                            @elseif($concesionaria_id=="1")
                                <img src="{{ asset('/assets/img/logo.jpg') }}" width="130" height="70">
                            @endif
                        </td>
                        <td rowspan="2" width="50%" class="text-center text-white bg-blue text-bold">
                            CHECK LIST VEHICULAR
                        </td>
                        <td>
                            <span class="text-bold">CÓDIGO DEL FORMATO:&nbsp;</span>ADM-SG-040
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-bold">REVISIÓN DEL FORMATO:&nbsp;</span>00
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            
                            <table width="100%" class="table-borderless">
                                <tr class="table-borderless">
                                    <td width="20%" class="text-bold table-borderless">
                                        <h3>UNIDAD MOVIL:</h3>
                                    </td>
                                    <td width="30%" class="text-center text-bold table-borderless">
                                        <h3>DIRECTO</h3>&nbsp;&nbsp;
                                        <input type="checkbox" id="directo" @if ( $directo == 1 ) checked="true" @endif>
                                    </td>
                                    <td width="30%" class="text-center text-bold table-borderless">
                                        <h3>CONTRATISTA</h3>&nbsp;&nbsp;
                                        <input type="checkbox" id="contratista" @if ( $directo == 0 ) checked="true" @endif>
                                    </td>
                                </tr>
                            </table>

                        </td>
                        <td>
                            <span class="text-bold">FORMATO VIGENTE DESDE:&nbsp;</span>25/06/2017
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-bold">PÁGINA DEL FORMATO:&nbsp;</span>1 DE 1
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%">
                    <tr>
                        <td colspan="3">

                            <table width="100%" class="table-borderless">
                                <tr class="table-borderless">
                                    <td width="33%" class=" text-bold">PLACA:&nbsp;<span>{{ $placa }}</span></td>
                                    <td class=" text-bold">UA:&nbsp;<span>{{ $ua }}</span></td>
                                    <td class=" text-bold">UA TRABAJO:&nbsp;<span>{{ $ua2 }}</span></td>
                                </tr>
                            </table>

                        </td>
                        <td colspan="2" class="text-bold">LÍDER ÁREA:&nbsp;<span>{{ $lider_area }}</span></td>
                    </tr>
                    <tr>
                        <td width="17%" class="text-bold">FECHA:&nbsp;<span>{{ $fecha_registro }}</span></td>
                        <td width="16%" class="text-bold">MARCA:&nbsp;<span>{{ $marca }}</span></td>
                        <td width="17%" class="text-bold">MODELO:&nbsp;<span>{{ $modelo }}</span></td>
                        <td width="25%" class="text-bold">COLOR:&nbsp;<span>{{ $color }}</span></td>
                        <td width="25%" class="text-bold">COMBUSTIBLE:&nbsp;<span>{{ $combustible }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-bold">EMP. CONTRATISTA:&nbsp;<span>{{ $contratista }}</span></td>
                        <td colspan="2" class="text-bold">CONDUCTOR:&nbsp;<span>{{ $conductor }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-bold">Hora Inicio:&nbsp;<span>{{ $hora_inicio }}</span></td>
                        <td class="text-bold">Hora Fin:&nbsp;<span>{{ $hora_fin }}</span></td>
                        <td rowspan="2" class="text-bold text-top">
                            <p class="text-bold">VENCIMIENTO DEL SOAT:</p>
                            <span class="text-normal">{{ $fecha_soat }}</span>
                        </td>
                        <td colspan="2" class="text-bold">KILOMETRAJE:</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-bold text-top">CATEGORIA Y N° DE LICENCIA:&nbsp;<span>{{ $licencia }}</span></td>
                        <td class="text-bold">Inicial:&nbsp;<span>{{ $k_inicial }}</span></td>
                        <td class="text-bold">Final:&nbsp;<span>{{ $k_final }}</span></td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%">
                    <tr>
                        <td colspan="6" class="text-center text-bold"><h2>REVISION DE ACCESORIOS / VEHICULO</h2></td>
                    </tr>

                    @for ($i = 0; $i <= $limite; $i++)
                        
                        <tr>
                            @if ( $i < ($limite) )
                            @if ( $lista[$i]['title'] ) 
                            <td width="30%" class="text-center text-bold"><h3> {{ $lista[$i]['caption'] }} </h3></td>
                            @else
                            <td width="30%"> {{ $lista[$i]['caption'] }} </td>
                            @endif

                            <td width="10%" class="text-center text-bold"><h3> {{ $lista[$i]['value_yes'] }} </h3></td>
                            <td width="10%" class="text-center text-bold"><h3> {{ $lista[$i]['value_not'] }} </h3></td>
                            @else
                            <td></td>
                            <td></td>
                            <td></td>
                            @endif

                            <?php $j = $i+$limite; ?>

                            @if ( $j < 47 ) 
                            @if ( $lista[$j]['title'] ) 
                            <td width="30%" class="text-center text-bold"><h3> {{ $lista[$j]['caption'] }} </h3></td>
                            @else
                            <td width="30%"> {{ $lista[$j]['caption'] }} </td>
                            @endif

                            <td width="10%" class="text-center text-bold"><h3> {{ $lista[$j]['value_yes'] }} </h3></td>
                            <td width="10%" class="text-center text-bold"><h3> {{ $lista[$j]['value_not'] }} </h3></td>
                            @endif
                        </tr>
                    @endfor

                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%">
                    <tr>
                        <td class="text-bold"><h2>OBSERVACIONES:</h2></td>
                    </tr>
                    <tr>
                        <td class="text-top text-normal" style="height: 110px; padding: 10px">
                            {{ str_replace("\r\n"," (*) ","(*) ".$observaciones) }}
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%">
                    <tr>
                        <td class="text-bold"><h2>INCIDENTES:</h2></td>
                    </tr>
                    <tr>
                        <td class="text-top text-normal" style="height: 110px; padding: 10px">
                            {{ str_replace("\r\n"," (*) ","(*) ".$incidentes) }}
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">

                <table width="100%">
                    <tr>
                        <td>

                            <table width="100%" class="table-borderless">
                                <tr class="table-borderless">
                                    <td class="text-bold text-center text-normal table-borderless" style="vertical-align: bottom; height: 40px;"><img src="{{asset('files/documento_conductor/imagenes_firmas')}}/{{$firma}}" width="110" height="30"></td>
                                    <td class="text-bold text-center text-normal table-borderless" style="vertical-align: bottom;">_______________________________</td>
                                    <td class="text-bold text-center text-normal table-borderless" style="vertical-align: bottom;">_______________________________</td>
                                    <td class="text-bold text-center text-normal table-borderless" style="vertical-align: bottom;">_______________________________</td>
                                </tr>
                                <tr class="table-borderless">
                                    <td class="text-center h2 text-normal table-borderless">CONDUCTOR</td>
                                    <td class="text-center h2 text-normal table-borderless">SUP. SSTMA</td>
                                    <td class="text-center h2 text-normal table-borderless">LIDER DE AREA</td>
                                    <td class="text-center h2 text-normal table-borderless">ADMINISTRACION</td>
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