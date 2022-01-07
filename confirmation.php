<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Exhibitor;
use App\Models\Signature;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
// use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notification;
use App\Notifications\BookingNotification;;

class UploadController extends Controller
{
    //
    public function uploadSign(Request $request){

        // [$exhibitors => $exhibitor];
        $request -> validate([
            'personSignature' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($request -> hasFile('personSignature')){

            $signature = new Signature;

            $filename =  time().'_'.$request->personSignature->getClientOriginalName();
            $filesize =  $request->personSignature->getSize();
            $request->personSignature->storeAs('public/signature', $filename);


            $signature -> exhbId = $request -> exhbId;
            $signature -> companyName = $request -> companyName;
            $signature -> contactPerson = $request -> contactPerson;
            $signature -> name = $filename;
            $signature -> size = $filesize;
            $signature -> status = false;

            $signature -> save();

            return redirect('user/confirmRules');
        }
    }

    function confirmRules(){
        $data = ['LoggedExhibitorInfo' => Exhibitor::where('id', "=", session('LoggedExhibitor')) -> first()];
        return view('user.confirmRules', $data);
    }

    function uploadStatus(Request $request, $id){
        $data = Exhibitor::find($id);

        if($data -> status == 0){
            $data -> status = 1;
        }
        else{
            $data -> status = 0;
        }

        $data -> save();

        return redirect('user/registration');
    }
}
