<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cache;
use DB;

class Misc extends Controller
{
    public function ranking(Request $req)
    {
        if (Cache::has('ranking')) {
            $res = Cache::get('ranking');
        } else {
            $userid = $req['user']->userid;
            $yesterday = Carbon::yesterday()->format('d-m');
            $check = DB::table('misc')->where('name', 'leaderboard')->first();
            $res = array();
            if ($check && $yesterday == $check->data) {
                $db = DB::table('leaderboard')
                        ->orderby('reward', 'desc')
                        ->limit(env('LEADERBOARD_LIMIT'))
                        ->get();
				for ($j = 0; $j < count($db); $j++) {
                    $y = $db[$j]->userid == $userid ? "y" : "n";
					$usr = DB::table('users')->where('userid', $db[$j]->userid)->first();
					if($usr){
                        $avatar = $usr->avatar;
                        if ($avatar == null || $avatar == '') {
                            $avatar = 'none';
                        }
                        array_push($res, ['p' => $j, 'y' => $y, 'a' => $avatar, 'n' => $db[$j]->name, 's' => $db[$j]->score_prv, 'r' => $db[$j]->reward]);
                    } else {
                        array_push($res, ['p' => $j, 'y' => $y, 'a' => 'none', 'n' => 'deleted', 's' => 0, 'r' => 0]);
                    }
                }
            } else {
                $today = Carbon::now()->format('d-m');
                $lb = DB::table('leaderboard')->get();
                foreach ($lb as $l) {
                    $dc = $l->date_cur;
                    if ($dc != $today) {
                        if ($dc != $yesterday) {
                            $dp = $l->date_prv;
                            if ($dp != $today) {
                                if ($dp != $yesterday) {
                                    DB::table('leaderboard')->where('id', $l->id)->delete();
                                }
                            }
                        }
                    }
                }
                $aa = DB::table('leaderboard')->where('date_cur', $yesterday)->get();
                foreach ($aa as $a) {
                    $dc = $a->date_cur;
                    $sc = $a->score_cur;
                    DB::table('leaderboard')->where('id', $a->id)->update([
                        'date_cur' => null,
                        'score_cur' => 0,
                        'date_prv' => $dc,
                        'score_prv' => $sc,
                        'rank' => 0,
                        'date_rank' => null,
                        'reward' => 0
                    ]);
                }
                if ($check) {
                    DB::table('misc')->where('name', 'leaderboard')->update(['data' => $yesterday]);
                } else {
                    DB::table('misc')->insert(['name' => 'leaderboard','data' => $yesterday]);
                }
                DB::table('leaderboard')->update(['reward' => 0]);
                $db = DB::table('leaderboard')
                        ->where('date_prv', $yesterday)
                        ->orderby('score_prv', 'DESC')
                        ->limit(env('LEADERBOARD_LIMIT'))
                        ->get();
                $pct = explode(',', env('LEADERBOARD_PCT'));
                $totalAmt = (int) env('LEADERBOARD_REWARD');
                for ($i = 0; $i < min(count($pct), count($db)); $i++) {
                    $uid = $db[$i]->userid;
                    $amt = round($totalAmt * (int) $pct[$i] / 100);
                    DB::table('leaderboard')->where('userid', $uid)->update(['reward' => $amt]);
                    $u = DB::table('users')->where('userid', $uid);
                    $user = $u->first();
					if($user){
						$u->update(['balance' => $user->balance + $amt]);
						DB::table('hist_activities')->insert([
							'userid' => $user->userid,
							'network' => 'ranking',
							'note' => 'Ranked '.($i + 1).' on '.$yesterday,
							'points' => $amt
						]);
						$y = $user->userid == $userid ? "y" : "n";
						$avatar = $user->avatar;
						if ($avatar == null || $avatar == '') {
							$avatar = 'none';
						}
						array_push($res, ['p' => $i, 'y' => $y, 'a' => $avatar, 'n' => $user->name, 's' => $db[$i]->score_prv, 'r' => $amt]);
					} else {
						array_push($res, ['p' => $i, 'y' => 'n', 'a' => 'none', 'n' => 'deleted', 's' => 0, 'r' => 0]);
					}
                }
            }
            
			Cache::put('ranking', $res, Carbon::now()->endOfDay()->timestamp - Carbon::now()->timestamp + 60);
        }
        
        return['status' => 1, 'rank' => $res];
    }

    public function faq()
    {
        return ['status' => 1, 'faq' => DB::table('support_faq')->get(['question','answer'])];
    }

    public function tos()
    {
        try {
            $p = file_get_contents(resource_path('views')."/privacy.blade.php");
            $privacy = str_replace(["@extends('privacy_inc') @section('privacy')\r\n","\r\n@endsection"], '', $p);
            $t = file_get_contents(resource_path('views')."/terms.blade.php");
            $terms = str_replace(["@extends('terms_inc') @section('terms')\r\n","\r\n@endsection"], '', $t);
        } catch (\Exception $e) {
            $privacy = 'Could not load the file. Make sure "resources\views\privacy.blade.php" got full read-write permission (0777)';
            $terms = 'Could not load the file. Make sure "resources\views\terms.blade.php" got full read-write permission (0777)';
        }
        return ['status' => 1, 'term' => $terms, 'privacy' => $privacy];
    }
}
