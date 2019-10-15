<?php

namespace App\Http\Controllers;

use App\Carrier;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $carriers = Carrier::all();
        return view('admin/service/index',compact('carriers'));
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        //
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        //
    }
    public function update(Request $request, $id)
    {
        if ($request->disable){
            $service=carrier::find($request->id);
            $service->status=0;
            $service->save();
            return back();
        }elseif ($request->enable){
            $service=carrier::find($request->id);
            $service->status=1;
            $service->save();
            return back();
        }
    }
    public function destroy($id)
    {
        //
    }
}
