<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class Maintain extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        $currAppVer = env('APP_VERSION');
        $latestAppVer = env('APP_LATESTVERSION');
        $currBackendVer = env('BACKEND_VERSION');
        $latestBackendVer = env('BACKEND_LATESTVERSION');
        
        
        $data = [
            'app' => '<div><span class="font-weight-bold">اصدار نسخة تطبيقك:</span> '.$currAppVer.'</div>',
            'app_update' => '',
            'backend' => '<div><span class="font-weight-bold">اصدار نسختك الحالية:</span> '.$currBackendVer.'</div>',
            'new_backend' => '<div><span class="font-weight-bold">احدث اصدار لنسخة لوحة التحكم:</span> '.$currBackendVer.'</div>',
            'app_ads' => \Funcs::getmisc('app_ads')
        ];
        try {
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, '');
            $result = curl_exec($ch);
            curl_close($ch);
            $obj = json_decode($result);
            
            if (is_numeric($currAppVer) && $currAppVer < $latestAppVer) {
                $data['app'] = '<div class="text-danger"><span class="font-weight-bold">اصدار تطبيقك الحالية:</span> '.$currAppVer.' (outdated)</div>';
                $data['app_update'] = '<a href="' . route('app_update', ['ver' =>  $latestAppVer, 'type' => 1]) . '" class="btn btn-sm btn-primary">تحقق من احدث اصدار للتطبيق</a>';
            }
            if (is_numeric($currBackendVer) && $currBackendVer < $latestBackendVer) {
                $data['backend'] = '<div class="text-danger"><span class="font-weight-bold">اصدار لوحة التحكم الحالية</span> '.$currBackendVer.' 
				<span class="small ml-2 text-blue font-italic">[<a href="https://content.mintsoft.org/updates" target="_blank">تنزيل البيانات</a>]</span></div>
                <a href="' . route('app_update', ['ver' => $latestBackendVer, 'type' => 2]) . '" class="btn btn-sm btn-success">ساحدث لوحة التحكم الخاصة بي</a>';
                $data['new_backend'] = '<div class="text-success"><span class="font-weight-bold">احدث اصدار من لوحة التحكم:</span> '.$latestBackendVer.'</div>';
            }
        } catch (\Exception $e) {
        }

        $tos = resource_path('views')."/terms.blade.php";
        try {
            $p = file_get_contents(resource_path('views')."/privacy.blade.php");
            $privacy = str_replace(["@extends('privacy_inc') @section('privacy')\r\n","\r\n@endsection"], '', $p);
            $t = file_get_contents(resource_path('views')."/terms.blade.php");
            $terms = str_replace(["@extends('terms_inc') @section('terms')\r\n","\r\n@endsection"], '', $t);
        } catch (\Exception $e) {
            $privacy = 'لا يمكن تحميل الملف. تأكد "resources\views\privacy.blade.php" حصلت على إذن القراءة والكتابة الكامل (0777)';
            $terms = 'لا يمكن تحميل الملف. تأكد "resources\views\terms.blade.php" حصلت على إذن القراءة والكتابة الكامل (0777)';
        }
        $data['tos'] = $terms;
        $data['privacy'] = $privacy;
        return view('admin.maintain', compact('data'));
    }

    public function appUpdate(Request $req)
    {
        $this->validate($req, [
            'type' => 'required|integer|between:1,2',
            'ver' => 'required|numeric'
        ]);
        $required_version = 1.28;
        $updated_version = 1.29;
        if ($req->type == 2) {
            if (env('BACKEND_VERSION') == $required_version) {
                try {
                    // Updating code start
                    // .....
                    // Updating code end
                } catch (\Exception $e) {
                    return back()->with('error', 'Update error: '.$e->getMessage());
                }
                \Funcs::setEnv('BACKEND_VERSION', $updated_version);
            } else {
                return back()->with('error', 'النسخة الخلفية ' . $required_version . ' مطلوب لعملية التحديث هذه.');
            }
        } else {
            \Funcs::setEnv('APP_VERSION', $req->get('ver'));
        }
        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function userUpdate(Request $req){
        $this->validate($req, [
            'update_type' => 'required|integer|between:0,1',
            'versioncode' => 'required|integer'
        ]);
        \Funcs::setEnv('USER_VERSIONCODE', $req->post('versioncode'), false);
        \Funcs::setEnv('USER_FORCE_UPDATE', $req->post('update_type'));
        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function faq()
    {
        $faq = DB::table('support_faq')->paginate(5);
        return view('admin.faq', compact('faq'));
    }
    
    public function faqAdd(Request $req)
    {
        $this->validate($req, [
            'faq_question' => 'required|string|min:10',
            'faq_answer' => 'required|string|min:10'
        ]);
        DB::table('support_faq')->insert(['question' => $req->post('faq_question'), 'answer' => $req->post('faq_answer')]);
        return back()->with('success', 'تمت إضافة الأسئلة الشائعة بنجاح.');
    }

    public function faqDel(Request $req)
    {
        DB::table('support_faq')->where('id', $req->get('id'))->delete();
        return back()->with('success', 'تم حذف الأسئلة الشائعة بنجاح.');
    }

    public function tosUpdate(Request $req)
    {
        try {
            $path = resource_path('views')."/terms.blade.php";
            $tos = "@extends('terms_inc') @section('terms')\r\n" . $req->post('tos') . "\r\n@endsection";
            file_put_contents($path, $tos);
            return back()->with('success', 'تم تحديث الشروط بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'تأكد "resources\views\terms.blade.php" حصلت على إذن القراءة والكتابة الكامل (0777)');
        }
    }

    public function privacyUpdate(Request $req)
    {
        try {
            $path = resource_path('views')."/privacy.blade.php";
            $tos = "@extends('privacy_inc') @section('privacy')\r\n" . $req->post('privacy') . "\r\n@endsection";
            file_put_contents($path, $tos);
            return back()->with('success', 'تم تحديث سياسة الخصوصية بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'تاكد "resources\views\privacy.blade.php" حصلت على إذن القراءة والكتابة الكامل (0777)');
        }
    }
	
	public function appAdsUpdate(Request $req)
    {
        $data = $req->post('data') == null ? '' : $req->post('data');
        \Funcs::setmisc('app_ads', $data);
        return back()->with('success', 'تم التحديث بنجاح.');
    }
}
