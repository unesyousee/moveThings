<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request, $user_id = null)
    {
        $allowedExt = ['image/jpeg','image/png'];
        $ext = $request->file->getClientMimeType();
        if(!in_array($ext, $allowedExt)){
            return back()->withErrors('فایل پشتیبانی نمی‌شود');
        }
        $uuid = uniqid();
        $path = Storage::putFile('carrier_users', $request->file('file'));
        $path ='/storage/'.$path;
        $file = new File();
        $file->uuid = $uuid;
        $file->user_id = $user_id ? $user_id : $request->user_id;
        $file->path = $path;
        $file->save();
        return back();
    }
    public function file(Request $request){

    }
}
