<table>
    @for ($r = 0; $r <= $max_row; $r++)
        @if ( isset($data[$r]) )
            <?php $row = $data[$r] ?>
            <tr>
                @for ($c = 0; $c <= $max_col; $c++)
                    @if ( isset($row[$c]) )
                        <?php $col = $row[$c] ?>
                        @if ( $col == $free_value )
                            <td></td>
                        @elseif ( isset($col['value']) )
                            <td 
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