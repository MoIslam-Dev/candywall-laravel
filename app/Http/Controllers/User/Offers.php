<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cache;
use Funcs;
use DB;

class Offers extends Controller
{
    public function servePremium(Request $req)
    {
        $user = $req['user'];
        $cc = $req->get('cc') == null ? 'all' : $req->get('cc');
        $completed = DB::table('hist_activities')->where('userid', $user->userid)->where('is_custom', 1)->pluck('offerid');
        $offers = DB::table('offerwall_c')->where('country', 'LIKE', '%'.$cc.'%')->orWhere('country', 'all')
                    ->whereColumn('max', '>', 'completed')->get(['offer_id','type','title','description','points','image','url']);
        return ['status' => 1, 'completed' => str_replace('"', "'", $completed), 'offers' => $offers];
    }

    public function servePpv(Request $req)
    {
        $user = $req['user'];
        $cc = $req->get('cc') == null ? 'all' : $req->get('cc');
        $completed = [];
        $db = DB::table('hist_activities')
                ->where('userid', $user->userid)
                ->where('is_custom', 2)
                ->whereDate('created_at', Carbon::today())
                ->get();
        foreach($db as $d){
            array_push($completed, ['oid' => $d->offerid, 'can_visit' => $d->note]);
        }
        $oids = DB::table('hist_activities')
                ->where('userid', $user->userid)
                ->where('note', '-999')
                ->pluck('offerid')
                ->toArray();
        $offers =  DB::table('offers_ppv')
                        ->where(function ($q) use ($cc){
                            $q->where('country', 'LIKE', '%'.$cc.'%');
                            $q->orWhere('country', 'all');
                        })
                        ->whereNotIn('id', str_replace('ppv_','',$oids))
                        ->orderBy('id', 'desc')
                        ->get(['id','title','seconds','points','url','per_day']);
        return ['status' => 1, 'completed' => $completed, 'offers' => $offers];
    }

    public function serveYt(Request $req)
    {
        $user = $req['user'];
        $cc = $req->get('cc') == null ? 'all' : $req->get('cc');
        $completed = DB::table('hist_activities')->where('userid', $user->userid)->where('is_custom', 3)->pluck('offerid')->toArray();
        $offers =  DB::table('offers_yt')->where('country', 'LIKE', '%'.$cc.'%')->orWhere('country', 'all')->get(['id','code','title','points']);
        return ['status' => 1, 'completed' => $completed, 'offers' => $offers];
    }

    public function rewardYt(Request $req)
    {
        $user = $req['user'];
        try {
            $data = explode("|", Funcs::dec($req->json('d')));
            if ($data[3] != $user->userid) {
                return ['status' => 0, 'message' => 'Reward void!'];
            }
            $timestamp = \Carbon\Carbon::now()->timestamp;
            if (abs($timestamp - (int)$data[2]) > 10) {
                return ['status' => 0, 'message' => 'Verification failed!'];
            }
            $check = DB::table('offers_yt')->where('id', $data[0])->get();
            foreach ($check as $c) {
                if ($c->country == $data[1] || $c->country == 'all') {
                    if (DB::table('hist_activities')->where('offerid', $data[0])->where('userid', $user->userid)->exists()) {
                        return ['status' => 0, 'message' => 'Already you got rewarded for this video!'];
                    }
                    DB::table('hist_activities')->insert([
                        'userid' => $user->userid,
                        'network' => 'youtube',
                        'is_lead' => 0,
                        'is_custom' => 3,
                        'offerid' => $data[0],
                        'ip' => \Request::ip(),
                        'points' => $c->points
                    ]);
                    $user->increment('balance', $c->points);
                    if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                        Funcs::leaderboard($user->name, $user->userid, $c->points);
                    }
                    return ['status' => 1, 'message' => $c->points];
                }
            }
            return ['status' => 0, 'message' => 'Reward void! Video is not available!'];
        } catch (\Exception $e) {
            return ['status' => 0, 'message' => 'Invalid request'];
        }
    }

    public function completedCpa(Request $req)
    {
        $user = $req['user'];
        $completed = DB::table('hist_activities')->where('userid', $user->userid)->where('is_custom', 4)->pluck('offerid');
        return ['status' => 1, 'message' => str_replace('"', "'", $completed)];
    }

    public function vpnDetected(Request $req)
    {
        $user = $req['user'];
        $user->increment('vpn', 1);
        if (env('AUTO_BAN_VPN') == 1) {
            if ($user->device_id != 'none') {
                DB::table('banned_users')->insert([
                    'userid' => $user->userid,
                    'reason' => "Auto ban for security reason!",
                    'device_id' => $user->device_id
                ]);
            } else {
                DB::table('banned_users')->insert([
                    'userid' => $user->userid,
                    'reason' => "Auto ban for security reason!"
                ]);
            }
        }
    }

    public function playReward(Request $req){
    
        try {
            
            $user = $req['user'];
            $package = Funcs::dec($req->json('d'));
            $rData = DB::table('offerwall_c')->where('type', 1)->where('url', 'LIKE', '%'.$package.'%');
            $reward = $rData->first();
            
            if ($reward) {
                
                if ($reward->max <= $reward->completed) {
                    return ['status' => 0, 'message' => 'You are too late! Offer quantity maxed up.'];
                }
                
                $offer_id = $reward->offer_id;
                $check = DB::table('hist_activities')->where('offerid', $offer_id)->where('userid', $user->userid)->first();
                
                if ($check) {
                    return ['status' => 0, 'message' => 'Offer already completed!'];
                }
                
                $payout = $reward->points;
                DB::table('hist_activities')->insert([
                    'userid' => $user->userid,
                    'network' => "direct",
                    'is_lead' => 1,
                    'is_custom' => 1,
                    'offerid' => $reward->offer_id,
                    'ip' => \Request::ip(),
                    'points' => $payout
                ]);
                
                $user->increment('balance', $payout);
                
                if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                    
                    Funcs::leaderboard($user->name, $user->userid, $payout);
                }
                
                $rData->increment('completed', 1);
                
                if (env('EARNING_NOTIFICATION') == 1) {
                    
                    $cn = strtolower(env('CURRENCY_NAME'))."s";
                    DB::table('message')->insert([
                        'userid' => $user->userid,
                        'title' => 'Reward from premium offer',
                        'msg' => 'You just received +'.$payout.' '.$cn.' for completing "'.$reward->title.'"',
                    ]);
                    
                    if ($user->has_notification == 0) {
                        $user->increment('has_notification', 1);
                    }
                };
                return ['status' => 1, 'message' => 'Credited'];
                
            } else {
                
                return ['status' => 0, 'message' => 'Offer unavailable!'];
            }
            
        } catch (\Exception $e) {
            return ['status' => 0, 'message' => 'Unknown error occurred!'];
            //return ['status' => 0, 'message' => $e->getMessage()];
        }
    }
	
	public function rewardPpv(Request $req)
    {
        $user = $req['user'];
        $data = explode("|", Funcs::dec($req->json('d')));
        if ($data[4] != $user->userid) {
            return ['status' => 0, 'message' => 'Reward void!'];
        }
        $now = Carbon::now();
        $bTime = (int)$data[3];
        if (abs($now->timestamp - $bTime) > 10) {
            return ['status' => 0, 'message' => 'Verification failed!'];
        }
        $oid = (int) $data[1];
        $cc = $data[2];
        $check = DB::table('offers_ppv')
                    ->where('id', $oid)
                    ->where(function($query) use ($cc){
                        $query->where('country', $cc)
                       ->orWhere('country', 'all');
                    })
                    ->first();
        if (!$check) {
            return ['status' => 0, 'message' => 'Offer not found!'];
        }
        if ($now->timestamp - (int) $data[0] < $check->seconds) {
            return ['status' => 0, 'message' => 'System cannot reward for now!'];
        }
        $com_db = DB::table('hist_activities')->where('userid', $user->userid)->where('offerid', 'ppv_'.$oid);
        $completed = $com_db->first();
        $sr = false;
        if ($completed){
            $note = (int) $completed->note;
            $isToday = Carbon::parse($completed->created_at)->isToday();
            if ($check->per_day == 0){
                $sr = false;
            } elseif ($isToday && $note > 0){
                $com_db->update([
                    'points' => $check->points + $completed->points,
                    'ip' => \Request::ip(),
                    'note' => (int) $completed->note - 1
                ]);
                $sr = true;
            } elseif (!$isToday) {
                $com_db->update([
                    'points' => $check->points + $completed->points,
                    'ip' => \Request::ip(),
                    'note' => $check->per_day - 1,
                    'created_at' => $now
                ]);
                $sr = true;
            } else {
                $sr = false;
            }
        } else {
            DB::table('hist_activities')->insert([
                'userid' => $user->userid,
                'network' => 'ppv',
                'is_lead' => 0,
                'is_custom' => 2,
                'offerid' => 'ppv_'.$oid,
                'ip' => \Request::ip(),
                'points' => $check->points,
                'created_at' => $now,
                'note' => $check->per_day == 0 ? -999 : $check->per_day - 1
            ]);
            $sr = true;
        }
        if ($sr){
            $user->increment('balance', $check->points);
            return ['status' => 1, 'message' => "You received ".$check->points." ".strtolower(env('CURRENCY_NAME'))."s"];
        } else {
            return ['status' => 0, 'message' => 'You have already completed this offer!'];
        }
        
        
    }
    
    
    public function liveoffers(Request $req) {
        
       $tok = $req['tok'];
       $user = $req['user'];
    
    
    
        if ($tok != null && $tok !== 'none') {
            $db = DB::table('hist_activities')->where('userid', $user->userid);
            
            $res = array();
          
            $recordCount = $db->count();
            
            if ($recordCount > 0) {
                $res['status'] = 1;
                $res['message'] = 'ok';
                $res['data'] = $db->get(['userid', 'network', 'offerid', 'points', 'created_at']);
            } else {
                $res['status'] = 0;
                $res['message'] = 'empty';
                $res['data'] = null;
            }
            
           return $res; 
            
            
        }
    
    }

    
}