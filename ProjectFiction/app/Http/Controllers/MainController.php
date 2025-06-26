<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\Genre;

class MainController extends Controller
{

    public function index()
    {
        return view('index');
    }
}
