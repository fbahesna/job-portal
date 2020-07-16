<?php

namespace App\Http\Controllers;

class homeController extends Controller
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

    public function index()
    {
        return "You in home controller";
    }

    //
}
