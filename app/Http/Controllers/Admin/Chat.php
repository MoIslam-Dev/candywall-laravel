<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use File;
use Funcs;
use DB;

class Chat extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        $keep = 100;
        $count = DB::table('chat')->count();
        if ($count > $keep) {
            DB::table('chat')->orderBy('id', 'asc')->skip($keep)->take($count-$keep)->delete();
        }
        $data['warning'] = Funcs::getmisc('chat_warning');
        $data['censored'] = Funcs::getmisc('censored_words');
        return view('admin.chat', compact('data'));
    }

    public function quick(Request $req)
    {
        $msgs = DB::table('chat')->orderBy('id', 'asc')->get();
        return ['msgs' => $msgs];
    }

    public function send(Request $req)
    {
        $msg = $req->json('msg');
        $adm = DB::table('users')->where('id', env('ADMIN'))->first();
        $now = Carbon::now();
        DB::table('chat')->insert([
            'userid' => $adm->userid,
            'name' => $adm->name,
            'avatar' => $adm->avatar == null ? 'none' : $adm->avatar,
            'message' => $msg,
            'datetime' => $now->timestamp,
            'is_staff' => 1,
            'updated_at' => $now
        ]);
        $msgs = DB::table('chat')->orderBy('id', 'asc')->get();
        return ['msgs' => $msgs];
    }

    public function del(Request $req)
    {
        $id = $req->get('id');
        $db = DB::table('chat')->where('id', $id);
        $data = $db->first();
        if ($data) {
            $c = $data->message;
            if (substr($c, 0, 4) === "http") {
                if (substr($c, -5) === ".jpeg" || substr($c, -4) === ".jpg"
                    || substr($c, -4) === ".png" || substr($c, -4) === ".gif"
                    || substr($c, -4) === ".mp3" || substr($c, -4) === ".mp4") {
                    $fpath = storage_path('app/public').'/'.basename($c);
                    if (File::exists($fpath)) {
                        unlink($fpath);
                    }
                }
            }
        }
        $db->delete();
        $msgs = DB::table('chat')->orderBy('id', 'asc')->get();
        return ['msgs' => $msgs];
    }

    public function delAll(Request $req)
    {
        DB::table('chat')->truncate();
        $fd = storage_path('app/public');
        $fs = new \Illuminate\Filesystem\Filesystem;
        $fs->cleanDirectory($fd);
        return back();
    }

    public function update(Request $req)
    {
        $this->validate($req, [
            'chat_disable' => 'required|integer|between:0,1',
            'attachment_size' => 'required|integer|between:0,10240',
            'warning_message' => 'nullable|string',
            'censored_words' => 'nullable|string'
        ]);
        Funcs::setEnv('CHAT_DISABLED', $req->post('chat_disable'), false);
        Funcs::setEnv('CHAT_ATTACHMENT', $req->has('attachment_status') ? '1' : '0', false);
        Funcs::setEnv('CHAT_ATTACH_KB', $req->post('attachment_size'));
        $wm = $req->post('warning_message');
        Funcs::setmisc('chat_warning', $wm == null ? '' : $wm);
        $cw = $req->post('censored_words');
        if ($cw == null) {
            $cw = '';
        }
        $cw = trim($cw, ',');
        if ($cw != '') {
            $cw = str_replace(array(",,",", "), ",", $cw);
        }
        Funcs::setmisc('censored_words', $cw);
        return back()->with('success', 'Chat settings were updated.');
    }
	public function adminAttachment($name)
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
