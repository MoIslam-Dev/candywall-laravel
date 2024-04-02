<?php
namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cache;
use Funcs;
use DB;
use App\User;

class Userinfo extends Controller{

    public function info(Request $req){
    
      $user = $req['user'];
      return ['status' => 1, 'name' => $user->name, 'avatar' => $user->avatar, 'bal' => $user->balance];
      
    }
    
    
    public function globalMsg(Request $req) {
        
    if ($req->has('mid')) {
        
        $mid = $req->get('mid');
        $gmid = Cache::get('gmid', '1234567');
        
        if ($gmid == $mid) {
            
            $gMsg = ['title' => '', 'desc' => '', 'mid' => $gmid];
            
        } else {
            
            $gMsg = Cache::rememberForever('global_msg', function () use ($gmid) {
                $gmCheck = DB::table('misc')->where('name', 'global_msg')->first();
                if ($gmCheck) {
                    $dta = unserialize($gmCheck->data);
                    Cache::put('gmid', $dta['mid']);
                } else {
                    $dta = ['title' => '', 'desc' => '', 'mid' => $gmid];
                }
                return $dta;
            });
        }
        
    } else {
        $gMsg = ['title' => '', 'desc' => '', 'mid' => ''];
    }

    
    return response()->json($gMsg);
}


    public function fid(Request $req)
    {
    
        $user = $req['user'];
        Funcs::updateFCT($user->userid, $user->email, $req->get('f'));
        return ['status' => 1, 'message' => 'updated'];
    }

    public function balance(Request $req) {
   
        $user = $req['user'];
        $type = $req['type'];
        
        $apf = $user->name;
        $rr = Funcs::getmisc('apf_reward');
        
        if($user->apf != 1 && $user->balance >= (int) $rr){
            $apf = $apf.'@_@'.$rr;
        }
        
        $res = array('status' => 1,'b' => $user->balance,'u' => $apf);
        
        
        if ($type == "1" && $user->has_notification > 0) {
            $user->decrement('has_notification', $user->has_notification);
            $msg = DB::table('message')->where('userid', $user->userid);
			$msgCount = DB::table('message')->where('userid', $user->userid)->count();
            $res['n'] = $msg->get(['title','msg','date']);
			$res['c'] = $msgCount;
            $msg->delete();
        }
        return $res;
    }
    
    

    public function profile(Request $req){
    
        $user = $req['user'];
        
        if ($user->refby == null || $user->refby == 'none') {
            $inv = '-none-';
            
        } else {
            
            $chk = DB::table('users')->where('userid', $user->refby)->first();
            $inv = $chk ? $chk->name : '-none-';
        }
        
        return [
            
            'status' => 1,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'email' => $user->email,
            'userid' => $user->userid,
            'inv' => $inv,
            'cc' => $user->country,
            'balance' => $user->balance
           
        ];
    }
    
    public function passChange(Request $req){
        
       $user = $req['user'];
       $password = $req['password'];

      $passes = explode('||', $password);

            if (count($passes) == 2) {
                if (Hash::check($passes[0], $user->password)) {
                    DB::table('users')->where('userid', $user->userid)->update(['password' => bcrypt($passes[1])]);
        
                    return ['status' => 1, 'message' => 'Password changed successfully.'];
                } else {
                    return ['status' => 0, 'message' => 'You have entered an invalid current password!'];
                }
            }
    }
    
    public function refCheck(Request $req){
        
        $user = $req['user'];
        $data = $req['data'];
        
       
            if (strlen($data) != 13) {
                return ['status' => 0, 'message' => 'Invalid referral code!'];
                
            }else{
                
                return Funcs::addref($user, $data);
            }
        
        
    }
    
    public function changeName(Request $req){
        
        $user = $req['user'];
        $data = $req['data'];
        
            if (strlen($data) < 50) {
                DB::table('users')->where('userid', $user->userid)->update(['name' => $data]);
                return ['status' => 1, 'message' => 'update successfull.','name' => $data];
            } else {
                return ['status' => 0, 'message' => 'Text length is too long!','name' => $data];
            }
        
        
    }
    
    
    public function avatarChange(Request $req)
    {
        $user = $req['user'];
       
        try {
            $filename = basename($user->avatar);
            $path = public_path('uploads');
            if ($filename != null && file_exists($path.'/'.$filename)) {
                unlink($path.'/'.$filename);
            };
            $filename = Carbon::now()->timestamp.'.'.$req->file('image')->getClientOriginalExtension();
            $req->file('image')->move($path, $filename);
            $avatar = env('APP_URL').'/public/uploads/'.$filename;
            DB::table('users')->where('id', $user->id)->update(['avatar' => $avatar]);
            return ['status' => 1,'message' => $avatar];
        } catch (\Exception $e) {
            return ['status' => 0,'message' => $e->getMessage()];
        }
    }
	
	public function avatarChange2(Request $req)
    {
       
       
    try{
    $user_id = $_POST['user'];
    $description = $_POST['description']; // get the description parameter from the form data

    $target_dir =  '/home/morijlqx/morumoney.com/public/uploads/';
    $target_file = $target_dir . basename($_FILES["image"]["name"]); // get the name of the uploaded file
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
           
            $avatar = htmlspecialchars( basename( $_FILES["image"]["name"]));
             DB::table('users')->where('id', $user_id)->update(['avatar' => $avatar]);
            return ['status' => 1,'message' => $avatar];
            
        } else {
            return ['status' => 2,'message' => 'Sorry, there was an error uploading your file.'];
           
        }
    }
    
    } catch (\Exception $e) {
            return ['status' => 2, 'message' => $e->getMessage()];
        }
         
    
}
    
       
    
	
    public function profileChange(Request $req)
    {
        $user = $req['user'];
        $type = $req->json('type');
        $data = $req->json('data');
        
        if ($type == '1') {
            if (strlen($data) < 300) {
                DB::table('users')->where('userid', $user->userid)->update(['avatar' => $data]);
                return ['status' => 1, 'message' => 'update successfull.'];
            } else {
                return ['status' => 0, 'message' => 'URL length is too long!'];
            }
            
        } elseif ($type == '2') {
            if (strlen($data) < 50) {
                DB::table('users')->where('userid', $user->userid)->update(['name' => $data]);
                return ['status' => 1, 'message' => 'update successfull.'];
            } else {
                return ['status' => 0, 'message' => 'Text length is too long!'];
            }
        } elseif ($type == '3') {
            if (strlen($data) != 13) {
                return ['status' => 0, 'message' => 'Invalid referral code!'];
            }
        
            return Funcs::addref($user, $data);
            
        } elseif ($type == '4') {
            
            $passes = explode('||', $data);
            
            if (count($passes) == 2) {
                
                if (Hash::check($passes[0], $user->password)) {
                    DB::table('users')->where('id', $user->id)->update(['password' => bcrypt($passes[1])]);
                    return ['status' => 1, 'message' => 'Password changed successfully.'];
                } else {
                    return ['status' => -1, 'message' => 'You have entered an invalid current password!'];
                }
            } else {
                return ['status' => -1, 'message' => 'New password is invalid'];
            }
        }
    }

    public function arGet(Request $req){
    
        $user = $req['user'];
        $reward = DB::table('activity_reward')->where('active', 1)->orderBy('id', "asc")->get(['id','name','max']);
        $done = 0;
        $isDone = 0;
        
        if ($user->done_ar != null) {
            
            $ar = explode('||', $user->done_ar);
            
            if (Carbon::parse($ar[1])->isToday()) {
                
                $done = $ar[0]-1;
                $isDone = 1;
                
            } else {
                
                $done = $ar[0];
            }
        }
        return ['status' => 1, 'rewards' => $reward, 'done' => $done, 'is_done' => $isDone];
    }

     public function arReward(Request $req){
    
        $user = $req['user'];
        $id = (int) $req->get('id');
        $done_ar = 0;
        $date = null;
        
        if ($user->done_ar != null) {
            $aa = explode('||', $user->done_ar);
            $done_ar = (int) $aa[0];
            $date = $aa[1];
        }
        
         if ($done_ar + 1 != $id) {
            return ['status' => 0, 'message' => 'You cannot open this reward vault!'];
        }
        
        $reward = DB::table('activity_reward')->where('active', 1)->where('id', $id+1)->first();
        
        if ($reward) {
            
            if ($date != null && Carbon::parse($date)->isToday()) {
                
                return ['status' => 0, 'message' => 'You already opened a vault. Come back tomorrow.'];
                
            }
            
            $amt = rand($reward->min, $reward->max);
            
            DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'ActivityReward'], ['points' => DB::raw("points + '$amt'")]);
            
         
                DB::table('users')->where('id', $user->id)->update([
                    'balance' => $user->balance + $amt,
                    'done_ar' => $id . '||' . Carbon::now()
                ]);
                

            return ['status' => 1, 'message' => 'You got rewarded', 'amount' => $amt];
            
        } else {
            
            return ['status' => 0, 'message' => 'Vault not found!'];
        }
    }

    public function autobanRoot(Request $req)
    {
        if (env('AUTO_BAN_ROOT') == 1) {
            $user = $req['user'];
            DB::table('banned_users')->updateOrInsert(
                ['userid' => $user->userid],
                ['reason' => 'Banned for using rooted device.', 'device_id' => $user->device_id]
            );
        }
    }

    public function vpnMonitor(Request $req)
    {
        if (env('AUTO_BAN_VPN') == 1) {
            $user = $req['user'];
            DB::table('banned_users')->updateOrInsert(
                ['userid' => $user->userid],
                ['reason' => 'Banned for using VPN.', 'device_id' => $user->device_id]
            );
        } elseif (env('VPN_MONITOR') == 1) {
            $user = $req['user'];
            $check = DB::table('vpn_monitor')->where('userid', $user->userid);
            if ($check->first()) {
                $check->increment('attempted', 1);
            } else {
                DB::table('vpn_monitor')->insert([
                    'userid' => $user->userid,
                    'name' => $user->name,
                    'avatar' => $user->avatar == null ? 'none' : $user->avatar,
                    'attempted' => 1
                ]);
            }
        }
    }

    public function refView()
    {
        return ['status' => 1, 'ref' => env('REF_LINK_REWARD'), 'user' => env('REF_USER_REWARD')];
    }

    public function refHistory(Request $req){
    
     try {
        
        $user = $req['user'];
        $db = DB::table('hist_activities')
                    ->where('userid', $user->userid)
                    ->where('network', 'referral')
                    ->orderBy('id', 'desc')
                    ->get();
                    
        $data = [];
        
        foreach ($db as $d) {
            
            $uid = str_replace('For referring: ', '', $d->note);
            $check = DB::table('users')->where('userid', $uid)->first();
            
            if ($check) {
                array_push($data, [
                    'image' => $check->avatar,
                    'name' => $check->name,
                    'date' => Carbon::parse($d->created_at)->timestamp,
                    'points' => $d->points
                ]);
                
            }
            
        }
        
        return ['status' => 1, 'message' => $data];
        
     } catch (\Exception $e) {
            return ['status' => 0, 'filed' => $e->getMessage()];
        }
    
    }
    
     public function withdrawalfHistory(Request $req){
    
     try {
        $user = $req['user']->userid;
        $db = DB::table('gate_request')
                    ->where('userid', $user)
                    ->orderBy('id', 'desc')
                    ->get();
         $data = [];
        foreach ($db as $d) {
             
               array_push($data, [
                    'g_name' => $d->g_name,
                    'points' => $d->points,
                    'to_acc' => $d->to_acc,
                    'country' => $d->country,
                    'is_completed' => $d->is_completed,
                    'date' => Carbon::parse($d->created_at)->timestamp
                    
                 ]);
            
        }
        return ['status' => 1, 'message' => $data];
        
          } catch (\Exception $e) {
            return ['status' => 1, 'message' => $e->getMessage()];
        }
        
        
    
    }
    
	
	public function delAcc(Request $req)
    {
        $u = $req['user']->userid;
        DB::table('banned_users')->where('userid', $u)->delete();
        DB::table('gate_request')->where('userid', $u)->delete();

        DB::table('hist_activities')->where('userid', $u)->delete();
        DB::table('hist_game')->where('userid', $u)->delete();
       
        DB::table('message')->where('userid', $u)->delete();
        DB::table('notif_id')->where('userid', $u)->delete();
       
        DB::table('support')->where('userid', $u)->delete();
     
        DB::table('vpn_monitor')->where('userid', $u)->delete();
        DB::table('wheel_player')->where('userid', $u)->delete();
        DB::table('users')->where('userid', $u)->delete();
        
		if ($req['user']->refby != null && strlen($req['user']->refby) == 13) {
            $ref_db = DB::table('users')->where('userid', $u);
            if ($ref_db->first()) {
                $ref_db->decrement('balance', (int) env('REF_LINK_REWARD'));
                DB::table('hist_activities')->where('note', 'For referring: '.$u)->delete();
            }
        }
        return ['status' => 1, 'message' => "User deleted."];
    }
    
    
      public function apf(Request $req){
    
        $user = $req['user'];
        DB::table('users')->where('userid', $user->userid)->update(['apf' => 1]);
        return ['status' => 1, 'message' => 'Appsflyer reward'];
    }

    public function devKey(){
    
        return ['status' => 1, 'message' => Funcs::getmisc('dev_key')];
    }
    
   
}
