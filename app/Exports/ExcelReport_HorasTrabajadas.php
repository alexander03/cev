<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use DateTime;
use DB;

use App\Concesionaria;
use App\Equipo;
use App\Tipohora;
use App\Contratista;
use App\Controldiario;

class ExcelReport_HorasTrabajadas implements FromView
{
    use Exportable;


    private $data = [], $row = -1, $col = -1, $max_row = -1, $max_col = -1;
    private $span_value = 'span_cell', $free_value = 'free_cell';
    private $start_date, $end_date, $contratista_id, $ua;
    private $level = 0;

    public function __construct($data)
    {
        $this->start_date = new DateTime($data['start_date']);
        $this->end_date = new DateTime($data['end_date']);
        $this->contratista_id = $data['contratista_id'];
        $this->ua = $data['ua_padre'];
    }

    private function nextRow($letEmpty = 1)
    {
        $this->row += $letEmpty;
        $this->col = -1;

        // defino la fila mas grande
        if ( $this->max_row < $this->row ) $this->max_row = $this->row;
    }
    private function nextCol($letEmpty = 0)
    {
        $this->col += $letEmpty;

        do {    // En caso que no este vacia, busca la siguiente
            $this->col++;
        } while ( isset($this->data[$this->row][$this->col]) && $this->data[$this->row][$this->col] != $this->free_value );

        // defino la columna mas grande
        if ( $this->max_col < $this->col ) $this->max_col = $this->col;
    }

    /**
     *  @method fixTable(). Esta funcion sirve para reparar un array $table y convertirlo en un table completa.
     *  @param string=>$table. Es el array que tiene los valores y tengo que evaluar para completarlo.
     *  @param int=>$nRows. Es la cantidad de filas que debe tener la tabla. Esta variable puede ser la longitud de la primera dimension de $table o lo puede definir el usuario.
     *  @param string=>$nCols. Es la cantidad de columnas que debe tener la tabla. Esta variable puede ser la mayor longitud de la segunda dimension de $table o lo puede definir el usuario.
     *  @return array. Retorna un array arreglado completado con $nRows filas y $nCols columnas.
     */
    private function fixTable(Array $table, int $nRows = 0, int $nCols = 0): ?Array
    {
        // En caso que el $table este vacio entonces regreso null
        if ( empty($table) ) return null;

        // Defino con mas exactitud las longitudes de las dimensiones del $table
        $nRows = count($table) > $nRows ? count($table) : $nRows; // Defino la maxima cantidad de filas
        foreach ($table as $row) if ( is_array($row) && count($row) > $nCols ) $nCols = count($row); // Defino la maxima cantidad de columnas

        // PRIMERA DIMENSION
        for ($i = 0; $i < $nRows; $i++) {
            // SI: el index $i no esta definido o es null entonces lo defino como array vacio para luego rellenarlo
            if ( !isset($table[$i]) || is_null($table[$i]) ) $table[$i] = [];
            // SI: en caso que sea un array
            else if ( is_array($table[$i]) ) {

                // SEGUNDA DIMENSION
                for ($j = 0; $j < count($table[$i]); $j++) {
                    // SI: el index $j es un array o es null entonces se elimina defrente
                    if ( is_array($table[$i][$j]) || is_null($table[$i][$j]) ) unset($table[$i][$j]);
                } unset($j);
                // En caso que se haya limpiado tengo que asegurarme que los indices mantienen el orden
                $table[$i] = array_merge($table[$i]);

            }
            // SINO: entonces defino el index $i como array para luego rellenarlo
            else $table[$i] = [$table[$i]];
            
            // Relleno las columnas que sean necesarias
            for ($j = count($table[$i]); $j < $nCols; $j++) $table[$i][$j] = "";
        } unset($i);

        // retorno el array arreglado en caso que tenga valores
        return $table;
    }



    /**
     *  @method format(). Esta funcion sirve para dar un arreglo con datos especificos y me devuelva un array con los datos ordenados como para mostrar en el excel.
     *  @param string|array=>$value. Son los datos que quiero ordenar para mostrar. El arreglo puede ser uni o bidireccional. Ejemplo => 'title1', ['title1', 'title2'], [['title1', 'title2'], 'title3']
     *  @param array=>$offset. Es contiene al oX y al oY, estas variables significan la cantidad de columnas o filas a dejar en blanco. Son relativos porque dependen de $trasposed. Ejemplo => ['oX' => 1, 'oY' => 3]
     *  @param boolean=>$trasposed. Esta variable indica si los datos del arreglo iran normal o de manera traspuesta. Ejemplo => true: traspuesta activada, false: traspuesta desactivada
     *  @param array=>$subData. Este array sera usado en el metodo format nuevamente.
     *  @return array. El arreglo de retorno se puede incluir normal en en array principal o en otro array.
     */
    private function formatTable($value, Array $attributes = []): ?Array
    {
        // 1) Defino los atributos para esta tabla
        // Defino los offsets
        $oRows = array_key_exists('oRows', $attributes) ? $attributes['oRows'] : 1; // Se define la longitud del offset de las filas, por defecto 1
        $oCols = array_key_exists('oCols', $attributes) ? $attributes['oCols'] : 1; // Se define la longitud del offset de las columnas, por defecto 1
        $trasposed = array_key_exists('trasposed', $attributes) ? $attributes['trasposed'] : false; // Se define si el array sera traspuesta o no, por defecto no
        $subData = array_key_exists('subData', $attributes) ? $attributes['subData'] : []; // Se define si existen subdatos que iran anexados a esta table, por defecto no

        // 2) Reparo y completo $value
        // SI: $value no es array entonces lo defino
        if ( !is_array($value) ) $value = [$value];
        // Limpiamos y arreglamos el array $value
        $value = $this->fixTable($value, $oRows, $oCols); // Limpio el array de imperfecciones
        if ( is_null($value) || empty($value) ) return []; // En caso que el array este vacio lo regreso de una vez
        $nRows = count($value); $nCols = count($value[0]); // Defino la cantidad real de filas y columnas respectivamente
        
        // 3) Fabricamos el $arrayHead
        // Fabricamos el arrayHead que contendra todos los datos ordenados del value
        $arrayHead = [];
        // SI: la $trasposed es verdadera entonces se hace la traspuesta del arreglo
        if ( $trasposed ) {
            // Iniciamos el proceso de la traspuesta de $value
            for ($i = 0; $i < $nCols; $i++) { 
                // Defino el index $i del $arrayHead en vacio
                $arrayHead[$i] = [];
                // Paso casa index $j de cada columna de $value a la columna $i de $arrayHead
                for ($j = 0; $j < $nRows; $j++) {
                    $arrayHead[$i][] = $value[$j][$i]; // Aqui sigo con algo de duda, espero entenderlo luego, pero es necesario cambiar el orden de $i $j
                } unset($j);
            } unset($i);
        }
        // SINO: solo tendra los valores que ya se encuentran en $value
        else $arrayHead = $value;
        
        // 4) Fabricamos el $arrayBody
        // Fabricamos el $arrayBody que contendra todos los subdatos del $arrayHead
        $arrayBody = [];
        // SI: el $subData es diferente de null y tiene almenos 1 dato
        if ( !is_null($subData) && !empty($subData) ) {
            for ($i = 0; $i < count($subData); $i++) { 
                // Ubico el index $i de $subdata en $newTable
                $newTable = $subData[$i];
                // Verifico que los datos existen
                if ( !isset($newTable['value']) ) $newTable['value'] = [];
                if ( !isset($newTable['offset']) ) $newTable['offset'] = [];
                if ( !isset($newTable['trasposed']) ) $newTable['trasposed'] = false;
                if ( !isset($newTable['subData']) ) $newTable['subData'] = [];
                // Formateo la nueva tabla
                $newTable = $this->formatTable($newTable['value'], [
                    'offset' => $newTable['offset'], 
                    'trasposed' => $newTable['trasposed'], 
                    'subData' => $newTable['subData']
                ]);
                // Fusiono esta tabla con el $arrayBody
                $arrayBody = array_merge($arrayBody, $newTable);
            } unset($i);
        }
        
        // 5) Fusionamos el $arrayHead con el $arrayBody
        // SI: se fabrico algun arrayBody 
        if ( !empty($arrayBody) ) {
            // Fusionamos el $arrayHead con el $arrayBody de manera horizontal y los ponemos en $arrayFinal
            $arrayFinal = [];
            // SI: la longitud de $arrayBody es mayor al de $arrayHead
            if ( count($arrayBody) > count($arrayHead) ) {
                // Defino el $nRows con el numero de filas del $arrayBody
                $nRows = count($arrayBody);
                // Reparo y completo el array $arrayHead
                $arrayHead = $this->fixTable($arrayHead, $nRows);
            }
            // SINO: entonces la longitud mas grande sera la de $arrayHead
            else {
                // Defino el $nRows con el numero de filas del $arrayHead
                $nRows = count($arrayHead);
                // Reparo y completo el array $arrayBody
                $arrayBody = $this->fixTable($arrayBody, $nRows);
            }
            
            // Fusionamos los datos del $arrayHead con los del $arrayBody
            for ($i = 0; $i < $nRows; $i++) {
                $arrayFinal[] = array_merge($arrayHead[$i], $arrayBody[$i]); // Fusionamos ambos indices de los arreglos
            } unset($i);
        } 
        // SINO: entonces el $arrayFinal = $arrayHead
        else $arrayFinal = $arrayHead;
        
        // Retornamos el arreglo final
        return $arrayFinal;
    }



    private function set($row, $col, $value)
    {
        // defino el valor en el lugar
        if ( $value == $this->span_value ) {
            $this->data[$row][$col] = $this->span_value;
        } else if ( $value == $this->free_value ) {
            if ( !isset($this->data[$row][$col]) ) $this->data[$row][$col] = $this->free_value;
        } else {
            $this->data[$row][$col] = [];
            $this->data[$row][$col]['value'] = $value;
        }

        // hago existir los espacios libres
        if ( $value != $this->free_value ) {
            for ($r = 0; $r <= $row; $r++) { 
                for ($c = 0; $c <= $col; $c++) { 
                    $this->set($r, $c, $this->free_value);
                }
            }
        }

        // defino la fila y columna mas grande
        if ( $this->max_row < $row ) $this->max_row = $row;
        if ( $this->max_col < $col ) $this->max_col = $col;
    }

   

    /**
     *  @method add(). Sirve para agregar un valor o un array a la tabla principal.
     *  @param string|array=>$value. Si es un array, entonces se redigira a addTable() function, caso contrario se ira a addCell() function.
     *  @param array=>$attributes. Es un array que tendra todos los atributos o condiciones para agregar esta celda.
     */
    private function add($value = null, array $attributes = [])
    {
        // VALIDACIONES: si no han dado ningun valor entonces no se realiza nada
        if ( is_null($value) ) return;

        // SI: $value es cualquier otro valor menos array, entonces se agregara como celda
        if ( !is_array($value) ) $this->addCell(strval($value), $attributes);
        // SI: $value es array entonces se agregara como tabla
        else $this->addTable($value, $attributes);
    }

    /**
     *  @method addTable(). Esta funcion sirve para añadir una tabla secuencial de datos en un punto en especifico
     *  @param array=>$table. Este array contendra todos los datos para ser formateada;
     */
    private function addTable(array $table = [], array $attributes = [])
    {
        // Establesco la siguiente columna
        $this->nextCol();
        
        //Establesco los valores
        $nRow = $this->row;
        $nCol = $this->col;
        
        $this->setTable($table, $nRow, $nCol, $attributes);
    }

    private function setTable(array $table = [], int $nRow = 0, int $nCol = 0)
    {
        $table = $this->fixTable($table);

        // inicio con el posicionamiento de los datos
        for ($rRow = 0; $rRow < count($table); $rRow++) { 
            // SI: la fila es null entonces la defino como una
            if (is_null($table[$rRow])) $table[$rRow] = [];

            // SI: no es un array entonces lo defino como array
            else if ( !is_array($table[$rRow]) ) $table[$rRow] = [$table[$rRow]];

            for ($rCol = 0; $rCol < count($table[$rRow]); $rCol++) { 
                if ( !is_null($table[$rRow][$rCol]) && !is_array($table[$rRow][$rCol]) ) {
                    $this->set($nRow + $rRow, $nCol + $rCol, $table[$rRow][$rCol]);
                }
            } unset($rCol);
        } unset($rRow);
    }

    private function addCell(string $value = '', array $attributes = [])
    {
        // Siguiente columna
        $this->nextCol();

        $nRow = $this->row; // Se define el numero de fila como el numero de fila general
        $nCol = $this->col; // Se define el numero de columna como el numero de columna general

        // Agregar value
        $this->set($nRow, $nCol, $value);
        
        // Si no existen atributos en $attributes entonces no se realiza lo demas
        if ( empty($attributes) ) return; 
        
        // defino las variables a usar
        $sRow = array_key_exists('rowspan', $attributes) ? $attributes['rowspan'] : 1;
        $sCol = array_key_exists('colspan', $attributes) ? $attributes['colspan'] : 1;
        $use = array_key_exists('use', $attributes) ?  $attributes['use'] : true;
        // dd($sRow, $sCol, $use);
        $this->spanCell($nRow, $nCol, $sRow, $sCol, $use);
    }

    private function spanCell(int $nRow = -1, int $nCol = -1, int $sRow = 1, int $sCol = 1, bool $use = true)
    {
        // VALIDACION: SI: el $sRow y el $sCol son 1 entonces no se trabaja nada
        if ( ($sRow == 1 && $sCol == 1) || $nRow < 0 || $nCol < 0 ) return;

        // Si: se usa el span entonces en el blade se definiran los span, en el array los espacios seran llenados con 'span'
        if ( $use ) { // en caso que exista el use en span y sea true entonces se agrega el span
            if ( $sRow > 1 ) $this->data[$nRow][$nCol]['sRow'] = $sRow;
            if ( $sCol > 1 ) $this->data[$nRow][$nCol]['sCol'] = $sCol;
            
            for ($r = $nRow; $r < $nRow + $sRow; $r++) { 
                for ($c = $nCol; $c < $nCol + $sCol; $c++) { 
                    if ( $r != $nRow || $c != $nCol ) $this->set($r, $c, $this->span_value);
                }
            }
        }
        
        // SINO: no usamos el span entonces en el blade NO se definiran los span y en el array los espacios seran llenados con celdas vacias ['value' => '']
        else {
            for ($r = $nRow; $r < $nRow + $sRow; $r++) { 
                for ($c = $nCol; $c < $nCol + $sCol; $c++) { 
                    if ( $r != $nRow || $c != $nCol ) $this->set($r, $c, '');
                }
            }
        }
    }

    private function applyFunction(array $table, int $sRow, int $sCol): array
    {
        // FIRST: defino las nuevas longitudes del arreglo
        $nRows = count($table); // La cantidad de filas de la tabla
        $nCols = count($table[0]); // La cantidad de columnas de la tabla

        // SECOND: se crea la nueva fila, mientras se busca la posicion inicial
        $newRow = [];
        for ($i = 0; $i < $nCols; $i++) { 
            // IF: esta columna esta dentro del cuadro de la formula entonces se calcula el total
            if ( $i >= $sCol ) {
                $total = 0;
                for ($j = 0; $j < $nRows; $j++) {
                    // IF: esta fila esta dentro del cuadro de la formula entonces se calcula el total
                    if ( $j >= $sRow ) {
                        if ( $table[$j][$i] != '' ) $total += floatval($table[$j][$i]);
                    }
                } unset($j);
                $newRow[] = number_format($total, 2);
                unset($total);
            }
            // ELSE: se encuentra dentro del cuadro de la formula entonces solo se crea un espacio vacio
            else {
                if ( $i == 0 ) $newRow[] = 'Total general';
                else $newRow[] = '';
            }
        } unset($i);

        // THIRD: Se agrega la nueva columna a la tabla
        $table[] = $newRow;

        // FOURTH
        return $table;
    }

    private function loadData()
    {
        // Row 1
        $this->nextRow();
        $this->add('Suma de HORAS', ['rowspan' => 2, 'colspan' => 4, 'use' => true]);
        $this->add('Etiquetas de columna');
        
        // Row 2
        $this->nextRow();
        $months = $this->getMonths();
        foreach ($months as $month) {
            $this->add($month['name'], ['colspan' => count($month['days'])]);
            $this->add('Total ' . $month['name'], ['rowspan' => 2, 'use' => false]);
        }
        $this->add('Total general');

        // Row 3
        $this->nextRow();
        $this->add('Etiquetas de fila');
        $this->add('CODIGO');
        $this->add('DESC. EQUIPO');
        $this->add('Tipo Hora');

        foreach ($months as $month) {
            foreach ($month['days'] as $day) $this->add($day . '/' . $month['name']);
            $this->add();
        }
        
        // Row 4
        $this->nextRow();
        if($this->contratista_id==""){
            $nombre = "TODOS";
        }else{
            $nombre = Contratista::find($this->contratista_id)->razonsocial;
        }
        $concesionaria = Concesionaria::getConcesionariaActual();
        $table = [
            'value' => $nombre,//$concesionaria['razonsocial'],
            'subData' => $this->getEquipos($concesionaria['id'])
        ];
        // dd($table);
        $table = $this->formatTable($table['value'], ['subData' => $table['subData']]);
        $table = $this->applyFunction($table, 0, 4);

        $this->add($table); 
    }

    private function getMonths()
    {
        setlocale(LC_TIME, 'es_ES');

        $months = [];
        $month = new DateTime($this->start_date->format('Y-m-d'));

        while ( $month <= $this->end_date ) {
            $new_month['name'] = substr(strtolower(strftime('%b', $month->getTimestamp())), 0, 3);
            
            $days = [];
            $day = new DateTime($month->format('Y-m-d'));
            while ( $day->format('m') == $month->format('m') && $day <= $this->end_date ) {
                $days[] = $day->format('d');
                $day->modify('+1 day');
            }
            
            $new_month['days'] = $days;
            $month = new DateTime($day->format('Y-m-d'));
            // $date = new DateTime(date('Y-m-d', strtotime($date->format('Y-m-d') . ' + 1 months')));

            $months[] = $new_month;
        }

        return $months;
    }
    
    private function getEquipos($concesionaria_id): array
    {
        $start_date = $this->start_date->format('Y-m-d');
        $end_date = $this->end_date->format('Y-m-d');
        $contratista_id = $this->contratista_id;
        $ua = trim($this->ua);

        $dbEquipos = Equipo::join('ua', 'equipo.ua_id', '=', 'ua.id')
                        ->join('controldiario', 'equipo.id', '=', 'controldiario.equipo_id')
                        ->select('equipo.id AS id', 'ua.codigo AS codigo', 'equipo.descripcion AS descripcion')->distinct()
                        ->where('controldiario.fecha', '>=', $this->start_date->format('Y-m-d'))
                        ->where(function($sql) use($contratista_id){
                            if($contratista_id!=""){
                                $sql->where('equipo.contratista_id','=',$contratista_id);
                            }
                        })
                        ->where(function($sql) use($ua){
                            if($ua!=""){
                                $sql->where('ua.codigo','like',$ua);
                            }
                        })
                        ->where('controldiario.fecha', '<=', $this->end_date->format('Y-m-d'))->get();
        
        $array = [];

        for ($i = 0; $i < count($dbEquipos); $i++) { 
            $id = $dbEquipos[$i]['id'];
            $codigo = $dbEquipos[$i]['codigo'];
            $descripcion = $dbEquipos[$i]['descripcion'];

            $array[] = [
                'value' => [$codigo, $descripcion],
                'trasposed' => true,
                'subData' => $this->getTipoHora($id)
            ];
        } unset($i);

        return $array;
    }

    private function getTipoHora($equipo_id): array
    {
        $start_date = $this->start_date->format('Y-m-d');
        $end_date = $this->end_date->format('Y-m-d');

        $dbTipoHora = TipoHora::rightJoin('controldiario', 'tipohora.id', '=', 'controldiario.tipohora_id')
                            ->select('tipohora.id AS id', 'tipohora.descripcion AS descripcion')->distinct()
                            ->where('controldiario.equipo_id', '=', $equipo_id)
                            ->where('controldiario.fecha', '>=', $this->start_date->format('Y-m-d'))
                            ->where('controldiario.fecha', '<=', $this->end_date->format('Y-m-d'))->get();
        
        $tiposHora = [];
        foreach ($dbTipoHora as $th) $tiposHora[] = ['descripcion' => $th['descripcion'], 'id' => $th['id']];

        // Preparo los grupos para la reparacion de la consulta
        $groups = [];

        // Grupo de "HORAS PARADAS"
        $group = [];
        $query = TipoHora::select('id')->where('tipo', 'like', '%Horas Paradas%')->get();
        for ($i = 0; $i < count($query); $i++) { 
            $group = array_merge($group, [$query[$i]['id']]);
        }
        $groups[] = ['descripcion' => 'HORAS PARADAS', 'ids' => $group, 'found' => false];

        // Grupo de "HORAS MANTENIMIENTO"
        $group = [];
        $query = TipoHora::select('id')->where('tipo', 'like', '%Horas de Mantenimiento%')->get();
        for ($i = 0; $i < count($query); $i++) { 
            $group = array_merge($group, [$query[$i]['id']]);
        }
        $groups[] = ['descripcion' => 'HORAS MANTENIMIENTO', 'ids' => $group, 'found' => false];
        
        // Grupo de "FUERA DE OBRA"
        $group = [];
        $query = TipoHora::select('id')->where('tipo', 'like', '%Fuera de Obra%')->get();
        for ($i = 0; $i < count($query); $i++) { 
            $group = array_merge($group, [$query[$i]['id']]);
        }
        $groups[] = ['descripcion' => 'FUERA DE OBRA', 'ids' => $group, 'found' => false];
        
        // Grupo de "REFRIGERIO"
        $group = [];
        $query = TipoHora::select('id')->where('tipo', 'like', '%Refrigerio%')->get();
        for ($i = 0; $i < count($query); $i++) { 
            $group = array_merge($group, [$query[$i]['id']]);
        }
        $groups[] = ['descripcion' => 'REFRIGERIO', 'ids' => $group, 'found' => false];
        
        // Grupo de "HORAS TRABAJADAS"
        $groups[] = ['descripcion' => 'HORAS TRABAJADAS', 'ids' => [NULL], 'found' => false];
        
        // Elimino todos los registros que tengan algun id de alguno de los grupos
        for ($i = 0; $i < count($groups); $i++) { // Paso por todos los grupos existentes
            // Elimino todos los registros que tengan algun id de este grupo
            foreach ($groups[$i]['ids'] as $id) { // Paso por cada id del grupo existente
                // Elimino el registro que tenga el id de este grupo
                for ($j = 0; $j < count($tiposHora); $j++) {  // Paso por cada registro del query principal
                    if ( $tiposHora[$j]['id'] == $id ) {
                        $groups[$i]['found'] = true;
                        unset($tiposHora[$j]);
                    }
                }
                // Para reacomodar los indices
                $tiposHora = array_merge($tiposHora);
            }
        }
        // Reparo el formato de los ids del query principal
        for ($i = 0; $i < count($tiposHora); $i++) {  // Paso por cada registro del query principal
            $tiposHora[$i]['ids'] = [$tiposHora[$i]['id']];
            unset($tiposHora[$i]['id']);
        }
        // Agregar los nuevos grupos al query principal
        foreach ($groups as $group) {
            if ( $group['found'] ) {
                $tiposHora[] = [
                    'descripcion' => $group['descripcion'],
                    'ids' => $group['ids']
                ];
            }
        }

        $array = [];
        
        for ($i = 0; $i < count($tiposHora); $i++) { 
            $tipoHora_ids = $tiposHora[$i]['ids'];
            $tipoHora_descripcion = mb_strtoupper($tiposHora[$i]['descripcion'], 'utf-8');

            $array[] = [
                'value' => $tipoHora_descripcion,
                'subData' => $this->getControlDiario($equipo_id, $tipoHora_ids)
            ];
        } unset($i);

        return $array;
    }

    private function getControlDiario($equipo_id, $ids): array
    {
        $start_date = $this->start_date->format('Y-m-d');
        $end_date = $this->end_date->format('Y-m-d');

        if($ids[0]==""){
            $dbControl = Controldiario::select('fecha', DB::raw('SUM(hora_total) AS hora_total'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where(function ($query) use ($start_date, $end_date) {
                                    $query->where('controldiario.fecha', '>=', $start_date)
                                        ->where('controldiario.fecha', '<=', $end_date);
                                })
                                ->groupBy('fecha')
                                ->orderBy('fecha', 'ASC')->get();
        }else{
            $dbControl = Controldiario::select('fecha', DB::raw('SUM(hora_parada) AS hora_total'))
                                ->where('equipo_id', '=', $equipo_id)
                                ->where(function ($query) use ($ids) {
                                    foreach ($ids as $id) $query->orWhere('tipohora_id', '=', $id);
                                })
                                ->where(function ($query) use ($start_date, $end_date) {
                                    $query->where('controldiario.fecha', '>=', $start_date)
                                        ->where('controldiario.fecha', '<=', $end_date);
                                })
                                ->groupBy('fecha')
                                ->orderBy('fecha', 'ASC')->get();
        }
        
        $value = [];
        $total_general = 0; $total_mes = 0; $mes = null;
        $date = new DateTime($start_date);
        
        $this->level++;

        while ( $date <= $this->end_date ) {
            if ( is_null($mes) ) $mes = intval($date->format('m'));
            
            $control = null;

            for ($i = 0; $i < count($dbControl); $i++) { 
                if ( $dbControl[$i]['fecha'] == $date->format('Y-m-d') ) {
                    $control = floatval($dbControl[$i]['hora_total']); break;
                }
            }

            $value[] = is_null($control) ? '' : number_format($control, 2);

            // SI: se esta trabajando con el mismo mes
            if ( $mes == intval($date->format('m')) ) $total_mes += is_null($control) ? 0 : $control;
            // SINO: entonces trabajamos con el nuevo mes
            else {
                $mes = intval($date->format('m'));
                $total_mes = is_null($control) ? 0 : $control;
            }
            
            // SI el mes que viene es el final o final de rango
            $tomorrow = new DateTime($date->format('Y-m-d'));
            $tomorrow->modify('+1 day');
            if ( $date == $this->end_date || $mes != intval($tomorrow->format('m')) ) {
                $value[] = number_format($total_mes, 2);
                $total_general += $total_mes;
            }

            $date->modify('+1 day');
        }
        
        $value[] = number_format($total_general, 2);

        return [
            [
                'value' => $value,
                'trasposed' => true
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view():View
    {
        $this->loadData();
        
        if($this->contratista_id==""){
            $nombre = "TODOS";
        }else{
            $nombre = Contratista::find($this->contratista_id)->razonsocial;
        }

        return view('app.controldiario.exports.excelReports.HorasTrabajadas', [
            'data' => $this->data,
            'max_row' => $this->max_row,
            'max_col' => $this->max_col,
            'span_value' => $this->span_value,
            'free_value' => $this->free_value,
            'nombre' => $nombre
        ]);
    }
    
    public function registerEvents(): array{
        
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z','AA','AB', 'AC','AD','AE'];

                foreach($letters as $letter){
                    $event->sheet->getColumnDimension($letter)->setAutoSize(true);
                }
                $cellRange = 'A1:AE1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
            }
        ];
    }
}
