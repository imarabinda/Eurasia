<?php

namespace App\Observers;

use App\Models\Fabric;

class FabricObserver
{
    /**
     * Handle the Fabric "created" event.
     *
     * @param  \App\Models\Fabric  $fabric
     * @return void
     */
    public function created(Fabric $fabric)
    {
        //
    }

    /**
     * Handle the Fabric "updated" event.
     *
     * @param  \App\Models\Fabric  $fabric
     * @return void
     */
    public function updated(Fabric $fabric)
    {
    }
    
    /**
     * Handle the Fabric "deleted" event.
     *
     * @param  \App\Models\Fabric  $fabric
     * @return void
     */
    public function deleted(Fabric $fabric)
    {
        $fabric->fabric_rolls()->delete();
    }

    /**
     * Handle the Fabric "restored" event.
     *
     * @param  \App\Models\Fabric  $fabric
     * @return void
     */
    public function restored(Fabric $fabric)
    {
        //
    }

    /**
     * Handle the Fabric "force deleted" event.
     *
     * @param  \App\Models\Fabric  $fabric
     * @return void
     */
    public function forceDeleted(Fabric $fabric)
    {
        //
    }
}
