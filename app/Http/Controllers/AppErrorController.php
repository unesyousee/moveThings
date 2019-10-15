<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 3/6/19
 * Time: 3:39 PM
 */

namespace App\Http\Controllers;


use App\AppError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AppErrorController extends Controller{

    public function insertNewError(Request $request){
        $app_error = new AppError();
        $app_error->url = $request->url;
        $app_error->header = $request->header;
        $app_error->body = $request->body;
        $app_error->response = $request->response;
        $app_error->save();

        $status = "200";
        $message = "با موفقیت اعمال شد";
        $final = array(
            "status" => $status,
            "message" => $message
        );

        return response()->json($final);

    }

    public function index()
    {
        $App_errors = AppError::orderBy('created_at','desc')->paginate(50);
        return View::make('admin/errors/index')->with('App_errors', $App_errors);
    }
}