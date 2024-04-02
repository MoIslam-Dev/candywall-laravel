<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Funcs;
use DB;

class Withdraw extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $pending = DB::table('gate_request')->where('is_completed', 0)
                        ->orderBy('created_at', 'desc')
                        ->paginate(3, ['*'], 'p');
        $completed = DB::table('gate_request')->where('is_completed', 1)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10, ['*'], 'c');
        $wd = ['pending' => $pending, 'completed' => $completed];
        return view('admin.withdraw', compact('wd'));
    }

    public function proceed(Request $req)
    {
        $db = DB::table('gate_request')->where('is_completed', 0)->where('id', $req->get('id'));
        $check = $db->first();
        if ($check) {
			DB::table('users')->where('userid', $check->userid)->decrement('pending', $check->points);
           // Funcs::deductGamePoints($check->userid, 'Redeemption', $check->points, false);
        }
        $db->update(['is_completed' => 1]);
        return back()->with('success', "Withdrawal marked as processed!");
    }
	
    public function discard(Request $req)
    {
        $reason = $req->get('reason');
        if (strpos($reason, ';@') !== false) {
            return back()->with('error', "Invalid characters exist in your refusing reason.");
        }
        $d = DB::table('gate_request')->where('id', $req->post('id'));
        $wd = $d->first();
        if ($wd->is_completed == 0) {
            $u = DB::table('users')->where('userid', $wd->userid);
            $u->increment('balance', $wd->points);
            $u->decrement('pending', $wd->points);
            if ($reason == null || $reason == '') {
                $d->update(['is_completed' => 2,'message' => 'No reason provided.']);
            } else {
                $d->update(['is_completed' => 2,'message' => $reason]);
            }
            /*
            if ($req->has('reason') && $req->get('reason') != '') {
                DB::table('message')->insert(['userid' => $wd->userid,'title' => 'Withdrawal rejected!', 'msg' => $req->get('reason')]);
                $u->increment('has_notification', 1);
            }
            $d->delete();
            */
            return back()->with('success', "Withdrawal request rejected and ".strtolower(env('CURRENCY_NAME'))."s returned to the user balance!");
        } else {
            return back()->with('error', "This withdrawal already processed by the system.");
        }
    }
}
