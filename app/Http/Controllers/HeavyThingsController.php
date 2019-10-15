<?php

namespace App\Http\Controllers;

use App\HeavyThing;
use App\Price;
use Illuminate\Http\Request;

class HeavyThingsController extends Controller
{

    /*public function index()
    {
        $things= HeavyThing::all();
        return view('admin.heavy.index', compact('things'));
    }*/
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Tehran');
        $things= new HeavyThing;
        /*        $things->name= $request->name;
                $things->price= $request->price;
                $things->status= 1;
                $things->save();*/
        $things->create($request->all());
        return back();
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
        date_default_timezone_set('Asia/Tehran');
        $things= HeavyThing::find($id);
        $things->price= $request->price;
        $things->status=1;
        $things->save();
        return back();
    }
    public function destroy($id)
    {
        HeavyThing::find($id)->delete();
        return back();
    }

    public function all()
    {
        $data = HeavyThing::all();
        $data2 = Price::where('id','<=',4)->get();
        if (sizeof($data) == 0) {
            $status = "403";
            $message = "اطلاعاتی موجود نیست";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        } else {
            $status = "200";
            $message = "اطلاعات با موفقیت ارسال شد.";
            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => $data,
                "others" => $data2
            );
        }

        return response()->json($final);
    }

    public function allNew(Request $request)
    {
        $data = HeavyThing::all();
        $data2 = Price::all();
        if(intval($request->origin_floor) >= 9 ||  intval($request->destination_floor) >= 9){

            foreach($data2 as $d){
                if($d->id == 1){
                    $d->amount += Price::where('title','بیشتر از 9')->first()->amount;

                    break;
                }
            }


        }elseif(intval($request->origin_floor) >= 4 ||  intval($request->destination_floor) >= 4){

            foreach($data2 as $d){
                if($d->id == 1){
                    $d->amount += Price::where('title','بیشتر از 4')->first()->amount;
                }
            }

        }

        if (sizeof($data) == 0) {
            $status = "403";
            $message = "اطلاعاتی موجود نیست";
            $final = array(
                "status" => $status,
                "message" => $message
            );
        } else {
            $status = "200";
            $message = "اطلاعات با موفقیت ارسال شد.";
            $final = array(
                "status" => $status,
                "message" => $message,
                "data" => $data,
                "others" => $data2
            );
        }

        return response()->json($final);
    }
}
