<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\AbastecimientoCombustible;
use App\Grifo;
use App\Tipocombustible;
use App\Horometro;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteDiariodeCombustible  implements FromCollection, WithHeadings, WithEvents
{	

	private $fecha;
	private $idgrifo;
	private $concesionaria;
    private $tipocombustible_id;

	public function __construct($fecha,$grifo,$concesionaria,$tipocombustible_id){
		$this->fecha = $fecha;
		$this->idgrifo = $grifo;
		$this->concesionaria = $concesionaria;
        $this->tipocombustible_id = $tipocombustible_id;
	}


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$fechaAbast = $this->fecha;
		$idGrifo = $this->idgrifo;
        $tipocombustible_id = $this->tipocombustible_id;
		
		$queryVehiculo = AbastecimientoCombustible::select(
			'abastecimiento_combustible.id as id',
			DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'), 
            'c.dni as dni',
            'ua.codigo as code',
            DB::raw('(CASE WHEN vh.modelo IS NULL THEN "-" ELSE vh.modelo END) AS desceq'), 
            'mc.descripcion as descmc', 
			DB::raw('(CASE WHEN vh.placa IS NULL THEN "-" ELSE vh.placa END) AS eqpl'),
			'km', 'qtdgl', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
            DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS crza') )
            -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
            -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
            -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
            -> leftjoin('ua', 'ua.id', '=', 'ua_id')
            -> Join('vehiculo as vh', 'vh.id', '=', 'vehiculo_id')
            -> leftJoin('marca as mc', 'mc.id', '=', 'vh.marca_id')
            -> leftJoin('contratista as ct', 'ct.id', '=', 'vh.contratista_id')
            -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
            -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
			-> whereNull('abastecimiento_combustible.deleted_at')
			//-> whereIn('ua.concesionaria_id',$this->concesionaria)
            -> whereIn('abastecimiento_combustible.concesionaria_id',$this->concesionaria)
			-> where([
				[ 'abastecimiento_combustible.fecha_abastecimiento', '=', $fechaAbast ],
				[ 'abastecimiento_combustible.grifo_id', '=', $idGrifo ]
			])->where(function($sql) use($tipocombustible_id){
                if(!is_null($tipocombustible_id) && $tipocombustible_id!=""){
                    $sql->where('abastecimiento_combustible.tipocombustible_id','=',$tipocombustible_id);
                }
            });

        $resultQuery = AbastecimientoCombustible::select(
			'abastecimiento_combustible.id as id',
			DB::raw('(CASE WHEN c.user_id IS NULL THEN us.name ELSE CONCAT(c.apellidos, " ", c.nombres) END) AS res'),
            'c.dni as dni',
            'ua.codigo as code',
            DB::raw('(CASE WHEN eq.descripcion IS NULL THEN "-" ELSE eq.descripcion END) AS desceq'),
            'mc.descripcion as descmc', 
			DB::raw('(CASE WHEN eq.placa IS NULL THEN "-" ELSE eq.placa END) AS eqpl'), 
			'km', 'qtdgl', 'abastecimiento_combustible.hora_inicio', 'abastecimiento_combustible.hora_fin',
            DB::raw('(CASE WHEN ct.razonsocial IS NULL THEN "-" ELSE ct.razonsocial END) AS crza') )
            -> leftJoin('grifo as g', 'g.id', '=', 'grifo_id')
            -> leftJoin('users as us', 'us.id', '=', 'abastecimiento_combustible.user_id')
            -> leftJoin('conductor as c', 'c.user_id', '=', 'abastecimiento_combustible.user_id')
            -> leftjoin('ua', 'ua.id', '=', 'ua_id')
            -> leftJoin('equipo as eq', 'eq.id', '=', 'equipo_id')
            -> leftJoin('marca as mc', 'mc.id', '=', 'eq.marca_id')
            -> leftJoin('contratista as ct', 'ct.id', '=', 'eq.contratista_id')
            -> leftJoin('tipocombustible as tcom', 'tcom.id', '=', 'abastecimiento_combustible.tipocombustible_id')
            -> leftJoin('abastecimiento as abast', 'abast.id', '=', 'abastecimiento_combustible.abastecimiento_id')
            -> whereNull('abastecimiento_combustible.deleted_at')
            -> whereNull('abastecimiento_combustible.vehiculo_id')
            //-> whereIn('ua.concesionaria_id',$this->concesionaria)
            -> whereIn('abastecimiento_combustible.concesionaria_id',$this->concesionaria)
            -> where(function($sql) use($tipocombustible_id){
                if(!is_null($tipocombustible_id) && $tipocombustible_id!=""){
                    $sql->where('abastecimiento_combustible.tipocombustible_id','=',$tipocombustible_id);
                }
            })
			-> unionAll($queryVehiculo)
			-> where([
				[ 'abastecimiento_combustible.fecha_abastecimiento', '=', $fechaAbast ],
				[ 'abastecimiento_combustible.grifo_id', '=', $idGrifo ]
			])
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
        $grifo = Grifo::find($this->idgrifo);
        if(!is_null($this->tipocombustible_id) && $this->tipocombustible_id!=""){
            $tipocombustible = Tipocombustible::find($this->tipocombustible_id)->descripcion;
        }else{
            $tipocombustible = "TODOS";
        }
        $horometro = Horometro::where('grifo_id','=',$this->idgrifo)->where('fecha','=',$this->fecha)->first();
        if(!is_null($horometro)){
            $inicio = $horometro->inicio;
            $fin = $horometro->fin;
        }else{
            $inicio = '-';
            $fin = '-';
        }
        $head  = [
            [
                'CONTROL DIARIO DE COMBUSTIBLE',
            ],
            [   'FECHA',
                $this->fecha,
                '',
                'GRIFO',
                $grifo->descripcion
            ],
            [
                'TIPO COMBUSTIBLE',
                $tipocombustible,
                '',
                'HOROMETRO INICIAL',
                $inicio
            ],
            [
                '',
                '',
                '',
                'HOROMETRO FINAL',
                $fin
            ],
            [
                ''
            ],
            [
                'NOMBRES Y APELLIDOS',
                'DNI',
                'UA',
    			'VEHICULO',
    			'MARCA',
                'PLACA',
                'HOROMETRO',
                'GALONES',
                'H - INICIO',
    			'H - TERMINO',
    			'EMPRESA'
            ]
        ];

        return  $head;
    }

    public function registerEvents(): array{
        
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z','AA','AB'];

                foreach($letters as $letter){
                    $event->sheet->getColumnDimension($letter)->setAutoSize(true);
                }
                $cellRange = 'A6:K6'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                       ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setARGB('FF17a2b8');

                $cellRange = 'A1:A3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                
                $cellRange = 'D2:D4'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            }
        ];
    }  
}
