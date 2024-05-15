<?php

use App\Models\User;
use Illuminate\Support\Carbon;

if (!function_exists('test')) {
    function test($date)
    {
        // return Carbon::createFromFormat('m-d-Y', strtotime($date))->format('Y-m-d');
        return Carbon::parse($date)->format('m-d-Y');
        // ...
    }

    function userDetail()
    {
        $user = User::find(1);
        // dd($user->name);
        return $user;
    }
}
