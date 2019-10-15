<?php

namespace App\Http\Controllers;
use App;
use Illuminate\Http\Request;

class priceController extends Controller
{
    public function index()
    {
        $prices= App\Price::all();
        return view('admin.prices.index', compact('prices'));
    }
    public function store(Request $request)
    {
        $prices= new App\Price();
        $prices->title= $request->title;
        $prices->amount= $request->amount;
        $prices->status= 1;
        $prices->save();
        return back();
    }


    public function update(Request $request, $id)
    {
        $prices= App\Price::find($id);
        $prices->amount= $request->amount;
        $prices->status=1;
        $prices->save();
        return back();
    }
    public function destroy($id)
    {
        App\Price::find($id)->delete();
        return back();
    }
}
