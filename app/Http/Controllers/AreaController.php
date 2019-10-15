<?php

namespace App\Http\Controllers;

use App\Area;
use App\PolygonNode;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $coords = PolygonNode::all();
        return view('admin.area.index',compact('coords'));
    }
    public function create()
    {
        return view('admin.area.create');
    }
    public function store(Request $request)
    {
        PolygonNode::truncate();
        $polygonNodes = $request->coordinates;
        foreach ($polygonNodes as $coord){
            $area = new PolygonNode();
            $area->lat= $coord[0];
            $area->long= $coord[1];
            $area->city_id = 1;
            $area->save();
        }
        return response()->json(['status'=>'ok']);
    }

    public function show(Area $area)
    {
        //
    }

    public function edit(Area $area)
    {
        //
    }

    public function update(Request $request, Area $area)
    {
        //
    }

    public function destroy(Area $area)
    {
        //
    }
}
