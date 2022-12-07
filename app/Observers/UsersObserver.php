<?php

namespace App\Observers;

use App\Models\Users;
use logClass;

class UsersObserver
{
    /**
     * Handle the Users "created" event.
     *
     * @param  \App\Models\Users  $users
     * @return void
     */
    public function created(Users $users)
    {
        //
    }

    /**
     * Handle the Users "updated" event.
     *
     * @param  \App\Models\Users  $users
     * @return void
     */
    public function updated(Users $users)
    {
        //
    }

    /**
     * Handle the Users "deleted" event.
     *
     * @param  \App\Models\Users  $users
     * @return void
     */
    public function deleted(Users $users)
    {
        //
    }

    /**
     * Handle the Users "restored" event.
     *
     * @param  \App\Models\Users  $users
     * @return void
     */
    public function restored(Users $users)
    {
        //
    }

    /**
     * Handle the Users "force deleted" event.
     *
     * @param  \App\Models\Users  $users
     * @return void
     */
    public function forceDeleted(Users $users)
    {
        //
    }
}
