<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Funcs;
use Cache;
use DB;

class RegisterController extends Controller
{
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function emailSend($user)
    {
        $email = $user['email'];
        if (Cache::has('reg-' . $email)) {
            return ['status' => 0, 'message' => 'We have already sent you an email with a validation link. Confirm your registration by clicking on that link.'];
        } else {
            $key = $this->generateRandomString();
            $html = $this->htmlEmailTemplate($user['name'], $key);
            try {
                \Mail::send(array(), array(), function ($message) use ($html, $email) {
                    $message->to($email)->subject('Registration confirmation')->setBody($html, 'text/html');
                });
                Cache::put('reg-' . $email, 1, 3600);
                Cache::put($key, $user, 3600);
                return ['status' => -2, 'message' => 'We have sent you an email with a validation link. Please confirm with that link.'];
            } catch (\Exception $er) {
                return ['status' => 0, 'message' => $er->getMessage()];
            }
        }
    }

    private function generateRandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function htmlEmailTemplate($name, $key)
    {
        $appName = env('APP_NAME');
        $appUrl = env('APP_URL');
        $confirm_web = $appUrl . '/register/confirm/w/' . $key;
        $confirm_app = str_replace(['https','http'], 'app', $appUrl) . '/register/confirm/w/' . $key;
        return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;"> <style> @media only screen and (max-width: 600px) { .inner-body { width: 100% !important; } .footer { width: 100% !important; } } @media only screen and (max-width: 500px) { .button { width: 100% !important; } } </style> <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"> <tr> <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"> <tr> <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; border-bottom: 1px solid #EDEFF2; border-top: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"> <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"> <tr> <td class="content-cell" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;"> <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">Hello ' . $name . ',</h1> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Thank you for joining ' . $appName . '.</p>We would like to confirm that your account was created successfully. To confirm registration click the link below.</b> <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 30px auto; padding: 0; text-align: center; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"> <tr> <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <tr> <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <table border="0" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <tr> <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <a href="' . $confirm_app . '" class="button button-blue" target="_blank" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #FFF; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: #18ad45; border-top: 5px solid #18ad45; border-right: 18px solid #18ad45; border-bottom: 5px solid #18ad45; border-left: 18px solid #18ad45;">Confirm by app</a> </td> <td><span style="margin: 0px 10px;">or</span></td> <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <a href="' . $confirm_web . '" class="button button-blue" target="_blank" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #FFF; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: #3097D1; border-top: 5px solid #3097D1; border-right: 18px solid #3097D1; border-bottom: 5px solid #3097D1; border-left: 18px solid #3097D1;">Confirm by web</a> </td> </tr> </table> </td> </tr> </table> </td> </tr> </table> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">If you experience any issues logging into your account do not hesitate to contact us.</p> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Best,<br>The ' . $appName . ' team</p> <table class="subcopy" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border-top: 1px solid #EDEFF2; margin-top: 25px; padding-top: 25px;"> <tr> <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; line-height: 1.5em; margin-top: 0; text-align: left; font-size: 12px;">If you’re having trouble clicking the "Confirm Registration" button, copy and paste the URL below into your web browser: <a href="' . $confirm_web . '" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #3869D4;"></a><a href="' . $confirm_web . '" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #3869D4;">' . $confirm_web . '</a></p> </td> </tr> </table> </td> </tr> </table> </td> </tr> <tr> <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"> <tr> <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;"> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #AEAEAE; font-size: 12px; text-align: center;">© ' . date('Y') . ' ' . $appName . '. All rights reserved.</p> </td> </tr> </table> </td> </tr> </table> </td> </tr> </table></body></html>';
    }
	
	private function isDiposable($email)
    {
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, 'https://disposable.debounce.io/?email='.$email);
            $result = curl_exec($ch);
            curl_close($ch);
            $obj = json_decode($result);
            return (String)$obj->disposable == 'true';
        } catch (\Exception $e){
            return false;
        }
    }

    public function apiCreate(Request $req)
    {
   
        try {
            $cc = strtolower($req['cc']);
            $did = $req['did'];
            $banned = Funcs::isBanned(null, $did);
            $devicemodel = $req['devicemodel'];
            
            if ($banned) {
                
                return ['data' => json_encode(['code'=>401,'status' => 0, 'message' => $banned->reason])];
            }
            
            $ref = $req['rb'];
            if ($ref != 'none' && strlen($ref) != 13) {
                
                return ['data' => json_encode([
                    'status' => 0,
                    'message' => 'Invalid referral code!'
                ])];
                
                 
            }
            
            $isBlocked = Funcs::isRegBlocked($ref);
            if ($isBlocked) {
                return ['data' => json_encode([
                    'status' => 0,
                    'message' => $isBlocked
                ])];
            }
            
            $uid = strtoupper(uniqid());
            while (User::where('userid', $uid)->first()) {
                $uid = strtoupper(uniqid());
            };
            
            if (env('SINGLE_ACCOUNT') == 1) {
                $check = DB::table('users')->where('device_id', $did)->first();
            } else {
                $check = null;
            }
            
            if ($check) {
                $res = ['code' => 201,'status' => 0, 'message' => "Multiple accounts not allowed!"];
                
            } else {
                
                $validator = $this->validator($req->all());
                
                if ($validator->fails()) {
                   
                    return ['code' => 201,'status' => 0 ,'message' => $validator->errors()->first()];
                }
                
                $eml = $req['email'];
                if (env('DISPOSABLE_CHECK') == 1 && $this->isDiposable($eml)) {
                    return json_encode(['code' => 201,'status' => 0, 'message' => "You have entered a blacklisted email address!"]);
                }
                
                $u_data = [
                    'userid' => $uid,
                    'email' => $eml,
                    'name' => $req['name'],
                    'password' => bcrypt($req['password']),
                    'device_id' => $did,
                    'device_name' => $devicemodel,
                    'ip' => \Request::ip(),
                    'country' => $cc,
                    'updated_at' => Carbon::now()
                ];
                if (env('REG_VALIDATION') == 1) {
                    $u_data['ref'] = $ref;
                   
                     return json_encode(['code' => 201,'status' => 0, 'message' => $this->emailSend($u_data)]);
                }
                $user = User::create($u_data);
                $token = $user->id.'|'.Str::random(80);
                DB::table('users')->where('userid', $user->userid)->update(['remember_token' => $token]);
                Funcs::addref($user, $ref);
                
                $res = [ 
                    'code' => 201,
                    'status' => 1 ,
                    'message' => 'Your account has been registered successfully',
                    'token' => $token,
                    'name' => $req['name'],
                    'avatar' => $req['avatar'],
                    'userid' => $uid,
                    'cc' => $cc,
                    'email' => $eml,
                    'version_code' => env('USER_VERSIONCODE'),
                    'update_force' => env('USER_FORCE_UPDATE')
                    ];
                    
                Funcs::delUser($user->id, $did);
            }
           
            
            return json_encode($res);
            
        } catch (\Exception $e) {
            
            
           // return ['code' => 401,'status' => 0 ,'message' => 'Could not resister your account with this information!'.$e->getMessage()];
            return ['code' => 401,'status' => 0 ,'message' => 'Could not resister your account with this information!'];
          
        }
    }

    public function confirmReg($type, $key = null)
    {
        try {
            if ($key && Cache::has($key)) {
                $u_data = Cache::get($key);
                $ref = $u_data['ref'];
                unset($u_data['ref']);
                $user = User::create($u_data);
                $token = $user->id.'|'.Str::random(80);
                DB::table('users')->where('userid', $user->userid)->update(['remember_token' => $token]);
                Funcs::addref($user, $ref);
                Funcs::delUser($user->id, $user->device_id);
                Cache::forget($key);
                Cache::forget('reg-'.$user->email);
                if ($type == 'm') {
                    return ['status' => 1, 'message' => $token, 'userid' => $user->userid];
                }
                return redirect()->route('forget')->with('success', 'Registration confirmation successful. Now you can login to your account.');
            } else {
                if ($type == 'm') {
                    return ['status'=> 0, 'message' => 'Either confirmation link expired or invalid confirmation link!'];
                }
                return redirect()->route('forget')->with('error', 'Either confirmation link expired or invalid confirmation link!');
            }
        } catch (\Exception $e) {
            if ($type == 'm') {
                return ['status'=> 0, 'message' => $e->getMessage()];
            }
            return redirect()->route('forget')->with('error', $e->getMessage());
        }
    }
}
