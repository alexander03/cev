<?php

namespace App\Exports;

use App\AbastecimientoCombustible;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AbastecimientoCombustibleExport implements FromCollection, WithHeadings, WithEvents{

    /**
    * @return \Illuminate\Support\Collection
    */
    private $start_date, $end_date, $equipo_ids, $subtotal;

    public function __construct($start_date,$end_date) 
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }
    
    public function collection(){
        
        $queryVehiculo = AbastecimientoCombustible::select('abastecimiento_combustible.id as id', 'fecha_abastecimiento', 'g.descripcion as desc', 
            'tcom.descripcion as tipo_combustible', 
            'c.nombres as nom', 'c.apellidos as ap', 'c.dni as dni',
            //DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE "-" END) AS res'), 
            'ua.codigo as code', 'ua.descripcion as descua', 
            DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
            'mc.descripcion as descmc', 
            DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS eqmode'), 
            DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
            DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS crza'), 
            'qtdgl', 'qtdl', 'km', 'motivo', 'comprobante', 'numero_comprobante',
            'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin', 'abast.descripcion as abasDesc',
            DB::raw('ua2.codigo AS code2'))
            -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
            -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
            -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
            -> leftjoin('ua', 'ua.id', '=', 'abastecimiento_combustible.ua_id')
            -> Join('vehiculo as vh', 'vh.id', '=', 'vehiculo_id')
            -> leftjoin('ua as ua2', 'ua2.id', '=', 'vh.ua_id')
            -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
            -> leftJoin('contratista as ct', 'ct.id', '=', 'vh.contratista_id')
            -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
            -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
            -> where('abastecimiento_combustible.fecha_abastecimiento','>=',$this->start_date)
            -> where('abastecimiento_combustible.fecha_abastecimiento','<=',$this->end_date)
            -> whereNull('abastecimiento_combustible.deleted_at')
            -> orderBy('abastecimiento_combustible.fecha_abastecimiento','asc');
        //'abastecimiento_dia'
        $resultQuery = AbastecimientoCombustible::select('abastecimiento_combustible.id as id', 'fecha_abastecimiento', 'g.descripcion as desc', 
            'tcom.descripcion as tipo_combustible', 
            'c.nombres as nom', 'c.apellidos as ap', 'c.dni as dni',
            //DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE "-" END) AS res'), 
            'ua.codigo as code', 'ua.descripcion as descua',
            DB::raw('(CASE WHEN eq.descripcion IS NULL THEN "-" ELSE eq.descripcion END) AS desceq'),
            'mc.descripcion as descmc', 
            DB::raw('(CASE WHEN eq.modelo IS NULL THEN "-" ELSE eq.modelo END) AS eqmode'), 
            DB::raw('(CASE WHEN eq.placa IS NULL THEN "-" ELSE eq.placa END) AS eqpl'), 
            DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS crza'), 
            'qtdgl', 'qtdl', 'km',  'motivo', 'comprobante', 'numero_comprobante',
            'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin', 'abast.descripcion as abasDesc',
            DB::raw('ua2.codigo AS code2'))
            -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
            -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
            -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
            -> leftjoin('ua', 'ua.id', '=', 'ua_id')
            -> leftJoin('equipo as eq', 'eq.id', '=', 'equipo_id')
            -> leftjoin('ua as ua2', 'ua2.id', '=', 'eq.ua_id')
            -> leftJoin('marca as mc', 'mc.id', '=', 'eq.marca_id')
            -> leftJoin('contratista as ct', 'ct.id', '=', 'eq.contratista_id')
            -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
            -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
            -> whereNull('abastecimiento_combustible.deleted_at')
            -> where('abastecimiento_combustible.fecha_abastecimiento','>=',$this->start_date)
            -> whereNull('abastecimiento_combustible.vehiculo_id')
            -> where('abastecimiento_combustible.fecha_abastecimiento','<=',$this->end_date)
            -> orderBy('abastecimiento_combustible.fecha_abastecimiento','asc')
            -> unionAll($queryVehiculo)
            -> orderBy('fecha_abastecimiento','asc')
            -> get();

        $newResult = [];
        $arrId = [];
        for($i = 0; $i < count($resultQuery); $i++) { 

            if(isset($arrId[0])){
                $isRepeat = false;
                foreach($arrId as $id){
                    if($resultQuery[$i] -> id === $id) $isRepeat = true;
                }
                if(!$isRepeat) array_push($newResult, $resultQuery[$i]);
                array_push($arrId, $resultQuery[$i] -> id);
            }else{
                $arrId = [ $resultQuery[0] -> id ];
                $newResult = [ $resultQuery[0] ];
            }
        }

        for($i = 0; $i < count($newResult); $i++) unset($newResult[$i] -> id);
        $collectionResult = collect($newResult);

        return $collectionResult;
    }

    public function headings(): array{
        return [
            'Fecha de abastecimiento',
            'Grifo',
            'Tipo de combustible',
            'Nombres',
            'Apellidos',
            'DNI',
            //'¿Es responsable?',
            'UA Desc. Subcont.',
            'Descripcion UA',
            'Unidad',
            'Marca',
            'Modelo',
            'Placa/serie',
            'Empresa',
            'QTD(GL)',
            'QTD(L)',
            'KM',
            //'Abastecimiento por día',
            'Motivo',
            'Comprobante',
            'Número de comprobante',
            'Hora de inicio',
            'Hora de fin',
            'Lugar de abastecimiento',
            'UA Vehiculo/Equipo',
        ];
    }

    public function registerEvents(): array{
        
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z'];

                foreach($letters as $letter){
                    $event->sheet->getColumnDimension($letter)->setAutoSize(true);
                }

                $cellRange = 'A1:K1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');
            }
        ];
    }       
}
