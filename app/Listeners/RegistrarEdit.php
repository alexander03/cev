<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Bitacora;

class RegistrarEdit
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
        $bitacora->accion = 'Editar';
        $bitacora->tabla = $event->table;
        $bitacora->user_id = $event->user_id;
        //$bitacora->valor_anterior = $event->element_org->toJson();
        $bitacora->valor_anterior = $event->element_org;
        $bitacora->valor_posterior = $event->element_final->toJson();
        $bitacora->save();
    }
}
