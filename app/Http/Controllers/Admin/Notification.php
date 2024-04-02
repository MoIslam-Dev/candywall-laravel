<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Cache;
use DB;

class Notification extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        if ($req->has('uid') && $req->method() == 'POST') {
            $data = ['direct' => true, 'uid' => $req->post('uid')];
            return view('admin.notification', compact('data'));
        } else {
            return view('admin.notification');
        }
    }

    
    public function sendPush(Request $req)
    {
        $this->validate($req, [
            
            'sendtype' => 'required|integer|between:1,5',
            'email_or_userid' => 'required_if:sendtype,1|nullable|string|max:40',
            'email_or_userid_type' => 'required_if:sendtype,1|nullable|integer|between:1,2',
            'sendtype-2-input-from' => 'required_if:sendtype,2|nullable|integer|between:1,9999998',
            'sendtype-2-input-to' => 'required_if:sendtype,2|nullable|integer|between:1,9999999',
            'text_or_multi' => 'integer|min:1|max:3',
            'title' => 'required_if:text_or_multi,3|string|max:80',
            'multimedia_image' => 'required_if:text_or_multi,2|nullable|mimes:jpeg,jpg,png|max:100',
            'message' => 'required_if:text_or_multi,3|string|max:200'
        ]);
        
        $path = public_path('uploads');
        $files = glob($path .'/fcm_*');
        $now = \Carbon\Carbon::now()->timestamp;
        foreach ($files as $f) {
            if ($now - filemtime($f) > 86400) {
                unlink($f);
            }
        }
        
        $success = null;
        $error = null;
        
        $title = $req->post('title') == null ? '' : $req->post('title');
        $message = $req->post('message') == null ? '' : $req->post('message');
        $table = DB::table('notif_id');
        $type = $req->post('sendtype');
        
        if ($req->post('text_or_multi') != 1) {
            $filename = 'fcm_'.$now.'.'.$req->file('multimedia_image')->getClientOriginalExtension();
            $req->file('multimedia_image')->move($path, $filename);
            $data = ['image' => env('APP_URL').'/public/uploads/'.$filename];
            if ($req->post('text_or_multi') == 3) {
                $data['small'] = 1;
            }
        } else {
            $data = null;
        }
        if ($type == 1) {
            $s = $req->post('email_or_userid_type');
            if ($s == 1) {
                $id = $table->where('userid', $req->post('email_or_userid'))->first();
                if ($id) {
                    $success = \Funcs::sendFCM($id->sender_id, $title, $message, $data);
                } else {
                    $error = "لم يتم العثور على معرف المستخدم أو لم يتم تسجيل معرف جهاز هذا المستخدم بعد!";
                }
            } else {
                $id = $table->where('email', $req->post('email_or_userid'))->first();
                if ($id) {
                    $success = \Funcs::sendFCM($id->sender_id, $title, $message, $data);
                } else {
                    $error = "لم يتم العثور على البريد الإلكتروني أو لم يتم تسجيل معرف جهاز هذا المستخدم بعد!";
                }
            }
        } elseif ($type == 2) {
            $from = $req->post('sendtype-2-input-from') - 1;
            $to = $req->post('sendtype-2-input-to') - 1;
            if ($to - $from < 50) {
                $tokens = $table->orderBy('id', 'desc')->skip($from)->take($to)->pluck('sender_id')->toArray();
                $success = \Funcs::sendFCM($tokens, $title, $message, $data);
            } else {
                $error = "تم تجاوز الحد! يمكنك إرسال ما يصل إلى 50 مستخدمًا لكل محاولة.";
            }
        } elseif ($type == 3) {
            $tokens = DB::table('banned_users')
                        ->orderBy('banned_users.id', 'desc')
                        ->limit(50)
                        ->leftJoin('notif_id', 'banned_users.userid', '=', 'notif_id.userid')
                        ->pluck('notif_id.sender_id')
                        ->toArray();
            $success = \Funcs::sendFCM($tokens, $title, $message, $data);
        } elseif ($type == 4) {
            $tokens = DB::table('leaderboard')
                        ->orderBy('score_prv', 'desc')
                        ->limit(env('LEADERBOARD_LIMIT'))
                        ->leftJoin('notif_id', 'leaderboard.userid', '=', 'notif_id.userid')
                        ->pluck('notif_id.sender_id')
                        ->toArray();
            $success = \Funcs::sendFCM($tokens, $title, $message, $data);
        } elseif ($type == 5) {
            $success = \Funcs::sendFCM(null, $title, $message, $data, true);
        }

        if ($error != null) {
            return back()->withInput()->with('error', $error);
        } elseif ($success['status'] == 1) {
            return back()->with('success', 'وصلت بنجاح!');
        } else {
            return back()->withInput()->with('error', $success['result']);
        }
    }

    public function localView(Request $req)
    {
        $check = DB::table('misc')->where('name', 'global_msg')->first();
        if ($check) {
            $d = unserialize($check->data);
            $t = $d['title'];
            $m = $d['desc'];
        } else {
            $t = '';
            $m = '';
        }
        $pending = DB::table('message')->paginate(5);
        $data = ['title' => $t, 'desc' => $m, 'pending' => $pending];
        return view('admin.notification-local', compact('data'));
    }

    public function globalMsg(Request $req){
    
        $this->validate($req, ['title' => 'nullable|string|max:50','desc' => 'required_with:title|string|max:500' ]);
            
            
        $title = $req->post('title');
        Cache::forget('global_msg');
        Cache::forget('gmid');
        
        if ($title == null) {
            DB::table('misc')->where('name', 'global_msg')->delete();
            return back()->with('success', 'تم تعطيل الرسالة العالمية');
        } else {
            $data = ['title' => $title, 'desc' => $req->post('desc'), 'mid' => Str::random(5)];
            DB::table('misc')->updateOrInsert(
                ['name' => 'global_msg'],
                ['data' => serialize($data)]
            );
            return back()->with('success', 'تم تحديث الرسالة العالمية بنجاح');
        }
    }

    public function localSend(Request $req)
    {
        $this->validate($req, [
            'to' => 'required|string|max:190',
            'title' => 'required|string|max:100|min:5',
            'message_body' => 'required|string|max:250|min:20'
        ]);
        $to = $req->post('to');
        $user = DB::table('users')->where('userid', $to)->orWhere('email', $to);
        $check = $user->first();
        if ($check) {
            DB::table('message')->insert([
                'userid' => $check->userid,
                'title' => $req->post('title'),
                'msg' => $req->post('message_body')
            ]);
            $user->update(['has_notification' => 1]);
            return back()->with('success', 'Message sent.');
        } else {
            return back()->withinput()->with('error', 'معرف المستخدم أو عنوان البريد الإلكتروني غير موجود في قاعدة البيانات!');
        }
    }

    public function localDel(Request $req)
    {
        $info = DB::table('message')->where('id', $req->get('id'));
        $uid = $info->first()->userid;
        if (count(DB::table('message')->where('userid', $uid)->get()) == 1) {
            DB::table('users')->where('userid', $uid)->update(['has_notification' => 0]);
        }
        $info->delete();
        return back()->with('success', 'تمت إزالة الرسالة من قائمة الانتظار');
    }
}
