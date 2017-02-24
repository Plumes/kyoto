<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
    public function test() {
        return Auth::user();
    }
}
