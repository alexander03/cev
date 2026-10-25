<?php

namespace App\Exports;


class ExcelHelper
{
    protected $data = [], $row = -1, $col = -1, $max_row = -1, $max_col = -1;
    protected $span_value = 'SPAN_CELL', $free_value = 'FREE_CELL';

    // METODOS DE LAS TABLAS RELATIVAS
    protected function nextRow(int $nRows = 1)
    {
        $this->row += $nRows;
        $this->col = -1;

        // actualizo la cantidad de filas maxima
        if ( $this->max_row < $this->row ) $this->max_row = $this->row;

        return $this;
    }
    protected function nextCol(int $nCols = 1)
    {
        $this->col += $nCols;

        // En caso que no este vacia, busca la siguiente
        while ( isset($this->data[$this->row][$this->col]) && $this->data[$this->row][$this->col] !== $this->free_value ) {
            $this->col++;
        }
        
        // actualizo la cantidad de columnas maxima
        if ( $this->max_col < $this->col ) $this->max_col = $this->col;

        return $this;
    }
    protected function nextCell(int $nRows = 1, int $nCols = 1)
    {
        $this->nextRow($nRows)->nextCol($nCols);

        return $this;
    }

    /**
     *  @method fixTable(). Esta funcion sirve para reparar un array $table y convertirlo en un table completa.
     *  @param array=>$table. Es el array que tiene los valores y tengo que evaluar para completarlo.
     *  @param int=>$nRows. Es la cantidad de filas que debe tener la tabla. Esta variable puede ser la longitud de la primera dimension de $table o lo puede definir el usuario.
     *  @param int=>$nCols. Es la cantidad de columnas que debe tener la tabla. Esta variable puede ser la mayor longitud de la segunda dimension de $table o lo puede definir el usuario.
     *  @return array. Retorna un array arreglado completado con $nRows filas y $nCols columnas.
     */
    private function fixTable(Array $table, int $nRows = 0, int $nCols = 0): ?Array
    {
        // En caso que el $table este vacio entonces regreso null
        if ( empty($table) ) throw new Exception('var $table is empty.');

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
     * @method merge(). Sirve para fusionar 2 tablas o 2 celdas, ya sea de manera horizontal o vertical
     * @param array=>$tableHead. Tabla que ira primero.
     * @param array=>$tableBody. Table que ira ultima.
     * @param string=>$orientation. Orientacion que tendra la union. Ejemplo: ('v': union vertical), ('h': union horizontal)
     */
    protected function mergeTables(array $tableHead = [], array $tableBody = [], string $orientation = 'v'): array
    {
        // Validamos que ninguno de los 2 este vacio
        if ( empty($tableHead) ) throw new Exception('$tableHead array is empty');
        if ( empty($tableBody) ) throw new Exception('$tableBody array is empty');

        // Reparo ambas tables para asegurarse que no existiran errores y tambien el $orientation
        $tableHead = $this->fixTable($tableHead);
        $tableBody = $this->fixTable($tableBody);
        
        // Verificamos la existencia de $orientation
        if ( $orientation == 'v' || $orientation == 'h') {
            $orientation = $orientation == 'v' ? 'v' : 'h';
        } else throw new Exception('Option from var $orientation don\'t found');

        // Fusionamos el $tableHead con el $tableBody de manera $orientation y los ponemos en $tableFinal
        $tableFinal = [];
        
        // SI: el tipo de orientacion es vertical entonces lo fusionamos verticalmente
        if ( $orientation == 'v' ) {
            // SI: la cantidad de columnas de $tableBody es mayor al de $tableHead entonces repararemos $tableHead
            if ( count($tableBody[0]) > count($tableHead[0]) ) {
                // Defino el $nCols como la cantidad de columnas del $tableBody
                $nCols = count($tableBody[0]);
                // Reparo el $tableHead
                $tableHead = $this->fixTable($tableHead, count($tableHead), $nCols);
            }
            // SINO: entonces repararemos $tableBody
            else {
                // Defino el $nCols como la cantidad de columnas del $tableHead
                $nCols = count($tableHead[0]);
                // Reparo el $tableBody
                $tableBody = $this->fixTable($tableBody, count($tableBody), $nCols);
            }
            
            // Fusionamos los datos del $tableHead con los del $tableBody verticalmente
            $tableFinal = array_merge($tableHead, $tableBody);
        }
        // SINO: entonces lo fusionamos horizontalmente
        else {
            // SI: la cantidad de filas de $tableBody es mayor al de $tableHead entonces repararemos $tableHead
            if ( count($tableBody) > count($tableHead) ) {
                // Defino el $nRows como la cantidad de filas del $tableBody
                $nRows = count($tableBody);
                // Reparo el $tableHead
                $tableHead = $this->fixTable($tableHead, $nRows);
            }
            // SINO: entonces repararemos $tableBody
            else {
                // Defino el $nRows como la cantidad de filas del $tableHead
                $nRows = count($tableHead);
                // Reparo el $tableBody
                $tableBody = $this->fixTable($tableBody, $nRows);
            }
            
            // Fusionamos los datos del $tableHead con los del $tableBody horizontalmente
            for ($i = 0; $i < $nRows; $i++) {
                $tableFinal[] = array_merge($tableHead[$i], $tableBody[$i]); // Fusionamos ambos indices de los arreglos
            } unset($i);
        }

        return $tableFinal;
    }

    /**
     * @method addTableHeaders(). Sirve para agregar los headers a una tabla.
     * @param array=>$tableHead. Es la tabla de donde estan todas las cabeceras y fusionaremos al $tableBody
     * @param array=>$tableBody. Es la tabla de datos a la cual le fusionaremos los headers.
     * @return array. Retorna la tabla fusionada con la cabecera
     */
    protected function addTableHeaders(array $tableHead = [], array $tableBody = []): array
    {
        // Fusionamos los 2 tables
        return $this->mergeTables($tableHead, $tableBody, 'v');
    }

    /**
     *  @method format(). Esta funcion sirve para dar un arreglo con datos especificos y me devuelva un array con los datos ordenados como para mostrar en el excel.
     *  @param string|array=>$value. Son los datos que quiero ordenar para mostrar. El arreglo puede ser uni o bidireccional. Ejemplo => 'title1', ['title1', 'title2'], [['title1', 'title2'], 'title3']
     *  @param array=>$offset. Es contiene al oX y al oY, estas variables significan la cantidad de columnas o filas a dejar en blanco. Son relativos porque dependen de $trasposed. Ejemplo => ['oX' => 1, 'oY' => 3]
     *  @param boolean=>$trasposed. Esta variable indica si los datos del arreglo iran normal o de manera traspuesta. Ejemplo => true: traspuesta activada, false: traspuesta desactivada
     *  @param array=>$subData. Este array sera usado en el metodo format nuevamente.
     *  @return array. El arreglo de retorno se puede incluir normal en en array principal o en otro array.
     */
    protected function formatTable($value, Array $attributes = []): ?Array
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
        // IF: se fabrico algun $arrayBody entonces lo fusiono con $arrayHead
        if ( !empty($arrayBody) ) $arrayFinal = $this->mergeTables($arrayHead, $arrayBody, 'h');
        // ELSE: entonces el $arrayFinal = $arrayHead
        else $arrayFinal = $arrayHead;
        
        // Retornamos el arreglo final
        return $arrayFinal;
    }



    protected function set($row, $col, $value)
    {
        // defino el valor en el lugar
        if ( $value === $this->span_value ) {
            $this->data[$row][$col] = $this->span_value;
        } else if ( $value === $this->free_value ) {
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
    protected function add($value = null, array $attributes = [])
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
    protected function addTable(array $table = [], array $attributes = [])
    {
        // Establesco la siguiente columna
        $this->nextCol();
        
        //Establesco los valores
        $nRow = $this->row;
        $nCol = $this->col;
        
        $this->setTable($table, $nRow, $nCol, $attributes);
    }

    protected function setTable(array $table = [], int $nRow = 0, int $nCol = 0)
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

    protected function addCell(string $value = '', array $attributes = [])
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
        
        $this->spanCell($nRow, $nCol, $sRow, $sCol, $use);
    }

    protected function spanCell(int $nRow = -1, int $nCol = -1, int $sRow = 1, int $sCol = 1, bool $use = true)
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

}