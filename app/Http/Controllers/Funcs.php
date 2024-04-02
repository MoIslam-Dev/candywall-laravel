<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\User;
use Cache;
use DB;

class Funcs
{
    public static function sendFCM($tokens, $title, $message, $data = null, $all = false)
    {
        try {
            $fields = [
                'priority' => 'high',
                "android" => [
                    "ttl" => env('FCM_TTL_SEC')."s"
                ]
            ];
            if ($all) {
                $fields['to']  = '/topics/misc';
            } else {
                $toks = is_array($tokens) ? $tokens : array($tokens);
                $reset = false;
                for ($i = 0; $i < count($toks); $i++) {
                    if ($toks[$i] == 'none') {
                        $reset = true;
                        unset($toks[$i]);
                    }
                }
                if ($reset) {
                    $toks = array_values($toks);
                }
                if (count($toks) == 0) {
                    return ['status' => 0, 'result' => 'No user to send message'];
                }
                $fields['registration_ids']  = $toks;
            }
            if ($data != null) {
                $fields['data'] = $data;
            }
            $fields['data']['title'] = $title;
            $fields['data']['desc'] = $message;
            $headers = array('Authorization: key=' . env('FCM_SERVER_KEY'), 'Content-Type: application/json');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
            return ['status' => 1, 'result' => $result];
        } catch (\Exception $e) {
            return ['status' => 0, 'result' => $e->getMessage()];
        }
    }
    
        protected static function saltKey()
    {
        $count = 0;
        $sKey = env('ENC_KEY');
        for ($i = 0; $i < strlen($sKey); $i++) {
            $count += ord($sKey[$i]);
        }
        return $count;
    }

 
    public static function encKey(){
    
        return Cache::remember('connect_enc_key', 86400, function () {
            $data = '';
            $padding = 0;
            $salt = 0;
            $sKey = 'string_str';
            for ($i = 0; $i < strlen($sKey); $i++) {
                $salt += ord($sKey[$i]);
            }
            $str = env('ENC_KEY');
            for ($i = 0; $i < strlen($str); $i++) {
                $data .= ord($str[$i]) + $salt + $padding . 'x';
                $padding += 1;
            }
            return substr($data, 0, -1);
        });
    }
    
        public static function enc($str)
    {
        $data = '';
        $padding = 0;
        $salt = Funcs::saltKey();
        for ($i = 0; $i < strlen($str); $i++) {
            $data .= ord($str[$i]) + $salt + $padding . 'x';
            $padding += 1;
        }
        return substr($data, 0, -1);
    }
    
    public static function dec($str)
    {
        $data = '';
        $salt = Funcs::saltKey();
        $arrayData = explode("x", $str);
        for ($i = 0; $i < sizeof($arrayData); $i++) {
            $data .= chr((int)$arrayData[$i] - $salt - $i);
        }
        return $data;
    }
  

    public static function getCountry($code)
    {
        try {
            return DB::table('online_users')->where('country_iso', $code)->first()->country_name;
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    public static function countryExist($code)
    {
        return DB::table('online_users')->where('country_iso', $code)->first();
    }

    public static function isBanned($user, $did)
    {
        if ($user === null) {
            return DB::table('banned_users')->where('device_id', $did)->first();
        } else {
            return DB::table('banned_users')
                ->where('userid', $user->userid)
                ->orWhere('device_id', $user->device_id)
                ->orWhere('device_id', $did)
                ->first();
        }
    }

    public static function userinfo($req){
 
    
    
        $token = $req->header('Authorization');
        if ($token == null || $token == '') {
            return false;
        }
        
        $u = explode('|', $token);
        if (count($u) != 2) {
            return false;
        }
        return User::where('id', $u[0])->where('remember_token', $token)->first();
    }

    public static function addOnline($cc)
    {
        DB::table('online_users')->where('country_iso', strtoupper($cc))->increment('visitors', 1);
    }

    public static function setEnv($key, $val, $clearCache = true)
    {
        file_put_contents(\App::environmentFilePath(), str_replace(
            $key . '=' . env($key),
            $key . '=' . $val,
            file_get_contents(\App::environmentFilePath())
        ));
        if ($clearCache && file_exists(\App::getCachedConfigPath())) {
            \Artisan::call("config:cache");
        };
    }

    public static function leaderboard($name, $userid, $amount)
    {
        $db = DB::table('leaderboard')->where('userid', $userid);
        $check = $db->first();
        $now = Carbon::now()->format('d-m');
        if ($check) {
            if ($check->date_cur == $now) {
                $db->increment('score_cur', $amount);
            } else {
                $yesterday = Carbon::yesterday()->format('d-m');
                if ($check->date_cur == $yesterday) {
                    $sC = $check->score_cur;
                    $dC = $check->date_cur;
                    $db->update([
                        'name' => $name,
                        'score_prv' => $sC,
                        'date_prv' => $dC,
                        'score_cur' => $amount,
                        'date_cur' => $now
                    ]);
                } else {
                    $db->update([
                        'name' => $name,
                        'score_prv' => 0,
                        'date_prv' => null,
                        'score_cur' => $amount,
                        'date_cur' => $now,
                        'rank' => 0,
                        'reward' => 0
                    ]);
                }
            }
        } else {
            DB::table('leaderboard')->insert([
                'name' => $name,
                'userid' => $userid,
                'score_cur' => $amount,
                'date_cur' => $now
            ]);
        }
    }

    public static function rank($uid)
    {
        $cS = DB::table('leaderboard')->where('userid', $uid);
        $check = $cS->first();
        $score = 0;
        $rank = 0;
        if ($check && Carbon::now()->format('d-m') == $check->date_cur) {
            $score = $check->score_cur;
            if ($check->date_rank == null || Carbon::parse($check->date_rank)->isCurrentHour()) {
                $rank =  DB::table('leaderboard')->where('score_cur', '>=', $score)->count();
                $cS->update(['rank' => $rank, 'date_rank' => Carbon::now()]);
            } else {
                $rank = $check->rank;
            }
        }
        return ['score' => $score, 'rank' => $rank];
    }

    public static function addref($user, $ref)
    {
        if ($user->refby == null || $user->refby == 'none') {
            $ref = strtoupper($ref);
            $refby = DB::table('users')->where('userid', $ref)->first();
            if (!$refby) {
                return ['status' => 0, 'message' => 'Referrer does not exist!'];
            }
            if ($user->userid == $ref) {
                return ['status' => 0, 'message' => 'You cannot refer yourself!'];
            }
            if ($refby->refby == $ref) {
                return ['status' => 0, 'message' => 'That user is already referred by you!'];
            }
			if ($refby->refby == $user->userid) {
                return ['status' => 0, 'message' => 'You referred that user, exchanging not allowed!'];
            }
            $amount = (int) env('REF_USER_REWARD');
            if ($amount != 0) {
                DB::table('users')->where('userid', $user->userid)->update([
                        'refby' => $ref,
                        'balance' => $user->balance + $amount
                    ]);
                DB::table('hist_activities')->insert([
                        'userid' => $user->userid,
                        'network' => 'reffered',
                        'is_lead' => 0,
                        'is_custom' => 0,
                        'offerid' => '-',
                        'points' => $amount,
                        'note' => 'Referred by: '.$refby->userid,
                        'created_at' => Carbon::now()
                    ]);
            }
            $amount = (int) env('REF_LINK_REWARD');
            if ($amount != 0) {
                DB::table('users')->where('userid', $refby->userid)->update([
                        'balance' => $refby->balance + $amount
                    ]);
                DB::table('hist_activities')->insert([
                        'userid' => $refby->userid,
                        'network' => 'referral',
                        'is_lead' => 0,
                        'is_custom' => 0,
                        'offerid' => '-',
                        'points' => $amount,
                        'note' => 'For referring: '.$user->userid,
                        'created_at' => Carbon::now()
                    ]);
            }
            return ['status' => 1, 'message' => $refby->name];
        } else {
            return ['status' => 0, 'message' => 'You are laready referred by someone.'];
        }
    }

   public static function updateFCT($uid, $email, $tok)
    {
        if ($tok != null && $tok != 'none') {
            
            $db = DB::table('notif_id')->where('userid', $uid);
            $check = $db->first();
            
            if ($check) {
                if ($check->sender_id != $tok) {
                    $db->update(['email' => $email, 'sender_id' => $tok]);
                   
                }
                
            } else {
                DB::table('notif_id')->insert(['userid' => $uid, 'email' => $email, 'sender_id' => $tok]);
                
            }
        }
        
        
    }
    
 

    public static function getmisc($req)
    {
        $exist = DB::table('misc')->where('name', $req)->first();
        if ($exist) {
            return $exist->data;
        }
        return '';
    }

    public static function setmisc($object, $value)
    {
        $exist = DB::table('misc')->where('name', $object);
        if ($exist->first()) {
            return $exist->update(['data' => $value]);
        } else {
            DB::table('misc')->insert(['name' => $object, 'data' => $value]);
        }
    }

    public static function makeLottoResult()
    {
        //$dt = date("d-m-Y");
        $dt = Carbon::now()->format('d-m-Y');
        $sdt = self::getmisc('lotto_draw_date');
        if ($dt != $sdt) {
            $winner = DB::table('lotto_player')->where('lotto_won', 1);
            $winnerData = $winner->first();
            $column = 'none';
            if ($winnerData) {
                //$yesterday = date("d-m-Y", strtotime('-1 days'));
                $yesterday = Carbon::yesterday()->format('d-m-Y');
                if ($winnerData->lotto_date_1 == $yesterday) {
                    $column = 'lotto_data_1';
                } elseif ($winnerData->lotto_date_2 == $yesterday) {
                    $column = 'lotto_data_2';
                }
            }
            if ($winnerData && $column != 'none') {
                $entNum = explode(',', $winnerData->$column);
                if (count($entNum) > 0) {
                    $selected = $entNum[array_rand($entNum)];
                    $winner->update(['lotto_won' =>  0]);
                    self::setmisc("lotto_winner", $selected);
                }
            } else {
                $data = array();
                //$date = date("d-m-Y", strtotime('-1 days'));
                $date = Carbon::yesterday()->format('d-m-Y');
                $userNumbers1 = DB::table('lotto_player')->whereNotNull('lotto_data_1')->where('lotto_date_1', $date)->get();
                foreach ($userNumbers1 as $un1) {
                    $nums1 = explode(',', $un1->lotto_data_1);
                    foreach ($nums1 as $n1) {
                        array_push($data, $n1);
                    }
                }
                $userNumbers2 = DB::table('lotto_player')->whereNotNull('lotto_data_2')->where('lotto_date_2', $date)->get();
                foreach ($userNumbers2 as $un2) {
                    $nums2 = explode(',', $un2->lotto_data_2);
                    foreach ($nums2 as $n2) {
                        array_push($data, $n2);
                    }
                }
                $prefix = '';
                $suffix = '';
                for ($i = 0; $i < 2; $i++) {
                    $prefix .= mt_rand(11, 46);
                }
                do {
                    for ($i = 0; $i < 3; $i++) {
                        $suffix .= mt_rand(11, 46);
                    }
                } while (in_array($suffix, $data));
                $finalData =  $prefix .= $suffix;
                self::setmisc('lotto_winner', $finalData);
            }
            self::setmisc('lotto_draw_date', $dt);
        }
    }

    public static function addCard($userid, $catId, $quantity)
    {
        $cat = DB::table('scratcher_game')->where('id', $catId)->first();
        if ($cat) {
            $exp = Carbon::now()->addDays($cat->days);
            $id = 0;
            if ($quantity == 1) {
                $id = DB::table('scratcher_player')->insertGetId([
                    'userid' => $userid,
                    'card_id' => $catId,
                    'created_at' => Carbon::now(),
                    'expiry' => $exp
                ]);
            } else {
                for ($i = 0; $i < $quantity; $i++) {
                    DB::table('scratcher_player')->insert([
                        'userid' => $userid,
                        'card_id' => $catId,
                        'created_at' => Carbon::now(),
                        'expiry' => $exp
                    ]);
                }
            }
            $db = DB::table('scratcher_limit')->where('userid', $userid);
            $check = $db->first();
            if($check){
                if(Carbon::parse($check->created_at)->isToday()){
                    $db->increment('quantity', $quantity);
                } else {
                    $db->update([
                        'quantity' => $quantity,
                        'created_at' => Carbon::now()
                    ]);
                }
            } else {
                DB::table('scratcher_limit')->insert([
                    'userid' => $userid,
                    'quantity' => $quantity,
                    'created_at' => Carbon::now()
                ]);
            }
            if($id != 0){
                return $id;
            }
        }
    }
	
	public static function deductGamePoints($user, $gameName, $pts, $deduct_bal = true)
    {
        if ($deduct_bal) {
            $user->decrement('balance', $pts);
            $userids = $user->userid;
        } else {
            $userids = $user;
        }
        $dbs = DB::table('hist_game')->where('userid', $userids)->where('game', $gameName);
        $chk = $dbs->first();
        if ($chk) {
            $dbs->increment('deducted', $pts);
        } else {
            DB::table('hist_game')->insert([
                'userid' => $userids,
                'game' => $gameName,
                'points' => 0,
                'deducted' => $pts
            ]);
        }
    }
	
	public static function delUser($id, $did)
    {
        if (env('PRV_ACC_DEL') == '1') {
            $db = DB::table('users')
                    ->where('device_id', $did)
                    ->where('id', '!=', env('ADMIN'))
                    ->where('id', '!=', $id)
                    ->get();
            foreach ($db as $d) {
                $u = $d->userid;
                DB::table('banned_users')->where('userid', $u)->delete();
                DB::table('gate_request')->where('userid', $u)->delete();
                DB::table('guess_word_player')->where('userid', $u)->delete();
                DB::table('hist_activities')->where('userid', $u)->delete();
                DB::table('hist_game')->where('userid', $u)->delete();
                DB::table('ip_player')->where('userid', $u)->delete();
                DB::table('jpz_player')->where('userid', $u)->delete();
                //DB::table('leaderboard')->where('userid', $u)->delete();
                DB::table('lotto_player')->where('userid', $u)->delete();
                DB::table('message')->where('userid', $u)->delete();
                DB::table('notif_id')->where('userid', $u)->delete();
                DB::table('quiz_player')->where('userid', $u)->delete();
                DB::table('scratcher_player')->where('userid', $u)->delete();
                DB::table('slot_player')->where('userid', $u)->delete();
                DB::table('support')->where('userid', $u)->delete();
                DB::table('tour_player')->where('userid', $u)->delete();
                DB::table('vpn_monitor')->where('userid', $u)->delete();
                DB::table('wheel_player')->where('userid', $u)->delete();
                DB::table('users')->where('userid', $u)->delete();
				if ($d->refby != null && strlen($d->refby) == 13) {
                    $ref_db = DB::table('users')->where('userid', $u);
                    if ($ref_db->first()) {
                        $ref_db->decrement('balance', (int) env('REF_LINK_REWARD'));
                        DB::table('hist_activities')->where('note', 'For referring: '.$u)->delete();
                    }
                }
            }
        }
    }
	
	public static function isRegBlocked($ref_id)
    {
        if (env('REG_DISABLED') == 1) {
            return 'Registration disabled!';
        } elseif (env('REG_INVITATION_ONLY') == 1) {
            if ($ref_id == null || $ref_id == 'none') {
                return 'You need a referral code to signup!';
            }
            if (!DB::table('users')->where('userid', $ref_id)->first()) {
                return 'Invalid referral code!';
            }
            if (DB::table('banned_users')->where('userid', $ref_id)->first()) {
                return 'Your referrer account was suspended!';
            }
        } elseif (env('REG_LIMIT_PER_HR') > 0) {
            $count = DB::table('users')->where('created_at', '>', Carbon::now()->subHour())->count();
            if ($count >= env('REG_LIMIT_PER_HR')) {
                return 'Registration limit exceeded in this hour. Please try again later.';
            }
        }
        return false;
    }
}
