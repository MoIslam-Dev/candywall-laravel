<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Cache;
use DB;

class Networks extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function sdkView(Request $req)
    {
        $json = DB::table('offerwalls')->where('type', 1)->get();
        $data = array();
        $pb = env('APP_URL').'/api/pb/';
        foreach ($json as $j) {
            $d = DB::table('postbacks')->where('offerwall_id', $j->id)->first();
            if ($d->param_ip != '' && $d->param_ip != null && $d->param_ip != '-none-') {
                $ip = '&'.$d->param_ip;
            } else {
                $ip = '';
            }
            if ($d->postback_type == 1) {
                $url = $pb.$d->param_tok.'/'.$d->network_slug.'?'.$d->param_amount.'&'.$d->param_userid.'&'.$d->param_offerid.$ip;
            } else {
                $url = $pb.$d->param_tok.'/'.$d->network_slug;
            }
            array_push($data, ['name' => $d->network_name, 'image' => $d->network_image, 'status' => $j->enabled, 'of_id' => $j->id, 'postback' => $url]);
        }
        return view('admin.networks_sdk', compact('data'));
    }

    public function sdkEdit(Request $req)
    {
        $id = $req->get('id');
        $d = DB::table('offerwalls')->where('id', $id)->first();
        $a = json_decode($d->data);
        $b = DB::table('postbacks')->where('offerwall_id', $id)->first();
        $c = array();
        foreach ($a as $y) {
            array_push($c, ['name' => $y->name, 'value' => $y->value]);
        }
        $data = [
            'id' => $id,
            'enabled' => $d->enabled,
            'offerwall_description' => $d->description,
            'extra' => $c,
            'postback_type_key' => $b->postback_type,
            'postback_exchange' => $b->exchange,
            'network_name' => $b->network_name,
            'image' => $b->network_image,
            'network_slug' => $b->network_slug,
            'postback_url_secret_key' => $b->param_tok,
            'postback_reward_amount_key' => $b->param_amount,
            'postback_user_id_key' => $b->param_userid,
            'postback_offer_id_key' => $b->param_offerid,
            'postback_ip_address_key' => $b->param_ip,
            'verify' => $b->param_verify,
            'back' => $id > 6 ? route('networks_cpv') : route('networks_sdk')
        ];
        return view('admin.networks_sdk_edit', compact('data'));
    }

    public function sdkUpdate(Request $req)
    {
        $this->validate($req, [
            'id' => 'required|integer',
            'enabled' => 'required|integer|between:1,2',
            'network_name' => 'required|string|max:20|regex:/^[\w ]+$/',
            'offerwall_description' => 'required|string|between:10,100',
            'network_image' => 'nullable|mimes:jpeg,jpg,png|max:1000',
            'postback_exchange' => 'required|integer|between:1,2',
            'postback_url_secret_key' => 'required|string|max:40',
            'postback_type_key' => 'required|integer|between:1,2',
            'verify' => 'nullable|string|max:40|regex:/(=)/',
            'values' => 'required'
        ]);
        $id = $req->post('id');
        $db = DB::table('offerwalls')->where('id', $id);
        $a = json_decode($db->first()->data);
        $data = array();
        $v = $req->post('values');
        $count =  count($v);
        for ($i = 0; $i < $count; $i++) {
            array_push($data, ['name' => $a[$i]->name, 'slug' => $a[$i]->slug, 'value' => $v[$i]]);
        }
        $nn = $req->post('network_name');
        $db->update([
            'title' => $nn,
            'description' => $req->post('offerwall_description'),
            'enabled' => $req->post('enabled'),
            'data' => json_encode($data)
        ]);
        $postback = DB::table('postbacks')->where('offerwall_id', $id);
        $filename = basename($postback->first()->network_image);
        if ($req->hasFile('network_image')) {
            $path = public_path('uploads');
            if ($filename != null && file_exists($path.'/'.$filename)) {
                unlink($path.'/'.$filename);
            };
            $filename = Carbon::now()->timestamp.'.'.$req->file('network_image')->getClientOriginalExtension();
            $req->file('network_image')->move($path, $filename);
        }
        $postback->update([
            'network_name' => $nn,
            'exchange' => $req->post('postback_exchange'),
            'network_image' => env('APP_URL').'/public/uploads/'.$filename,
            'param_tok' => $req->post('postback_url_secret_key'),
            'postback_type' => $req->post('postback_type_key'),
            'param_verify' => $req->post('verify')
            ]);
        Cache::forget('connect_offers');
        if ($id > 6) {
            return redirect()->route('networks_cpv')->with('success', 'تم تحديث شبكة الإعلانات بنجاح.');
        } else {
            return redirect()->route('networks_sdk')->with('success', 'تم تحديث شبكة الإعلانات بنجاح.');
        }
    }
    public function cpaView(Request $req)
    {
        $json = DB::table('offerwalls')->where('type', 2)->get();
        $data = array();
        $pb = env('APP_URL').'/api/pb/';
        foreach ($json as $j) {
            $d = DB::table('postbacks')->where('offerwall_id', $j->id)->first();
            if ($d->param_ip != '' && $d->param_ip != null && $d->param_ip != '-none-') {
                $ip = '&'.$d->param_ip;
            } else {
                $ip = '';
            }
            if ($d->postback_type == 1) {
                $url = $pb.$d->param_tok.'/'.$d->network_slug.'?'.$d->param_amount.'&'.$d->param_userid.'&'.$d->param_offerid.$ip;
            } else {
                $url = $pb.$d->param_tok.'/'.$d->network_slug;
            }
            array_push($data, ['name' => $d->network_name, 'image' => $d->network_image, 'status' => $j->enabled, 'of_id' => $j->id, 'postback' => $url]);
        }
        return view('admin.networks_cpa', compact('data'));
    }

    public function cpaNew(Request $req)
    {
        return view('admin.networks_cpa_new');
    }

    public function cpaAdd(Request $req)
    {
        $this->validate($req, [
            'enabled' => 'required|integer|between:1,2',
            'network_name' => 'required|string|max:20|regex:/^[\w ]+$/',
            'offerwall_description' => 'required|string|between:10,100',
            'offer_api_url' => 'required|string',
            'offer_api_auth' => 'nullable|string',
            'offerwall_type' => 'required|integer|between:1,2',
            'json_array_key' => 'nullable|string|max:50',
            'offer_id_key' => 'required|string|max:50',
            'offer_title_key' => 'required|string|max:50',
            'offer_description_key' => 'required|string|max:50',
            'reward_amount_key' => 'required|string|max:50',
            'icon_url_key' => 'required|string|max:50',
            'offer_url_key' => 'required|string|max:50',
            'offer_url_suffix' => 'nullable|string|max:100',
            'network_image' => 'required|mimes:jpeg,jpg,png|max:1000',
            'postback_type_key' => 'required|integer|between:1,2',
            'postback_exchange' => 'required|integer|between:1,2',
            'postback_url_secret_key' => 'required|string|max:40',
            'postback_reward_amount_key' => 'required|string|max:40|regex:/(=)/',
            'postback_user_id_key' => 'required|string|max:40|regex:/(=)/',
            'postback_offer_id_key' => 'required|string|max:40|regex:/(=)/',
            'postback_ip_address_key' => 'nullable|string|max:40|regex:/(=)/',
            'verify' => 'nullable|string|max:40|regex:/(=)/'
        ]);

        $nn = $req->post('network_name');
        $nn_slug = strtolower(str_replace(' ', '', $nn));
        if (DB::table('postbacks')->where('network_name', $nn)->first()) {
            return back()->withInput()->with('error', 'اسم الشبكة موجود بالفعل');
        }

        $json_array_key = $req->post('json_array_key') == '' ? '-none-' : $req->post('json_array_key');
        $offer_url_suffix = $req->post('offer_url_suffix') == '' ? '-none-' : $req->post('offer_url_suffix');
        $ipParam = $req->post('postback_ip_address_key');
        if ($ipParam == null || strpos($ipParam, '=') === false) {
            $ipParam = 'ip=blank';
        }
        $data = json_encode([
            'offerwall_type' => $req->post('offerwall_type'),
            'offer_api_url' => $req->post('offer_api_url'),
			'offer_api_auth' => $req->post('offer_api_auth'),
            'json_array_key' => $json_array_key,
            'offer_id_key' => $req->post('offer_id_key'),
            'offer_title_key' => $req->post('offer_title_key'),
            'offer_description_key' => $req->post('offer_description_key'),
            'reward_amount_key' => $req->post('reward_amount_key'),
            'icon_url_key' => $req->post('icon_url_key'),
            'offer_url_key' => $req->post('offer_url_key'),
            'offer_url_suffix' => $offer_url_suffix
        ]);
        $id = DB::table('offerwalls')->insertGetId([
            'type' => 2,
            'enabled' => $req->post('enabled'),
            'name' => $nn_slug,
            'title' => $nn,
            'description' => $req->post('offerwall_description'),
            'data' => $data
        ]);
        $path = public_path('uploads');
        $filename = Carbon::now()->timestamp.'.'.$req->file('network_image')->getClientOriginalExtension();
        $req->file('network_image')->move($path, $filename);
        DB::table('postbacks')->insert([
            'offerwall_id' => $id,
            'postback_type' => $req->post('postback_type_key'),
            'exchange' => $req->post('postback_exchange'),
            'network_name' => $nn,
            'network_slug' => $nn_slug . '_a',
            'network_image' => env('APP_URL').'/public/uploads/'.$filename,
            'param_tok' => $req->post('postback_url_secret_key'),
            'param_amount' => $req->post('postback_reward_amount_key'),
            'param_userid' => $req->post('postback_user_id_key'),
            'param_offerid' => $req->post('postback_offer_id_key'),
            'param_ip' => $ipParam,
            'param_verify' => $req->post('verify')
        ]);
        Cache::forget('connect_offers');
        return redirect()->route('networks_cpa')->with('success', 'تمت إضافة شبكة الإعلانات بنجاح.');
    }

    public function cpaEdit(Request $req)
    {
        $id = $req->get('id');
        $d = DB::table('offerwalls')->where('id', $id)->first();
        $a = json_decode($d->data);
        $b = DB::table('postbacks')->where('offerwall_id', $id)->first();
        $data = [
            'id' => $id,
            'enabled' => $d->enabled,
            'offerwall_type' => $a->offerwall_type,
            'offer_api_url' => $a->offer_api_url,
			'offer_api_auth' => isset($a->offer_api_auth) ? $a->offer_api_auth : '',
            'json_array_key' => $a->json_array_key,
            'offer_id_key' => $a->offer_id_key,
            'offer_title_key' => $a->offer_title_key,
            'offerwall_description' => $d->description,
            'offer_description_key' => $a->offer_description_key,
            'reward_amount_key' => $a->reward_amount_key,
            'icon_url_key' => $a->icon_url_key,
            'offer_url_key' => $a->offer_url_key,
            'offer_url_suffix' => $a->offer_url_suffix,
            'postback_type_key' => $b->postback_type,
            'postback_exchange' => $b->exchange,
            'network_name' => $b->network_name,
            'image' => $b->network_image,
            'postback_url_secret_key' => $b->param_tok,
            'postback_reward_amount_key' => $b->param_amount,
            'postback_user_id_key' => $b->param_userid,
            'postback_offer_id_key' => $b->param_offerid,
            'postback_ip_address_key' => $b->param_ip,
            'verify' => $b->param_verify
        ];
        return view('admin.networks_cpa_edit', compact('data'));
    }

    public function cpaUpdate(Request $req)
    {
        $this->validate($req, [
            'id' => 'required|integer',
            'enabled' => 'required|integer|between:1,2',
            'network_name' => 'required|string|max:20|regex:/^[\w ]+$/',
            'offerwall_description' => 'required|string|between:10,100',
            'offer_api_url' => 'required|string',
            'offer_api_auth' => 'nullable|string',
            'offerwall_type' => 'required|integer|between:1,2',
            'json_array_key' => 'nullable|string|max:50',
            'offer_id_key' => 'required|string|max:50',
            'offer_title_key' => 'required|string|max:50',
            'offer_description_key' => 'required|string|max:50',
            'reward_amount_key' => 'required|string|max:50',
            'icon_url_key' => 'required|string|max:50',
            'offer_url_key' => 'required|string|max:50',
            'offer_url_suffix' => 'nullable|string|max:100',
            'network_image' => 'nullable|mimes:jpeg,jpg,png|max:1000',
            'postback_type_key' => 'required|integer|between:1,2',
            'postback_exchange' => 'required|integer|between:1,2',
            'postback_url_secret_key' => 'required|string|max:40',
            'postback_reward_amount_key' => 'required|string|max:40|regex:/(=)/',
            'postback_user_id_key' => 'required|string|max:40|regex:/(=)/',
            'postback_offer_id_key' => 'required|string|max:40|regex:/(=)/',
            'postback_ip_address_key' => 'nullable|string|max:40|regex:/(=)/',
            'verify' => 'nullable|string|max:40|regex:/(=)/'
        ]);
        $nn = $req->post('network_name');
        $nn_slug = strtolower(str_replace(' ', '', $nn));
        $id = $req->post('id');
        $check = DB::table('postbacks')->where('network_name', $nn)->first();
        if ($check && $id != $check->offerwall_id) {
            return back()->withInput()->with('error', 'اسم الشبكة موجود بالفعل');
        }
        $json_array_key = $req->post('json_array_key') == '' ? '-none-' : $req->post('json_array_key');
        $offer_url_suffix = $req->post('offer_url_suffix') == '' ? '-none-' : $req->post('offer_url_suffix');
        $ipParam = $req->post('postback_ip_address_key');
        if ($ipParam == null || strpos($ipParam, '=') === false) {
            $ipParam = 'ip=blank';
        }
        $data = json_encode([
            'offerwall_type' => $req->post('offerwall_type'),
            'offer_api_url' => $req->post('offer_api_url'),
			'offer_api_auth' => $req->post('offer_api_auth'),
            'json_array_key' => $json_array_key,
            'offer_id_key' => $req->post('offer_id_key'),
            'offer_title_key' => $req->post('offer_title_key'),
            'offer_description_key' => $req->post('offer_description_key'),
            'reward_amount_key' => $req->post('reward_amount_key'),
            'icon_url_key' => $req->post('icon_url_key'),
            'offer_url_key' => $req->post('offer_url_key'),
            'offer_url_suffix' => $offer_url_suffix
        ]);
        DB::table('offerwalls')->where('id', $id)->update([
            'enabled' => $req->post('enabled'),
            'name' => $nn_slug,
            'data' => $data,
            'title' => $nn,
            'description' => $req->post('offerwall_description'),
        ]);
        $postback = DB::table('postbacks')->where('offerwall_id', $id);
        $filename = basename($postback->first()->network_image);
        if ($req->hasFile('network_image')) {
            $path = public_path('uploads');
            if ($filename != null && file_exists($path.'/'.$filename)) {
                unlink($path.'/'.$filename);
            };
            $filename = Carbon::now()->timestamp.'.'.$req->file('network_image')->getClientOriginalExtension();
            $req->file('network_image')->move($path, $filename);
        }
        $postback->update([
            'postback_type' => $req->post('postback_type_key'),
            'exchange' => $req->post('postback_exchange'),
            'network_name' => $nn,
            'network_slug' => $nn_slug . '_a',
            'network_image' => env('APP_URL').'/public/uploads/'.$filename,
            'param_tok' => $req->post('postback_url_secret_key'),
            'param_amount' => $req->post('postback_reward_amount_key'),
            'param_userid' => $req->post('postback_user_id_key'),
            'param_offerid' => $req->post('postback_offer_id_key'),
            'param_ip' => $ipParam,
            'param_verify' => $req->post('verify')
        ]);
        Cache::forget('connect_offers');
        return redirect()->route('networks_cpa')->with('success', 'تم تحديث شبكة الإعلانات بنجاح.');
    }

    public function offerwallDel(Request $req)
    {
        $id = $req->get('id');
        $offerwall = DB::table('offerwalls')->where('id', $id);
        $check = $offerwall->first();
        if ($check->type == 1 || $check->type == 3) {
            return back()->with('error', 'لا يمكن حذف هذه الشبكة!');
        }
        $postback = DB::table('postbacks')->where('offerwall_id', $id);
        $filename = basename($postback->first()->network_image);
        $path = public_path('uploads');
        if ($filename != null && file_exists($path.'/'.$filename)) {
            unlink($path.'/'.$filename);
        };
        $offerwall->delete();
        $postback->delete();
        Cache::forget('connect_offers');
        return redirect()->route('networks_cpa')->with('success', 'تم حذف شبكة الإعلانات بنجاح.');
    }

    public function cpvView(Request $req)
    {
        $json = DB::table('offerwalls')->where('type', 3)->get();
        $data = array();
        $pb = env('APP_URL').'/api/pb/';
        foreach ($json as $j) {
            $d = DB::table('postbacks')->where('offerwall_id', $j->id)->first();
            if ($d->param_ip != '' && $d->param_ip != null && $d->param_ip != '-none-') {
                $ip = '&'.$d->param_ip;
            } else {
                $ip = '';
            }
            if ($d->postback_type == 1) {
                $url = $pb.$d->param_tok.'/'.$d->network_slug.'?'.$d->param_amount.'&'.$d->param_userid.'&'.$d->param_offerid.$ip;
            } else {
                $url = $pb.$d->param_tok.'/'.$d->network_slug;
            }
            array_push($data, ['name' => $d->network_name, 'image' => $d->network_image, 'status' => $j->enabled, 'of_id' => $j->id, 'postback' => $url]);
        }
        return view('admin.networks_cpv', compact('data'));
    }

    public function customView(Request $req)
    {
        $postback = DB::table('misc')->where('name', 'cpb')->first();
        $offers = DB::table('offerwall_c')->orderBy('id', 'desc')->paginate(10);
        $data = ['pb' => $postback ? $postback->data : null, 'offers' => $offers];
        return view('admin.networks_custom', compact('data'));
    }

    public function customAddView(Request $req)
    {
        $data = ['title' => 'Add a custom offer'];
        return view('admin.networks_custom_new', compact('data'));
    }

    public function customPostback(Request $req)
    {
        $this->validate($req, [
            'custom_token' => 'string|max:40|regex:/^[\w-]*$/'
        ]);
        DB::table('misc')->updateOrInsert(['name' => 'cpb'], ['data' => $req->post('custom_token')]);
        return back()->with('success', 'تم تحديث الرمز المميز بنجاح. الآن يمكنك استخدام رابط إعادة النشر الجديد من الأسفل');
    }

    public function customAdd(Request $req)
    {
        $this->validate($req, [
            'title' => 'required|string|max:99',
            'description' => 'required|string|max:190',
            'offer_icon' => 'required|mimes:jpeg,jpg,png|max:1000',
            'country' => 'nullable|string|max:190',
            'points' => 'required|integer|between:0,99999999',
            'max' => 'required|integer|between:0,99999999',
            'url' => 'required|string'
        ]);
        $ctry = $req->post('country');
        if ($ctry == null) {
            $ctry = 'all';
        } else {
            $ct = explode(',', $ctry);
            $ol = DB::table('online_users')->pluck('country_iso');
            foreach ($ct as $c) {
                if (strlen($c) != 2) {
                    return back()->withInput()->with('error', 'أدخل رمز ISO الخاص بالبلد المكون من رقمين. بالنسبة إلى بلدان متعددة، اجعلها مفصولة بفواصل مثل: US، AU، GB، FR');
                } elseif (strpos($ol, strtoupper($c)) === false) {
                    return back()->withInput()->with('error', 'أنت قد دخلت "'.$c.'"وهو رمز ISO خاطئ للبلد. أدخل رمز دولة صالحًاO.');
                };
            }
        }
        $url = $req->post('url');
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return back()->withInput()->with('error', 'عنوان URL للعرض غير صالح. يجب أن تحتوي عروض CPA على عنوان URL صالح');
        } else {
            $url = str_replace('offerid=[app_oid]', '', $url);
            $url = str_replace('&uid=[app_uid]', '', $url);
            if (strpos($url, '?') != true) {
                $url = $url.'?offerid=[app_oid]&uid=[app_uid]';
            } elseif (substr($url, -1) == '&') {
                $url = $url.'offerid=[app_oid]&uid=[app_uid]';
            } elseif (substr($url, -1) == '?') {
                $url = $url.'offerid=[app_oid]&uid=[app_uid]';
            } else {
                $url = $url.'&offerid=[app_oid]&uid=[app_uid]';
            }
        }
        if (DB::table('offerwall_c')->where('url', $url)->exists()) {
            return back()->withInput()->with('error', 'هذا العرض موجود بالفعل في قاعدة البيانات. يمكنك تمديد إتاحة العرض الحالي عن طريق التحرير.');
        }
        $path = public_path('uploads');
        $filename = Carbon::now()->timestamp.'.'.$req->file('offer_icon')->getClientOriginalExtension();
        $req->file('offer_icon')->move($path, $filename);
        $id = DB::table('offerwall_c')->insertGetId([
            'offer_id' => 'p',
            'type' => 2,
            'country' => $ctry,
            'title' => $req->post('title'),
            'description' => $req->post('description'),
            'points' => $req->post('points'),
            'image' => env('APP_URL').'/public/uploads/'.$filename,
            'url' => $url,
            'max' => $req->post('max')
        ]);
        DB::table('offerwall_c')->where('id', $id)->update(['offer_id' => 'p'.$id]);
        return redirect()->route('networks_custom')->with('success', 'تمت إضافة العرض بنجاح.');
    }

    public function customEditView(Request $req)
    {
        $db = DB::table('offerwall_c')->where('id', $req->get('id'))->first();
        $data = ['title' => 'EDIT: '.$db->title, 'offer' => $db];
        return view('admin.networks_custom_edit', compact('data'));
    }

    public function customEdit(Request $req)
    {
        $this->validate($req, [
            'id' => 'required|integer',
            'title' => 'required|string|max:99',
            'description' => 'required|string|max:190',
            'offer_icon' => 'nullable|mimes:jpeg,jpg,png|max:1000',
            'country' => 'nullable|string|max:190',
            'points' => 'required|integer|between:0,99999999',
            'max' => 'required|integer|between:0,99999999',
            'url' => 'required|string'
        ]);
        $id = $req->post('id');
        $ctry = $req->post('country');
        if ($ctry == null) {
            $ctry = 'all';
        } else {
            $ct = explode(',', $ctry);
            $ol = DB::table('online_users')->pluck('country_iso');
            foreach ($ct as $c) {
                if (strlen($c) != 2) {
                    return back()->withInput()->with('error', 'أدخل رمز ISO الخاص بالبلد المكون من رقمين. بالنسبة إلى بلدان متعددة، اجعلها مفصولة بفواصل مثل: US، AU، GB، FR');
                } elseif (strpos($ol, strtoupper($c)) === false) {
                    return back()->withInput()->with('error', 'أنت قد دخلت "'.$c.'" وهو رمز ISO خاطئ للبلد. أدخل ISO صالحًا للبلد.');
                };
            }
        }
        $url = $req->post('url');
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return back()->withInput()->with('error', 'عنوان URL للعرض غير صالح. يجب أن تحتوي عروض CPA على عنوان URL صالح');
        } else {
            $url = str_replace('offerid=[app_oid]', '', $url);
            $url = str_replace('&uid=[app_uid]', '', $url);
            if (strpos($url, '?') != true) {
                $url = $url.'?offerid=[app_oid]&uid=[app_uid]';
            } elseif (substr($url, -1) == '&') {
                $url = $url.'offerid=[app_oid]&uid=[app_uid]';
            } elseif (substr($url, -1) == '?') {
                $url = $url.'offerid=[app_oid]&uid=[app_uid]';
            } else {
                $url = $url.'&offerid=[app_oid]&uid=[app_uid]';
            }
        }
        $offer = DB::table('offerwall_c')->where('id', $id);
        $filename = basename($offer->first()->image);
        if ($req->hasFile('offer_icon')) {
            $path = public_path('uploads');
            if ($filename != null && file_exists($path.'/'.$filename)) {
                unlink($path.'/'.$filename);
            };
            $filename = Carbon::now()->timestamp.'.'.$req->file('offer_icon')->getClientOriginalExtension();
            $req->file('offer_icon')->move($path, $filename);
        }
        $offer->update([
            'type' => 2,
            'country' => $ctry,
            'image' => env('APP_URL').'/public/uploads/'.$filename,
            'title' => $req->post('title'),
            'description' => $req->post('description'),
            'points' => $req->post('points'),
            'url' => $url,
            'max' => $req->post('max')
        ]);
        return redirect()->route('networks_custom')->with('success', 'تم تحديث العرض بنجاح.');
    }

    public function customDel(Request $req)
    {
        $db = DB::table('offerwall_c')->where('id', $req->get('id'));
        $filename = basename($db->first()->image);
        $path = public_path('uploads');
        if ($filename != null && file_exists($path.'/'.$filename)) {
            unlink($path.'/'.$filename);
        };
        $db->delete();
        return redirect()->route('networks_custom')->with('success', 'تم حذف العرض بنجاح.');
    }

    public function youtubeView(Request $req)
    {
        $data = DB::table('offers_yt')->orderBy('id', 'desc')->paginate(8);
        return view('admin.networks_yt', compact('data'));
    }
    public function youtubeAdd(Request $req)
    {
        $this->validate($req, [
            'video_id' => 'required|regex:/^[a-zA-Z0-9_-]{11}$/',
            'reward_amount' => 'required|integer',
            'country_iso' => 'nullable|string|max:190'
        ]);
        $id = $req->post('video_id');
        if (DB::table('offers_yt')->where('code', $id)->first()) {
            return back()->with('error', 'لقد أضفت هذا الفيديو بالفعل!');
        }
        $ctry = $req->post('country_iso');
        if ($ctry == null) {
            $ctry = 'all';
        } else {
            $ct = explode(',', $ctry);
            $ol = DB::table('online_users')->pluck('country_iso');
            foreach ($ct as $c) {
                if (strlen($c) != 2) {
                    return back()->withInput()->with('error', 'أدخل رمز ISO الخاص بالبلد المكون من رقمين. بالنسبة إلى بلدان متعددة، اجعلها مفصولة بفواصل مثل: US، AU، GB، FR');
                } elseif (strpos($ol, strtoupper($c)) === false) {
                    return back()->withInput()->with('error', 'أنت قد دخلت "'.$c.'"وهو رمز ISO خاطئ للبلد. أدخل ISO صالحًا للبلد.');
                };
            }
        }        

        $yurl = 'https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' . $id . '&format=json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $yurl);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status == 200) {
            try {
                $data = json_decode($result);
                DB::table('offers_yt')->insert([
                        'code' => $id,
                        'title' => $data->title,
                        'points' => $req->post('reward_amount'),
                        'country' => $ctry
                    ]);
                return back()->with('success', 'تمت إضافة الفيديو بنجاح!');
            } catch (\Exception $e) {
                return back()->with('error', 'لا يمكن تحليل بيانات json!');
            }
        } else {
            return back()->with('error', 'لم يتم العثور على الفيديو: '.$status);
        }

    }
    public function youtubeDel(Request $req)
    {
        DB::table('offers_yt')->where('id', $req->post('id'))->delete();
        return back()->with('success', 'تم حذف الفيديو.');
    }
    public function ppvView(Request $req)
    {
        $data = DB::table('offers_ppv')->orderBy('id', 'desc')->paginate(5);
        return view('admin.networks_ppv', compact('data'));
    }
    public function ppvAdd(Request $req)
    {
        $this->validate($req, [
            'ppv_title' => 'required|string|max:25',
            'ppv_url' => 'required|string',
            'ppv_time' => 'required|integer',
            'reward_amount' => 'required|integer',
            'country_iso' => 'nullable|string|max:189',
            'visit_per_day' => 'required|integer|min:0'
        ]);

        $url = $req->post('ppv_url');
        if (DB::table('offers_ppv')->where('url', $url)->first()) {
            return back()->with('error', 'لقد قمت بالفعل بإضافة عنوان URL هذا!');
        }
        $ctry = $req->post('country_iso');
        if ($ctry == null) {
            $ctry = 'all';
        } else {
            $ct = explode(',', $ctry);
            $ol = DB::table('online_users')->pluck('country_iso');
            foreach ($ct as $c) {
                if (strlen($c) != 2) {
                    return back()->withInput()->with('error', 'أدخل رمز ISO الخاص بالبلد المكون من رقمين. بالنسبة إلى بلدان متعددة، اجعلها مفصولة بفواصل مثل: US، AU، GB، FR');
                } elseif (strpos($ol, strtoupper($c)) === false) {
                    return back()->withInput()->with('error', 'أنت قد دخلت "'.$c.'" وهو رمز ISO خاطئ للبلد. أدخل ISO صالحًا للبلد.');
                };
            }
        }
        DB::table('offers_ppv')->insert([
            'title' => $req->post('ppv_title'),
            'url' => $url,
            'seconds' => $req->post('ppv_time'),
            'points' => $req->post('reward_amount'),
            'per_day' => $req->post('visit_per_day'),
            'country' => $ctry
        ]);
        return back()->with('success', 'تمت إضافة العرض بنجاح.');
    }
    public function ppvDel(Request $req)
    {
        DB::table('offers_ppv')->where('id', $req->post('id'))->delete();
        return back()->with('success', 'تم حذف عرض PPV.');
    }

    public function webView(Request $req)
    {
        $json = DB::table('offerwalls')->where('type', 4)->get();
        $data = array();
        $pb = env('APP_URL').'/api/pb/';
        foreach ($json as $j) {
            $d = DB::table('postbacks')->where('offerwall_id', $j->id)->first();
            if ($d->param_ip != '' && $d->param_ip != null && $d->param_ip != '-none-') {
                $ip = '&'.$d->param_ip;
            } else {
                $ip = '';
            }
            if ($d->postback_type == 1) {
                $url = $pb.$d->param_tok.'/'.$d->network_slug.'?'.$d->param_amount.'&'.$d->param_userid.'&'.$d->param_offerid.$ip;
            } else {
                $url = $pb.$d->param_tok.'/'.$d->network_slug;
            }
            array_push($data, ['name' => $d->network_name, 'image' => $d->network_image, 'status' => $j->enabled, 'of_id' => $j->id, 'postback' => $url]);
        }
        return view('admin.networks_web', compact('data'));
    }

    public function webNew(Request $req)
    {
        return view('admin.networks_web_new');
    }

    public function webAdd(Request $req)
    {
        $this->validate($req, [
            'enabled' => 'required|integer|between:1,2',
            'network_name' => 'required|string|max:20|regex:/^[\w ]+$/',
            'offerwall_description' => 'required|string|between:10,100',
            'web_url' => 'required|string',
			'web_auth' => 'nullable|string',
            'network_image' => 'required|mimes:jpeg,jpg,png|max:1000',
            'postback_type_key' => 'required|integer|between:1,2',
            'postback_exchange' => 'required|integer|between:1,2',
            'postback_url_secret_key' => 'required|string|max:40',
            'postback_reward_amount_key' => 'required|string|max:40|regex:/(=)/',
            'postback_user_id_key' => 'required|string|max:40|regex:/(=)/',
            'postback_offer_id_key' => 'required|string|max:40|regex:/(=)/',
            'postback_ip_address_key' => 'nullable|string|max:40|regex:/(=)/',
            'verify' => 'nullable|string|max:40|regex:/(=)/'
        ]);

        $nn = $req->post('network_name');
        $nn_slug = strtolower(str_replace(' ', '', $nn));
        if (DB::table('postbacks')->where('network_name', $nn)->first()) {
            return back()->withInput()->with('error', 'اسم الشبكة موجود بالفعل');
        }

        $ipParam = $req->post('postback_ip_address_key');
        if ($ipParam == null || strpos($ipParam, '=') === false) {
            $ipParam = 'ip=blank';
        }
		$wAuth = $req->post('web_auth') == null ? '-' : $req->post('web_auth');
        $id = DB::table('offerwalls')->insertGetId([
            'type' => 4,
            'enabled' => $req->post('enabled'),
            'name' => $nn_slug,
            'data' => $req->post('web_url').'@@@'.$wAuth,
            'title' => $nn,
            'description' => $req->post('offerwall_description'),
        ]);
        $path = public_path('uploads');
        $filename = Carbon::now()->timestamp.'.'.$req->file('network_image')->getClientOriginalExtension();
        $req->file('network_image')->move($path, $filename);
        DB::table('postbacks')->insert([
            'offerwall_id' => $id,
            'postback_type' => $req->post('postback_type_key'),
            'exchange' => $req->post('postback_exchange'),
            'network_name' => $nn,
            'network_slug' => $nn_slug . '_a',
            'network_image' => env('APP_URL').'/public/uploads/'.$filename,
            'param_tok' => $req->post('postback_url_secret_key'),
            'param_amount' => $req->post('postback_reward_amount_key'),
            'param_userid' => $req->post('postback_user_id_key'),
            'param_offerid' => $req->post('postback_offer_id_key'),
            'param_ip' => $ipParam,
            'param_verify' => $req->post('verify')
        ]);
        Cache::forget('connect_offers');
        return redirect()->route('networks_web')->with('success', 'تمت إضافة شبكة الإعلانات بنجاح.');
    }

    public function webEdit(Request $req)
    {
        $id = $req->get('id');
        $d = DB::table('offerwalls')->where('id', $id)->first();
        $b = DB::table('postbacks')->where('offerwall_id', $id)->first();
		$urls = explode('@@@', $d->data);
        $data = [
            'id' => $id,
            'enabled' => $d->enabled,
            'web_url' => $urls[0],
            'web_auth' => isset($urls[1]) ? $urls[1] : '',
            'offerwall_description' => $d->description,
            'postback_type_key' => $b->postback_type,
            'postback_exchange' => $b->exchange,
            'network_name' => $b->network_name,
            'image' => $b->network_image,
            'postback_url_secret_key' => $b->param_tok,
            'postback_reward_amount_key' => $b->param_amount,
            'postback_user_id_key' => $b->param_userid,
            'postback_offer_id_key' => $b->param_offerid,
            'postback_ip_address_key' => $b->param_ip,
            'verify' => $b->param_verify
        ];
        return view('admin.networks_web_edit', compact('data'));
    }

    public function webUpdate(Request $req)
    {
        $this->validate($req, [
            'id' => 'required|integer',
            'enabled' => 'required|integer|between:1,2',
            'network_name' => 'required|string|max:20|regex:/^[\w ]+$/',
            'offerwall_description' => 'required|string|between:10,100',
            'web_url' => 'required|string',
			'web_auth' => 'nullable|string',
            'network_image' => 'nullable|mimes:jpeg,jpg,png|max:1000',
            'postback_type_key' => 'required|integer|between:1,2',
            'postback_exchange' => 'required|integer|between:1,2',
            'postback_url_secret_key' => 'required|string|max:40',
            'postback_reward_amount_key' => 'required|string|max:40|regex:/(=)/',
            'postback_user_id_key' => 'required|string|max:40|regex:/(=)/',
            'postback_offer_id_key' => 'required|string|max:40|regex:/(=)/',
            'postback_ip_address_key' => 'nullable|string|max:40|regex:/(=)/',
            'verify' => 'nullable|string|max:40|regex:/(=)/'
        ]);
        $nn = $req->post('network_name');
        $nn_slug = strtolower(str_replace(' ', '', $nn));
        $id = $req->post('id');
        $check = DB::table('postbacks')->where('network_name', $nn)->first();
        if ($check && $id != $check->offerwall_id) {
            return back()->withInput()->with('error', 'اسم الشبكة موجود بالفعل');
        }
        $ipParam = $req->post('postback_ip_address_key');
        if ($ipParam == null || strpos($ipParam, '=') === false) {
            $ipParam = 'ip=blank';
        }
		$wAuth = $req->post('web_auth') == null ? '-' : $req->post('web_auth');
        DB::table('offerwalls')->where('id', $id)->update([
            'enabled' => $req->post('enabled'),
            'name' => $nn_slug,
            'data' => $req->post('web_url').'@@@'.$wAuth,
            'title' => $nn,
            'description' => $req->post('offerwall_description'),
        ]);
        $postback = DB::table('postbacks')->where('offerwall_id', $id);
        $filename = basename($postback->first()->network_image);
        if ($req->hasFile('network_image')) {
            $path = public_path('uploads');
            if ($filename != null && file_exists($path.'/'.$filename)) {
                unlink($path.'/'.$filename);
            };
            $filename = Carbon::now()->timestamp.'.'.$req->file('network_image')->getClientOriginalExtension();
            $req->file('network_image')->move($path, $filename);
        }
        $postback->update([
            'postback_type' => $req->post('postback_type_key'),
            'exchange' => $req->post('postback_exchange'),
            'network_name' => $nn,
            'network_slug' => $nn_slug . '_a',
            'network_image' => env('APP_URL').'/public/uploads/'.$filename,
            'param_tok' => $req->post('postback_url_secret_key'),
            'param_amount' => $req->post('postback_reward_amount_key'),
            'param_userid' => $req->post('postback_user_id_key'),
            'param_offerid' => $req->post('postback_offer_id_key'),
            'param_ip' => $ipParam,
            'param_verify' => $req->post('verify')
        ]);
        Cache::forget('connect_offers');
        return redirect()->route('networks_web')->with('success', 'تم تحديث شبكة الإعلانات بنجاح.');
    }
}