<?php

namespace App\Http\Controllers;

use App\Alive;
use Illuminate\Http\Request;

class AliveController extends Controller
{
    public function store()
    {
        $arr = ["user_id"=>auth()->id()];
        Alive::create($arr);
        return response(null,203);
    }
}