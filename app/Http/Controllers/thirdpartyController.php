<?php

namespace App\Http\Controllers;

use App\Observer;
use App\Thirdparty;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;

class thirdpartyController extends Controller
{
    public function show($id)
    {
        $thirdparty = Thirdparty::find($id);
        return view('admin.thirdparty.show', compact('thirdparty'));
    }

    public function create()
    {
        return view('admin.thirdparty.create');
    }

    public function store(Request $request)
    {
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->save();
        $third = new Thirdparty();
        $third->commission = $request->commission;
        $user->thirdparty()->save($third);
        $third->save();
        return back();
    }
}
