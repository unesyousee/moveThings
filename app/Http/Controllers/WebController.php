<?php

namespace App\Http\Controllers;

use App\Web;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function settings()
    {
        $all = Web::all(['name','value']);
        return response()->json($all);
    }
}
