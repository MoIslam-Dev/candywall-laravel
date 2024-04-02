<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Funcs;
use File;
use DB;

class Chat extends Controller{

    public function get(Request $req){
    
        if (env('CHAT_DISABLED') == 1) {
            return['status' => -2, 'message' => 'Chat room is disabled!'];
        }
        
        $msgs = DB::table('chat')->orderBy('id', 'desc')->take(20)->get()->reverse()->values();
                    
        return[
            'status' => 1,
            'msg' => $msgs,
            'warn' => Funcs::getmisc('chat_warning'),
            'attachment' => env('CHAT_ATTACHMENT'),
            'attach_size' => env('CHAT_ATTACH_KB')
        ];
    }

    public function getNew(Request $req){
    
        $lastId = $req->get('id');
        $msgs = DB::table('chat')->where('id', '>', $lastId)->orderBy('id', 'asc')->get();
        return['status' => 1, 'msg' => $msgs];
    }
    
     public function postMessage(Request $req){
         
        try { 
         
         if (env('CHAT_DISABLED') == 1) {
                return['status' => -2, 'message' => 'Chat room is disabled!'];
            }
            $user = $req['user'];
            if (Funcs::isBanned($user, $user->device_id)) {
                return['status' => -1, 'message' => 'You have been banned!'];
            }
            
            $lastId = $req->post('id');
            $type = $req->post('type');
            
            if ($type == 'message') {
                
                $msg = $req->post('msg');
				$cns = Funcs::getmisc('censored_words');
				
                if ($cns != '') {
                    
                    $cns = explode(',', $cns);
                    
                    foreach ($cns as $cn) {
                        if (strpos($msg, $cn) !== false) {
                            return['status' => -1, 'message' => 'Your message contains unacceptable word!'];
                        }
                    }
                }
                
            $now = Carbon::now();
            DB::table('chat')->insert([
                'userid' => $user->userid,
                'name' => $user->name,
                'avatar' => $user->avatar == null ? '' : $user->avatar,
                'message' => $msg,
                'datetime' => $now->timestamp,
                'is_staff' => 0,
                'updated_at' => $now
            ]);
            
            $msgs = DB::table('chat')->where('id', '>', $lastId)->orderBy('id', 'asc')->get();
            return['status' => 1, 'msg' => $msgs];
                
            }
            
        } catch (\Exception $e) {
            return['status' => 0, 'message' => 'Message delivery failed'];
        }    
         
         
         
     }

     
     public function postImageAoudio(Request $req){
    
       try {
           
        if (env('CHAT_DISABLED') == 1) {
            return ['status' => -2, 'message' => 'Chat room is disabled!'];
        }

        $user = $req['user'];
        if (Funcs::isBanned($user, $user->device_id)) {
            return ['status' => -1, 'message' => 'You have been banned!'];
        }

        $lastId = $req->post('id');
        $type = $req->post('type');
        
     if ($type == 'image' || $type == 'audio') {
            if (env('CHAT_ATTACHMENT') != 1) {
                return ['status' => -1, 'message' => 'Attachment not allowed!'];
            }
            
            // تحقق من وجود الملف في الطلب
            if (!$req->file('image')) {
                return ['status' => -1, 'message' => 'File not found in the request!'];
            }
            
            // حدد النوع والامتداد الصحيح للملف (image أو audio)
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'mp3', 'mp4'];
            $file = $req->file('image');
            $fileExtension = $file->getClientOriginalExtension();
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                return ['status' => -1, 'message' => 'Unsupported file type!'];
            }
            
            // التحقق من حجم الملف
            $attachSize = env('CHAT_ATTACH_KB');
            $fileSize = $file->getSize(); // حجم الملف بالكيلوبايت
            if ($fileSize > $attachSize * 1024) {
                return ['status' => -1, 'message' => 'File size exceeds the allowed limit!'];
            }

            // حفظ الملف والبيانات المرتبطة به في قاعدة البيانات
            $filename = Carbon::now()->timestamp . '.' . $fileExtension;
            $file->move(storage_path('app/public'), $filename);
            $msg = env('APP_URL') . '/api/chat/media/' . $filename;
        } else {
            return ['status' => -1, 'message' => 'Message delivery failed due to unsupported content!'];
        }
        
        $keep = 100;
        $count = DB::table('chat')->count();
        if ($count > $keep) {
            $db = DB::table('chat')->orderBy('id', 'asc')->skip($keep)->take($count - $keep);
            $check = $db->get();
            foreach ($check as $ck) {
                $c = $ck->message;
                if (substr($c, 0, 4) === "http") {
                    if (substr($c, -5) === ".jpeg" || substr($c, -4) === ".jpg"
                        || substr($c, -4) === ".png" || substr($c, -4) === ".gif"
                        || substr($c, -4) === ".mp3" || substr($c, -4) === ".mp4") {
                        $fpath = storage_path('app/public') . '/' . basename($c);
                        if (File::exists($fpath)) {
                            unlink($fpath);
                        }
                    }
                }
            }
            $db->delete();
        }
        $now = Carbon::now();
        DB::table('chat')->insert([
            'userid' => $user->userid,
            'name' => $user->name,
            'avatar' => $user->avatar == null ? '' : $user->avatar,
            'message' => $msg,
            'datetime' => $now->timestamp,
            'is_staff' => 0,
            'updated_at' => $now
        ]);
        $msgs = DB::table('chat')->where('id', '>', $lastId)->orderBy('id', 'asc')->get();
        return ['status' => 1, 'msg' => $msgs];
    } catch (\Exception $e) {
        return ['status' => 0, 'message' => 'Message delivery failed ' . $lastId . ' | ' . $e->getMessage()];
    }
    }


    public function serveAttachment($name)
    {
        $path = storage_path('app/public/'.$name);
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = \Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
}
