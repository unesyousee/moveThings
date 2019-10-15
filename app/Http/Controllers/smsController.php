<?php

namespace App\Http\Controllers;

use App\CarrierUser;
use Illuminate\Http\Request;

class smsController extends Controller
{
    public function smsToDrivers () {
        CarrierUser::where('parent_id', null);

    }
}
