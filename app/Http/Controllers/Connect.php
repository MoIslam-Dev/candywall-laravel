<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;
use Cache;
use Funcs;
use DB;

class Connect extends Controller
{
    public function geo()
    {
        
        try {
        return Cache::rememberForever('connect_geo', function () {
            return ['message' => DB::table('misc')->where('name', 'geo_api')->first()->data];
        });
        
    } catch (\Exception $e) {
            return Cache::remember('connect_general_err', 3600, function () use ($key) {
                return ['size' => $key, 'data' => Funcs::enc(json_encode(['status' => 0, 'message' => 'Unexpected error occurred!']))];
            });
        }
    }

    public function check(Request $req){
        
    
     //$key = Funcs::encKey();
    
        try {
            
            $ip = \Request::ip();
            $cc = strtolower($req['cc']);
         
            Funcs::addOnline($cc);
            
           $offers = Cache::rememberForever('connect_offers', function () {
                return [
                    'offerwall_sdk' => DB::table('offerwalls')->where('type', 1)->where('enabled', 1)
                        ->leftJoin('postbacks', 'postbacks.offerwall_id', '=', 'offerwalls.id') ->get(['data','name','title','description','network_image']),
                       
                    'offerwall_cpa' => DB::table('offerwalls')->where('type', 2)->where('enabled', 1)
                        ->leftJoin('postbacks', 'postbacks.offerwall_id', '=', 'offerwalls.id')->get(['offerwalls.id','data','name','title','description','network_image']),
                        
                    'offerwall_cpv' => DB::table('offerwalls')->where('type', 3)->where('enabled', 1)
                        ->leftJoin('postbacks', 'postbacks.offerwall_id', '=', 'offerwalls.id')->get(['data','name','title','description','network_image']),
                        
                    'offerwall_web' => DB::table('offerwalls')->where('type', 4)->where('enabled', 1)
                        ->leftJoin('postbacks', 'postbacks.offerwall_id', '=', 'offerwalls.id') ->get(['data','name','title','description','network_image'])
                       
                ];
            });
            
            
            $chat = env('CHAT_DISABLED');
            $currency = env('CURRENCY_NAME');
            $root = env('ROOT_BLOCK');
            $a_root = env('AUTO_BAN_ROOT');
            $vpn = env('VPN_BLOCK');
            $m_vpn = env('VPN_MONITOR');
            $interval = (int)env('BALANCE_INTERVAL');
            $conversion = env('CASHTOPTS') * env('PAY_PCT') / 100;
            $Howmuch = env('CASHTOPTS');
            $user = Funcs::userinfo($req);
            $googleplay = env('GOOGLEPLAY');
            $enckey = env('ENC_KEY');
            
            $status = 1;
            
            
			$misc = 'emu='.env('EMULATOR_DETECT').'@_@';
			
            if ($user) {
                
                $tok = $req['fid'];
                
                Funcs::updateFCT($user->userid, $user->email, $tok);
                $banned = Funcs::isBanned($user, $user->device_id);
                if ($banned) {
                    
                    return json_encode(['code' => 400, 'status' => -2 , 'message' => $banned->reason, 'info' => '']);
                }
                if (strtolower($user->country) != $cc && env('BAN_CC_CHANGE') == 1) {
                    if ($user->device_id != 'none') {
                        DB::table('banned_users')->insert([
                            'userid' => $user->userid,
                            'reason' => "Auto ban CC",
                            'device_id' => $user->device_id
                        ]);
                    } else {
                        DB::table('banned_users')->insert([
                            'userid' => $user->userid,
                            'reason' => "Auto ban CC"
                        ]);
                    }
                    return Cache::remember('connect_ban_cc', 3600, function () use ($key) {
                        return json_encode(['code' => 401, 'status' => -2 , 'message' => "Auto ban CC.", 'info' => '']);
                    });
                }
                if (env('AUTO_BAN_MULTI') == 1) {
                    $toBan = DB::table('users')->where('device_id', $user->device_id)->get();
                    if (count($toBan) > 1) {
                        foreach ($toBan as $ban) {
                            DB::table('banned_users')->updateOrInsert(
                                ['userid' => $ban->userid],
                                ['reason' => 'Banned for creating multiple accounts.', 'device_id' => $ban->device_id]
                            );
                        }
                       
                    
                        return json_encode(['code' => 401, 'status' => -2 , 'message' => "Banned for creating multiple accounts.", 'info' => '']);
                    }
                }
                $status = 1;
				$misc .= 'redeem=' . DB::table('gate_category')->count() . '@_@';
            }
            $now = Carbon::now()->timestamp;
            $tour = Cache::remember('connect_tour', 300, function () {
                return [
                    'name' => str_replace('_', ' ', env('TOUR_NAME')),
                    'time' => (int) env('TOUR_BEGIN_TIME'),
                    'fee' => env('TOUR_ENTRY_FEE'),
                    'reward' => env('TOUR_REWARD'),
                    'pub' => env('TOUR_PUB_TIMESTAMP')
                ];
            });
            
            $res = [
             
                'ip' => $ip,
                'chat' => $chat,
                'currency' => $currency,
                'conversion' => $conversion,
                'root' => $root,
                'a_root' => $a_root,
                'vpn' => $vpn,
                'vpn_m' => $m_vpn,
                'interval' => $interval,
                'time' => $now,
                'version_code' => env('USER_VERSIONCODE'),
                'update_force' => env('USER_FORCE_UPDATE'),
                'html_reload' => env('HTML_GAME_REFRESH'),
                'misc' => $misc,
                'offers'=>$offers,
                'howmuch'=>$Howmuch,
                'googleplay' => $googleplay, 
                'enckey' => $enckey
                 
            ];
            
            /*
            if ($status == 1) {
                $e = DB::table('tour_player')->where('userid', $user->userid)->first();
                $res['enroll'] = $e ? 1 : 0;
            }
            */
            return json_encode(['code' => 201, 'status' => 1 , 'message' => 'Data fetched successfully!', 'info' => $res]);
           
            
        } catch (\Exception $e) {
            return Cache::remember('connect_general_err', 3600, function () use ($key) {
                return json_encode(['code' => 402, 'status' => 0 , 'message' => 'Unexpected error occurred!'.$e->getMessage(), 'info' => '']);
            });
        }
    }
}
