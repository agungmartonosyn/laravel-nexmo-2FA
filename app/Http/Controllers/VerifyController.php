<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nexmo\Client\Exception\Exception;
use Nexmo\Laravel\Facade\Nexmo;

class VerifyController extends Controller
{
    public function show(Request $request)
    {
        return view('verify');
    }

    public function verify(Request $request) 
    {
        $this->validate($request, [
            'code' => 'size:4',
        ]);

        $user_id = $request->session()->get('verify:user:id');
        $user = User::where('id',$user_id)->get()->first();

        try {
            $response = Nexmo::verify()->check(
                $user->request_id,
                $request->code
            );

            Auth::loginUsingId($request->session()->pull('verify:user:id'));
            return redirect('/home');

        } catch (Exception $e) {
            return redirect()->back()->withErrors([
                'code' => $e->getMessage()
            ]);
        }

        return view('verify');
    }

    public function resend_sms(Request $request)
    {
        if($request->session()->has('verify:user:id') && !Auth::user())
        {
            $user_id = $request->session()->get('verify:user:id');
            $user = User::where('id',$user_id)->get()->first();

            try {
                $verification = Nexmo::verify()->start([
                    'number' => $user->full_number,
                    'brand'  => 'Laravel Nexmo Verify Login',
                    'code_length'  => '4'
                ]);
                $user->request_id = $verification->getRequestId();
                $user->save();
                return redirect()->back()->with('resend_sms', 'Resend Success');
            } catch (Exception $e) {
                return redirect()->back()->withErrors([
                    'code' => $e->getMessage()
                ]);
            }

        }else{
            return  redirect()->route('login');
        }
    }

	
}