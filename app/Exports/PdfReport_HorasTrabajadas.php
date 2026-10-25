<?php
namespace App\Exports;
ini_set("pcre.backtrack_limit", "10000000");
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use DateTime;
use DB;

use App\Concesionaria;
use App\Equipo;
use App\Tipohora;
use App\Controldiario;

class PdfReport_HorasTrabajadas extends ExcelHelper
{
    private $start_date, $end_date, $contratista_id, $ua;

    public function __construct(DateTime $start_date, DateTime $end_date, $contratista_id, $ua)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->contratista_id = $contratista_id;
        $this->ua = $ua;
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
                if ( $i == 0 || $i == 1 ) $newRow[] = 'Total general';
                else $newRow[] = '';
            }
        } unset($i);

        // THIRD: Se agrega la nueva columna a la tabla
        $table[] = $newRow;

        // FOURTH
        return $table;
    }

    private function applyStyles()
    {
        $row = count($this->data) - 1;

        for ($i = 0; $i < count($this->data); $i++) { 
            for ($j = 0; $j < count($this->data[$i]); $j++) { 
                $class = '';
                if ( ($i < 3 || $i === $row ) && is_array($this->data[$i][$j]) ) {
                    $class .= ' table-dark text-light text-bold';
                    $this->data[$i][$j]['class'] = $class;
                } 
                if ( $i >= 3 && $j >= 4 && is_array($this->data[$i][$j]) ) {
                    $class .= ' text-right';
                    $this->data[$i][$j]['class'] = $class;
                }
            }
        }
    }

    private function getColspan()
    {
        $nro_dias = 0;

        $months = $this->getMonths();
        foreach ($months as $month) {
            $nro_dias += count($month['days']) + 1;
        }
        
        $nro_dias++;

        return $nro_dias;
    }

    public function getData()
    {
        // Row 1
        $nro_dias = $this->getColspan();

        $this->nextRow();
        $this->add('Suma de HORAS', ['rowspan' => 2, 'colspan' => 3, 'class' => 'table-black text-white']);
        $this->add('Etiquetas de columna', ['colspan' => ($nro_dias-2), 'class' => 'table-black text-white']);
        
        // Row 2
        $this->nextRow();
        $months = $this->getMonths();
        foreach ($months as $month) {
            $this->add($month['name'], ['colspan' => count($month['days'])]);
            //$this->add('Total ' . $month['name'], ['rowspan' => 2, 'use' => false]);
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
            //foreach ($month['days'] as $day) $this->add($day . '/' . $month['val']);
            $this->add();
        }
        
        // Row 4
        $this->nextRow();
        $concesionaria = Concesionaria::getConcesionariaActual();
        $table = [
            'value' => $concesionaria['razonsocial'],
            'subData' => $this->getEquipos($concesionaria['id'])
        ];
        // dd($table);
        $table = $this->formatTable($table['value'], ['subData' => $table['subData']]);
        $table = $this->applyFunction($table, 0, 4);

        $this->add($table); 
        
        $this->applyStyles();

        return [
            'data' => $this->data,
            'max_row' => $this->max_row,
            'max_col' => $this->max_col,
            'span_value' => $this->span_value,
            'free_value' => $this->free_value
        ];
    }

    private function getMonths()
    {
        setlocale(LC_TIME, 'es_ES');

        $months = [];
        $month = new DateTime($this->start_date->format('Y-m-d'));

        while ( $month <= $this->end_date ) {
            $new_month['name'] = substr(strtolower(strftime('%b', $month->getTimestamp())), 0, 3);
            $new_month['val'] = $month->format('m');
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
                //$value[] = number_format($total_mes, 2);
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
}
