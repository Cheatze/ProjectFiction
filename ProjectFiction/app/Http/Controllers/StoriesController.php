<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoriesController extends Controller
{

    public function showUpload()
    {
        return view('upload');
    }

    
}
