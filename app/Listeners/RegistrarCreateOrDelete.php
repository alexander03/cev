<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Bitacora;

class RegistrarCreateOrDelete
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {  

        $bitacora = new Bitacora();
        $bitacora->accion = $event->accion;
        $bitacora->tabla = $event->table;
        $bitacora->user_id = $event->user_id;
        $bitacora->registro_id = $event->element_id;
        $bitacora->save();
    }
}
