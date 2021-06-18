<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Spatie\WelcomeNotification\WelcomeController as BaseWelcomeController;

class WelcomeController extends BaseWelcomeController
{
    protected function redirectTo()
    {
        return route('dashboard.index');
    }
}
