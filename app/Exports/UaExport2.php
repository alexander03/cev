<?php

namespace App\Exports;

use App\Concesionaria;
use App\Ua;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UaExport2 implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
    
        /*$uaList = Ua::select('ua.codigo', 'ua.descripcion', 'u_padre.codigo as padreCodigo', 'u_padre.descripcion as padreDesc','ua.padre',
        'ua.habilitada','ua.fecha_inicio','ua.fecha_fin',
        'rep.nombre as responsable','c.razonsocial as concesionaria','u_padre.ua_padre_id') 
            -> leftjoin('ua as u_padre', 'u_padre.id', '=', 'ua.ua_padre_id')
            -> join('concesionaria as c','c.id','=','ua.concesionaria_id')
            -> leftjoin('responsable as rep','rep.id','=','ua.responsable_id')
            -> whereNull('ua.deleted_at')
            -> where('ua.concesionaria_id','=',$this -> getConsecionariaActual())
            //-> orderBy('ua.id','asc')
            -> orderBy(DB::raw('case when ua.ua_padre_id is null then rpad(ua.codigo,24,"0") else (concat(rpad(ua.codigo,8,"0"),rpad(u_padre.codigo,16,"0"))) end'),'desc')
            -> get();         

        foreach($uaList as $ua) {
            if(!$ua->getOriginal('padreCodigo')){
                $ua->padreCodigo = 'SIN PADRE';
            }else{
                $ua->descripcion = '                  '.$ua->descripcion;
                if(!is_null($ua->ua_padre_id) && $ua->ua_padre_id>0){
                    $ua->descripcion = '                  '.$ua->descripcion;    
                }
            }
            $ua->ua_padre_id='';
            if(!$ua->getOriginal('padreDesc')) $ua->padreDesc = 'SIN PADRE';
            ($ua->getOriginal('habilitada') == 0) ? $ua->habilitada = 'INHABILITADA' : $ua->habilitada = 'HABILITADA';
            if(!$ua->getOriginal('fecha_fin')){
               $ua->fecha_fin = 'ILIMITADA';
            }else{
               $ua->fecha_fin = date("d/m/Y",strtotime($ua->fecha_fin)); 
            }
            $ua->fecha_inicio = date("d/m/Y",strtotime($ua->fecha_inicio));
        }*/

        $uaList = Ua::select('ua.codigo', 'ua.descripcion', 'u_padre.codigo as padreCodigo', 'u_padre.descripcion as padreDesc','ua.padre',
        'ua.habilitada','ua.fecha_inicio','ua.fecha_fin',
        'rep.nombre as responsable','c.razonsocial as concesionaria','ua.ua_padre_id','ua.id') 
            -> leftjoin('ua as u_padre', 'u_padre.id', '=', 'ua.ua_padre_id')
            -> join('concesionaria as c','c.id','=','ua.concesionaria_id')
            -> leftjoin('responsable as rep','rep.id','=','ua.responsable_id')
            -> whereNull('ua.deleted_at')
            -> whereNull('ua.ua_padre_id')
            -> where('ua.concesionaria_id','=',$this -> getConsecionariaActual())
            //-> orderBy('ua.id','asc')
            //-> orderBy(DB::raw('case when ua.ua_padre_id is null then rpad(ua.codigo,24,"0") else (concat(rpad(ua.codigo,8,"0"),rpad(u_padre.codigo,16,"0"))) end'),'desc')
            -> orderBy(DB::raw('rpad((case when ua.codigo=\'TOTAL\' then \'0TOTAL\' else ua.codigo end),16,"0")'),'asc')
            //-> orderBy('ua.codigo','asc')
            -> get();    
            
        $lista = array();
        foreach($uaList as $ua) {
            $ua->padreCodigo = 'SIN PADRE';
            if(!$ua->getOriginal('padreDesc')) $ua->padreDesc = 'SIN PADRE';
            ($ua->getOriginal('habilitada') == 0) ? $ua->habilitada = 'INHABILITADA' : $ua->habilitada = 'HABILITADA';
            if(!$ua->getOriginal('fecha_fin')){
               $ua->fecha_fin = 'ILIMITADA';
            }else{
               $ua->fecha_fin = date("d/m/Y",strtotime($ua->fecha_fin)); 
            }
            $ua->fecha_inicio = date("d/m/Y",strtotime($ua->fecha_inicio));
            
            $lista[]=array("codigo"=>" ".$ua->codigo,"descripcion"=>$ua->descripcion,"padreCodigo"=>$ua->padreCodigo,"padreDesc"=>$ua->padreDesc,"padre"=>$ua->padre,"habilitada"=>$ua->habilitada,
                        "fecha_inicio"=>$ua->fecha_inicio,"fecha_fin"=>$ua->fecha_fin,"nombre"=>$ua->responsable,"concesionaria"=>$ua->concesionaria);
            
            $uaList2 = Ua::select('ua.codigo', 'ua.descripcion', 'u_padre.codigo as padreCodigo', 'u_padre.descripcion as padreDesc','ua.padre',
            'ua.habilitada','ua.fecha_inicio','ua.fecha_fin',
            'rep.nombre as responsable','c.razonsocial as concesionaria','u_padre.ua_padre_id','ua.id') 
                -> leftjoin('ua as u_padre', 'u_padre.id', '=', 'ua.ua_padre_id')
                -> join('concesionaria as c','c.id','=','ua.concesionaria_id')
                -> leftjoin('responsable as rep','rep.id','=','ua.responsable_id')
                -> whereNull('ua.deleted_at')
                -> where('ua.ua_padre_id','=',$ua->id)
                -> where('ua.concesionaria_id','=',$this -> getConsecionariaActual())
                -> orderBy('ua.codigo','asc')
                -> get();   
                
            foreach($uaList2 as $ua2){
                if(!$ua2->getOriginal('padreCodigo')){
                    $ua2->padreCodigo = 'SIN PADRE';
                }else{
                    $ua2->padreCodigo =" ".$ua2->padreCodigo;
                    $ua2->codigo='        '.$ua2->codigo;
                    $ua2->descripcion = '                  '.$ua2->descripcion;
                    if(!is_null($ua2->ua_padre_id) && $ua2->ua_padre_id>0){
                        $ua2->descripcion = '                  '.$ua2->descripcion;    
                    }
                }
                $ua->ua_padre_id='';
                if(!$ua2->getOriginal('padreDesc')) $ua2->padreDesc = 'SIN PADRE';
                ($ua2->getOriginal('habilitada') == 0) ? $ua2->habilitada = 'INHABILITADA' : $ua2->habilitada = 'HABILITADA';
                if(!$ua2->getOriginal('fecha_fin')){
                   $ua2->fecha_fin = 'ILIMITADA';
                }else{
                   $ua2->fecha_fin = date("d/m/Y",strtotime($ua2->fecha_fin)); 
                }
                $ua2->fecha_inicio = date("d/m/Y",strtotime($ua2->fecha_inicio));
                
                $lista[]=array("codigo"=>$ua2->codigo,"descripcion"=>$ua2->descripcion,"padreCodigo"=>$ua2->padreCodigo,"padreDesc"=>$ua2->padreDesc,"padre"=>$ua2->padre,"habilitada"=>$ua2->habilitada,
                            "fecha_inicio"=>$ua2->fecha_inicio,"fecha_fin"=>$ua2->fecha_fin,"nombre"=>$ua2->responsable,"concesionaria"=>$ua2->concesionaria);
                    
                $uaList3 = Ua::select('ua.codigo', 'ua.descripcion', 'u_padre.codigo as padreCodigo', 'u_padre.descripcion as padreDesc','ua.padre',
                    'ua.habilitada','ua.fecha_inicio','ua.fecha_fin',
                    'rep.nombre as responsable','c.razonsocial as concesionaria','u_padre.ua_padre_id','ua.id') 
                        -> leftjoin('ua as u_padre', 'u_padre.id', '=', 'ua.ua_padre_id')
                        -> join('concesionaria as c','c.id','=','ua.concesionaria_id')
                        -> leftjoin('responsable as rep','rep.id','=','ua.responsable_id')
                        -> whereNull('ua.deleted_at')
                        -> where('ua.ua_padre_id','=',$ua2->id)
                        -> where('ua.concesionaria_id','=',$this -> getConsecionariaActual())
                        -> orderBy('ua.codigo','asc')
                        -> get();   
                
                foreach($uaList3 as $ua3) {
                    if(!$ua3->getOriginal('padreCodigo')){
                        $ua3->padreCodigo = 'SIN PADRE';
                    }else{
                        $ua3->padreCodigo =" ".$ua3->padreCodigo;
                        $ua3->codigo='                '.$ua3->codigo;
                        $ua3->descripcion = '                  '.$ua3->descripcion;
                        if(!is_null($ua3->ua_padre_id) && $ua3->ua_padre_id>0){
                            $ua3->descripcion = '                  '.$ua3->descripcion;    
                        }
                    }
                    $ua3->ua_padre_id='';
                    if(!$ua3->getOriginal('padreDesc')) $ua3->padreDesc = 'SIN PADRE';
                    ($ua3->getOriginal('habilitada') == 0) ? $ua3->habilitada = 'INHABILITADA' : $ua3->habilitada = 'HABILITADA';
                    if(!$ua3->getOriginal('fecha_fin')){
                       $ua3->fecha_fin = 'ILIMITADA';
                    }else{
                       $ua3->fecha_fin = date("d/m/Y",strtotime($ua3->fecha_fin)); 
                    }
                    $ua3->fecha_inicio = date("d/m/Y",strtotime($ua3->fecha_inicio));
                    
                    $lista[]=array("codigo"=>$ua3->codigo,"descripcion"=>$ua3->descripcion,"padreCodigo"=>$ua3->padreCodigo,"padreDesc"=>$ua3->padreDesc,"padre"=>$ua3->padre,"habilitada"=>$ua3->habilitada,
                                "fecha_inicio"=>$ua3->fecha_inicio,"fecha_fin"=>$ua3->fecha_fin,"nombre"=>$ua3->responsable,"concesionaria"=>$ua3->concesionaria);
                                
                    $uaList4 = Ua::select('ua.codigo', 'ua.descripcion', 'u_padre.codigo as padreCodigo', 'u_padre.descripcion as padreDesc','ua.padre',
                    'ua.habilitada','ua.fecha_inicio','ua.fecha_fin',
                    'rep.nombre as responsable','c.razonsocial as concesionaria','u_padre.ua_padre_id','ua.id') 
                        -> leftjoin('ua as u_padre', 'u_padre.id', '=', 'ua.ua_padre_id')
                        -> join('concesionaria as c','c.id','=','ua.concesionaria_id')
                        -> leftjoin('responsable as rep','rep.id','=','ua.responsable_id')
                        -> whereNull('ua.deleted_at')
                        -> where('ua.ua_padre_id','=',$ua3->id)
                        -> where('ua.concesionaria_id','=',$this -> getConsecionariaActual())
                        -> orderBy('ua.codigo','asc')
                        -> get();   
                
                    foreach($uaList4 as $ua4) {
                        if(!$ua4->getOriginal('padreCodigo')){
                            $ua4->padreCodigo = 'SIN PADRE';
                        }else{
                            $ua4->padreCodigo =" ".$ua4->padreCodigo;
                            $ua4->descripcion = '                                    '.$ua4->descripcion;
                            $ua4->codigo='                        '.$ua4->codigo;
                            if(!is_null($ua4->ua_padre_id) && $ua4->ua_padre_id>0){
                                $ua4->descripcion = '                  '.$ua4->descripcion;    
                            }
                        }
                        $ua4->ua_padre_id='';
                        if(!$ua4->getOriginal('padreDesc')) $ua4->padreDesc = 'SIN PADRE';
                        ($ua4->getOriginal('habilitada') == 0) ? $ua4->habilitada = 'INHABILITADA' : $ua4->habilitada = 'HABILITADA';
                        if(!$ua4->getOriginal('fecha_fin')){
                           $ua4->fecha_fin = 'ILIMITADA';
                        }else{
                           $ua4->fecha_fin = date("d/m/Y",strtotime($ua4->fecha_fin)); 
                        }
                        $ua4->fecha_inicio = date("d/m/Y",strtotime($ua4->fecha_inicio));
                        
                        $lista[]=array("codigo"=>$ua4->codigo,"descripcion"=>$ua4->descripcion,"padreCodigo"=>$ua4->padreCodigo,"padreDesc"=>$ua4->padreDesc,"padre"=>$ua4->padre,"habilitada"=>$ua4->habilitada,
                                    "fecha_inicio"=>$ua4->fecha_inicio,"fecha_fin"=>$ua4->fecha_fin,"nombre"=>$ua4->responsable,"concesionaria"=>$ua4->concesionaria);
                                    
                        $uaList5 = Ua::select('ua.codigo', 'ua.descripcion', 'u_padre.codigo as padreCodigo', 'u_padre.descripcion as padreDesc','ua.padre',
                        'ua.habilitada','ua.fecha_inicio','ua.fecha_fin',
                        'rep.nombre as responsable','c.razonsocial as concesionaria','u_padre.ua_padre_id','ua.id') 
                            -> leftjoin('ua as u_padre', 'u_padre.id', '=', 'ua.ua_padre_id')
                            -> join('concesionaria as c','c.id','=','ua.concesionaria_id')
                            -> leftjoin('responsable as rep','rep.id','=','ua.responsable_id')
                            -> whereNull('ua.deleted_at')
                            -> where('ua.ua_padre_id','=',$ua4->id)
                            -> where('ua.concesionaria_id','=',$this -> getConsecionariaActual())
                            -> orderBy('ua.codigo','asc')
                            -> get();   
                    
                        foreach($uaList5 as $ua5) {
                            if(!$ua5->getOriginal('padreCodigo')){
                                $ua5->padreCodigo = 'SIN PADRE';
                            }else{
                                $ua5->padreCodigo =" ".$ua5->padreCodigo;
                                $ua5->descripcion = '                                             '.$ua5->descripcion;
                                $ua5->codigo='                                '.$ua5->codigo;
                                if(!is_null($ua5->ua_padre_id) && $ua5->ua_padre_id>0){
                                    $ua5->descripcion = '                  '.$ua5->descripcion;    
                                }
                            }
                            $ua5->ua_padre_id='';
                            if(!$ua5->getOriginal('padreDesc')) $ua5->padreDesc = 'SIN PADRE';
                            ($ua5->getOriginal('habilitada') == 0) ? $ua5->habilitada = 'INHABILITADA' : $ua5->habilitada = 'HABILITADA';
                            if(!$ua5->getOriginal('fecha_fin')){
                               $ua5->fecha_fin = 'ILIMITADA';
                            }else{
                               $ua5->fecha_fin = date("d/m/Y",strtotime($ua5->fecha_fin)); 
                            }
                            $ua5->fecha_inicio = date("d/m/Y",strtotime($ua5->fecha_inicio));
                            
                            $lista[]=array("codigo"=>$ua5->codigo,"descripcion"=>$ua5->descripcion,"padreCodigo"=>$ua5->padreCodigo,"padreDesc"=>$ua5->padreDesc,"padre"=>$ua5->padre,"habilitada"=>$ua5->habilitada,
                                        "fecha_inicio"=>$ua5->fecha_inicio,"fecha_fin"=>$ua5->fecha_fin,"nombre"=>$ua5->responsable,"concesionaria"=>$ua5->concesionaria);
                                        
                            $uaList6 = Ua::select('ua.codigo', 'ua.descripcion', 'u_padre.codigo as padreCodigo', 'u_padre.descripcion as padreDesc','ua.padre',
                            'ua.habilitada','ua.fecha_inicio','ua.fecha_fin',
                            'rep.nombre as responsable','c.razonsocial as concesionaria','u_padre.ua_padre_id','ua.id') 
                                -> leftjoin('ua as u_padre', 'u_padre.id', '=', 'ua.ua_padre_id')
                                -> join('concesionaria as c','c.id','=','ua.concesionaria_id')
                                -> leftjoin('responsable as rep','rep.id','=','ua.responsable_id')
                                -> whereNull('ua.deleted_at')
                                -> where('ua.ua_padre_id','=',$ua5->id)
                                -> where('ua.concesionaria_id','=',$this -> getConsecionariaActual())
                                -> orderBy('ua.codigo','asc')
                                -> get();   
                        
                            foreach($uaList6 as $ua6) {
                                if(!$ua6->getOriginal('padreCodigo')){
                                    $ua6->padreCodigo = 'SIN PADRE';
                                }else{
                                    $ua6->padreCodigo =" ".$ua6->padreCodigo;
                                    $ua6->descripcion = '                                                       '.$ua6->descripcion;
                                    $ua6->codigo='                                       '.$ua6->codigo;
                                    if(!is_null($ua6->ua_padre_id) && $ua6->ua_padre_id>0){
                                        $ua6->descripcion = '                  '.$ua6->descripcion;    
                                    }
                                }
                                $ua6->ua_padre_id='';
                                if(!$ua6->getOriginal('padreDesc')) $ua6->padreDesc = 'SIN PADRE';
                                ($ua6->getOriginal('habilitada') == 0) ? $ua6->habilitada = 'INHABILITADA' : $ua6->habilitada = 'HABILITADA';
                                if(!$ua6->getOriginal('fecha_fin')){
                                   $ua6->fecha_fin = 'ILIMITADA';
                                }else{
                                   $ua6->fecha_fin = date("d/m/Y",strtotime($ua6->fecha_fin)); 
                                }
                                $ua6->fecha_inicio = date("d/m/Y",strtotime($ua6->fecha_inicio));
                                
                                $lista[]=array("codigo"=>$ua6->codigo,"descripcion"=>$ua6->descripcion,"padreCodigo"=>$ua6->padreCodigo,"padreDesc"=>$ua6->padreDesc,"padre"=>$ua6->padre,"habilitada"=>$ua6->habilitada,
                                            "fecha_inicio"=>$ua6->fecha_inicio,"fecha_fin"=>$ua6->fecha_fin,"nombre"=>$ua6->responsable,"concesionaria"=>$ua6->concesionaria);
                                            
                                        
                            } 
                        }
                    }
                }
            }
        }
        return collect($lista);
        //return $uaList;
    }

    public function headings(): array{
        return [
            'Codigo',
            'Descripción',
            'Código ua padre',
            'Descripción ua padre',
            'Es Padre',
            'Situacion',
            'Fecha Inicio',
            'Fecha Fin',
            'Responsable',
            'Concesionaria'
        ];
    }
    
    private function getConsecionariaActual(){

        $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
        $idConcAct=$ConcesionariaActual[0]->id;

        return $idConcAct;
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
            }
        ];
    }  
}
