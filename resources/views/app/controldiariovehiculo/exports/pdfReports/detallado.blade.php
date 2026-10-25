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
            <td class="table-borderless" width="80%">
                
                <table width="100%">
                    <thead>
                        <tr>
                            <td class="text-center text-bold title" style="height: 50px;width: 80px;">CODIGO</td>
                            <td class="text-center text-bold title" style="width:100px;">UA TRABAJO</td>
                            <td class="text-center text-bold title" style="width:100px;">EQUIPO</td>
                            <td class="text-center text-bold title" style="width:80px;">PLACA</td>
                            <td class="text-center text-bold title">PERIODO</td>
                            <td class="text-center text-bold title text-primary">DIAS VALORIZADOS<br />(A)</td>
                            <td class="text-center text-bold title text-danger">DIAS MANTENIMIENTO<br />(B)</td>
                            <td class="text-center text-bold title">PAGO MENSUAL</td>
                        </tr>
                        <tr class="table-borderless"><td class="table-borderless" style="height: 5px;"></td></tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($listSummaryEquipos as $equipo)

                        <tr>
                            <td class="text-center text-bold" style="height: 30px;width: 80px;">{{ $equipo['codigo'] }}</td>
                            <td class="text-center text-bold" style="width:100px;">{{ $equipo['ua'] }}</td>
                            <td class="text-center text-bold" style="width:100px;">{{ $equipo['descripcion'] }}</td>
                            <td class="text-center text-bold" style="width:80px;">{{ $equipo['placa'] }}</td>
                            <td class="text-center text-bold">{{ $equipo['dias_permanencia'] }}</td>
                            <td class="text-center text-bold text-primary">{{ $equipo['a'] }}</td>
                            <td class="text-center text-bold text-danger">{{ $equipo['b'] }}</td>
                            <td class="text-center text-bold">{{ $equipo['f'] }}</td>
                        </tr>
                        <tr class="table-borderless"><td class="table-borderless" style="height: 5px;"></td></tr>

                        @endforeach

                    </tbody>
                </table>

            </td>
            <td class="table-borderless">&nbsp;</td>
            <td class="table-borderless" width="19%">
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
                        </tr>
                        <tr class="table-borderless"><td class="table-borderless" style="height: 5px;"></td></tr>

                        @endforeach
                    </tbody>
                </table>
                */ ?>
            </td>
        </tr>

        <tr class="table-borderless">
            <td class="table-borderless" colspan="3">
                
                <table width="100%">
                    @for ($r = 0; $r <= $max_row; $r++)
                        @if ( isset($listEquipos[$r]) )
                            <?php $row = $listEquipos[$r];
                            $color='';?>
                            <tr style="<?php if($r==0) echo "background-color: #9fa3a7;";?>">
                                @for ($c = 0; $c <= $max_col; $c++)
                                    @if ( isset($row[$c]) )
                                        <?php $col = $row[$c] ?>
                                        @if ( $col == $free_value )
                                            <td></td>
                                        @elseif ( isset($col['value']) )
                                            <?  if($col['value']=="HORAS TRABAJADAS"){
                                                    $color = "color:blue;background:#c1e2fc";
                                                }elseif($col['value']=="HORAS MANTENIMIENTO"){
                                                    $color = "color:red;background:#fda099";
                                                }elseif($col['value']=="FUERA DE OBRA"){
                                                    $color = "color:black;background:#fdff8f";
                                                }
                                            ?>
                                            <td class="text-bold text-center" style="{{ $color }}"
                                                @if (array_key_exists('sRow', $col)) rowspan="{{ $col['sRow'] }}" @endif 
                                                @if (array_key_exists('sCol', $col)) colspan="{{ $col['sCol'] }}" @endif
                                                >
                                                {{ $col['value'] }}
                                            </td>
                                        @endif
                                    @endif
                                @endfor
                            </tr>
                        @endif
                    @endfor
                </table>

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