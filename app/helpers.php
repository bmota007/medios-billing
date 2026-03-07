<?php

use Illuminate\Support\Facades\Auth;

if (! function_exists('current_company')) {

    function current_company()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::user()->company;
    }

}
