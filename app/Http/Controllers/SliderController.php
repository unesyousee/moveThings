<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slider;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::paginate(15);
        return view('admin/blog/slider/index', compact('sliders'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $slider=new Slider;
        $slider->title = $request->title;
        $slider->abstract = $request->abstract;
        $slider->link = $request->link;
        $slider->status = 1;
        $photoName = time().'.'.$request->image->getClientOriginalExtension();
        $image = $request->image->move(public_path('images/slider/'), $photoName);
        $slider->image = env('APP_URL').('/images/slider/').$photoName;
        $slider->save();
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
        if ($request->disable){
            $slider=Slider::find($request->id);
            $slider->status=0;
            $slider->save();
            return back();
        }else if ($request->enable){
            $slider=Slider::find($request->id);
            $slider->status=1;
            $slider->save();
            return back();
        }
        else {
            $slider = Slider::find($id);
            $slider->title = $request->title;
            $slider->abstract = $request->abstract;
            $slider->link = $request->link;
            $slider->save();
            return back();
        }
    }
    public function destroy($id)
    {
        Slider::find($id)->delete();
        return back();
    }
    public function allSliders(){
        $sliders = Slider::where('status', 1)->get();
        return response()->json($sliders);
    }
}
