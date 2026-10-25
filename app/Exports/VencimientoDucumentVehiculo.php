<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use App\Vehiculodocument;

class VencimientoDucumentVehiculo implements FromCollection ,WithHeadings
{
 	
 	private $id;

	public function __construct($id)
    {
        $this->id = $id;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$consulta = "select u.codigo , v.modelo , m.descripcion , v.placa , v.anio , c.descripcion , d.tipo , d.fecha 
        		from vehiculodocument d join vehiculo v on(d.vehiculo_id = v.id ) join ua u on(v.ua_id = u.id) 
        		join marca m on(v.marca_id = m.id) join carroceria c on(v.carroceria_id = c.id) where d.id = {$this->id}
        		";
        $resultado = Vehiculodocument::select('u.codigo' , 'v.modelo' , 'ma.descripcion as marca' , 'v.placa' , 
        			'v.anio' , 'c.descripcion as carroceria' , 'vehiculodocument.tipo' , 'vehiculodocument.fecha')
        			->join('vehiculo as v','v.id','=','vehiculodocument.vehiculo_id')
        			->join('marca as ma','ma.id','=','v.marca_id')
                    ->join('ua as u','u.id','=','v.ua_id')
        			->join('carroceria as c','c.id','=','v.carroceria_id')
        			->where('vehiculodocument.id','=',$this->id)->get();
        return $resultado;
    }

    public function headings(): array{
        return [
            'UA',
            'Modelo',
            'Marca',
            'placa',
            'Año',
            'Carroceria',
            'Documento',
            'Fecha de V'
        ];
    }
}
