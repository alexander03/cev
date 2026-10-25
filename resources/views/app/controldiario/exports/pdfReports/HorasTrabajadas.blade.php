<?php 
use App\Concesionaria;
$concesionaria = Concesionaria::getConcesionariaActual();
?>
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
            border: 1px;
        }
        tr{
            border-collapse: collapse;
            border: 1px solid;
        }
        td{
            border-collapse: collapse;
            border: 1px solid;
            height: 19px;
        }
        span{
            font-weight: normal;
        }
        .table-dark{
            background-color: gray;
        }
        .table-bordered{
            border-collapse: collapse;
            border: 1px solid; 
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
        .text-light{
            color: white;
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
        .text-right{
            text-align: right;
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
                        <td width="100%" class="text-center table-borderless">
                            <h1>REPORTE DE HORAS TRABAJADAS DE EQUIPOS {{ $nombre }}</h1>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

        <tr class="table-borderless">
            <td class="table-borderless" style="height: 10px;"></td>
        </tr>
        <tr class="table-borderless">
            <td class="table-borderless">
                
                <table width="100%">
                    @for ($r = 0; $r <= $max_row; $r++)
                        @if ( isset($data[$r]) )
                            <?php $row = $data[$r];
                            if($r<2){
                                $max=$max_col-1;
                            }else{
                                $max=$max_col;
                            }
                            ?>
                            <tr>
                                @for ($c = 0; $c <= $max; $c++)
                                    @if ( isset($row[$c]) )
                                        <?php $col = $row[$c] ?>
                                        @if ( $col == $free_value && $r<2)
                                            <td></td>
                                        @elseif ( isset($col['value']) && (($r==0 && $c==0) || $c>0))
                                            <td @if (array_key_exists('class', $col)) class="{{ $col['class'] }} fsize" @endif 
                                                @if (array_key_exists('sRow', $col)) rowspan="{{ $col['sRow'] }}" @endif 
                                                @if (array_key_exists('sCol', $col)) colspan="{{ $col['sCol'] }}" @endif
                                                style="<?php if($c==3){ echo "width:100px !important;";}?>";
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

        
    </table>
</body>
</html>