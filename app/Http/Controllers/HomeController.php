<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;
use DB;
use Auth;

class HomeController extends Controller
{
    public function Index()
    {
        if (env('INSTALLED') == 1) {
            return view('welcome');
        } 
    }



    public function Home()
    {
        if (Auth::id() == env('ADMIN')) {
            return redirect()->route('index');
        } else {
            return view('home');
        }
    }

    public function Faq()
    {
        $data = DB::table('support_faq')->get();
        return view('faq', compact('data'));
    }

    public function Terms()
    {
        return view('terms');
    }

    public function Privacy()
    {
        return view('privacy');
    }
	public function appAds()
    {
        $response = \Response::make(\Funcs::getmisc('app_ads'), 200);
        $response->header('Content-Type', 'text/plain');
        return $response;
    }
	public function dataDelete()
    {
        return view('user_delete');
    }
}
