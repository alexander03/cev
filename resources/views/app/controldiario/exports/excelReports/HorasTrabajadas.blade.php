<table>
    @for ($r = 0; $r <= $max_row; $r++)
        @if ( isset($data[$r]) )
            <?php $row = $data[$r] ?>
            <tr>
                @for ($c = 0; $c <= $max_col; $c++)
                    @if ( isset($row[$c]) )
                        <?php $col = $row[$c];
                            if($r<3){
                                $style2="font-weight: bold;";
                            }else{
                                $style2="";
                            }
                            if($c<2){
                                $style="width:15px;";
                            }elseif($c==2){
                                $style="width:35px;";
                            }elseif($c==3){
                                $style="width:40px;";
                            }else{
                                $style="";
                            }
                        ?>
                        @if ( $col == $free_value )
                            <td style="<?=$style.$style2?>"></td>
                        @elseif ( isset($col['value']) )
                            <td style="<?=$style.$style2?>" 
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