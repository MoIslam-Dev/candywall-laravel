<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('loginerror', function (Request $req) {
    return ['status' => -1, 'message' => 'You need to login first!'];
})->name('apiloginerror');

Route::group(['middleware' => 'api'], function () {
    Route::get('/geo', 'Connect@geo');
    Route::post('/connect', 'Connect@check');
    Route::post('/login', 'Auth\APILoginController@login');
    Route::post('/flogin', 'Auth\APILoginController@fLogin');
    Route::post('/glogin', 'Auth\APILoginController@gLogin');
    Route::post('/plogin', 'Auth\APILoginController@pLogin');
    Route::post('/register', 'Auth\RegisterController@apiCreate');
    Route::post('/forget', 'Auth\ResetPass@apiReset');

    // Postbacks
    Route::get('pb/{secret}/{net}', 'Postback@cpa')->where('net', '^[\w ]+$');
    Route::post('pb/{secret}/{net}', 'Postback@cpa')->where('net', '^[\w ]+$');
    Route::get('cpb', 'Postback@premiumUrlOffers');
    Route::post('cpb', 'Postback@premiumUrlOffers');
    Route::post('/support/tos', 'User\Misc@tos');

    //Route::get('/game/slot/test', 'User\Slot@post');
    //Route::get('/game/slot/get', 'User\Slot@get');
});
Route::group(['middleware' => ['api', 'securedAPI']], function () {
    Route::post('/me/info', 'User\Userinfo@info');
    Route::post('/me/fid', 'User\Userinfo@fid');
    Route::post('/me/balance', 'User\Userinfo@balance');
	Route::post('/me/del', 'User\Userinfo@delAcc');
    Route::post('/profile', 'User\Userinfo@profile');
    Route::post('/ref', 'User\Userinfo@refView');
    Route::post('/abnr', 'User\Userinfo@autobanRoot');
    Route::post('/vm', 'User\Userinfo@vpnMonitor');
    Route::post('/profile/update', 'User\Userinfo@profileChange');
	Route::post('/profile/update/avatar', 'User\Userinfo@avatarChange');
	Route::post('/updatepassword', 'User\Userinfo@passChange');
	Route::post('/refcheck', 'User\Userinfo@refCheck');
	Route::post('/changename', 'User\Userinfo@changeName');
	Route::post('/glomsg', 'User\Userinfo@globalMsg');
	Route::post('/devkey', 'User\Userinfo@devKey');
	Route::post('/apf', 'User\Userinfo@apf');
	
    Route::post('/offers/live', 'User\Offers@liveoffers');
    Route::post('/offers/premium', 'User\Offers@servePremium');
    Route::post('/offers/ppv', 'User\Offers@servePpv');
	Route::post('/offers/rppv', 'User\Offers@rewardPpv');
    Route::post('/offers/yt', 'User\Offers@serveYt');
    Route::post('/offers/yt/reward', 'User\Offers@rewardYt');
    Route::post('/offers/cpa/done', 'User\Offers@completedCpa');
    Route::post('/offers/drwd', 'User\Offers@playReward');
    Route::post('/rank', 'User\Misc@ranking');
    

    //Support
    Route::post('/support/get', 'User\Support@get');
    Route::post('/support/post', 'User\Support@post');
    Route::post('/support/faq', 'User\Misc@faq');

    //Withdrawals
    Route::post('/gift/get', 'User\Withdrawal@get');
    Route::post('/gift/post', 'User\Withdrawal@post');

    //History
    Route::post('/history/withdrawal', 'User\Userinfo@withdrawalfHistory');
    Route::post('/history/ref', 'User\Userinfo@refHistory');
    
    //Chat
	Route::post('/getchat', 'User\Chat@get');
	Route::post('/getnewchat', 'User\Chat@getNew');
	Route::post('/postmessage', 'User\Chat@postMessage');
	Route::post('/postimageaoudio', 'User\Chat@postImageAoudio');
	Route::get('/chat/media/{name}', 'User\Chat@serveAttachment');
    
    //Games
    Route::post('/game/ar/get', 'User\Userinfo@arGet');
    Route::post('/game/ar/reward', 'User\Userinfo@arReward');

 
});
