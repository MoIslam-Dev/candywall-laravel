@extends('layouts.head')
@section('content')
@if (\Session::has('success'))
<div class="alert alert-success alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {!! \Session::get('success') !!}
</div>
@elseif (\Session::has('error'))
<div class="alert alert-danger alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {!! \Session::get('error') !!}
</div>
@elseif ($errors->any())
<div class="alert alert-warning alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{$errors->first()}}
</div>
@endif
<div class="row">
    <form class="card" action="{{route('settings_update')}}" method="post">
        @csrf
        <div class="card-header d-flex pt-3 pb-1">
            <div class="h3 font-weight-bold">اعدادات النظام</div>
            
        </div>
        <div class="card-body">
            <div class="row mt-2">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">اسم لوحة التحكم:</label>
                        <input type="text" class="form-control" name="backend_name" value="{{env('APP_NAME')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">رابط لوحة التحكم:</label>
                        <input type="text" class="form-control" name="backend_url" value="{{env('APP_URL')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">مفتاح تشفير لوحة التحكم:</label>
                        <input type="text" class="form-control" name="enc_key" value="{{env('ENC_KEY')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">تصحيح أخطاء التطبيق:<span class="small text-danger mx-2">[ ابقه معطلا ]</span></label>
                        <div class="form-selectgroup">
                            <label class="form-selectgroup-item text-no-wrap">
                                <input type="radio" name="debug" value="1" class="form-selectgroup-input" {{ env('APP_DEBUG') == 'true' ? 'checked' : '' }}>
                                <span class="form-selectgroup-label">مفعل</span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="debug" value="0" class="form-selectgroup-input" {{ env('APP_DEBUG') == 'true' ? '' : 'checked' }}>
                                <span class="form-selectgroup-label">ملغى</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12">
                    <div class="mb-3">
                        <label class="form-label">معرف الفاير بيز  [server key Firebase]:</label>
                        <input type="text" class="form-control" name="fcm_key" value="{{env('FCM_SERVER_KEY')}}">
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12">
                    <div class="mb-3">
                        <label class="form-label text-green">معرف اب فلايرز  [Appsflyer Dev Key]:</label>
                        <input type="text" class="form-control" name="dev_key" value="{{$data['dev_key']}}">
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12">
                    <div class="mb-3">
                        <label class="form-label text-green">المبلغ للتحفيز  [Appsflyer event]:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="apf_event_reward" value="{{$data['apf_reward']}}">
                            <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="hr-text my-4 text-cyan">تفاعلات التطبيق</div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">اسم العملة داخل التطبيق:</label>
                        <input type="text" class="form-control" name="currency_name" value="{{env('CURRENCY_NAME')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">ما يعادله بالدولار الأمريكي:</label>
                        <input type="text" class="form-control" name="usd-eq" value="{{env('USD_EQ')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">بكم 1 {{env('USD_EQ')}}؟ </label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="cash_to_points" value="{{env('CASHTOPTS')}}">
                            <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">حصة الربح من العروض:</label>
                        <div class="input-group">
                            <span class="input-group-text">أنت تدفع للمستخدم</span>
                            <input type="text" class="form-control" name="pay_percent" value="{{env('PAY_PCT')}}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">من أحال المستخدم:</label>
                        <div class="input-group">
                            <span class="input-group-text">سوف تتلقى</span>
                            <input type="text" class="form-control" name="pay_referral" value="{{env('REF_LINK_REWARD')}}">
                            <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">من أدخل رمز الإحالة:</label>
                        <div class="input-group">
                            <span class="input-group-text">سوف تتلقى</span>
                            <input type="text" class="form-control" name="pay_referred" value="{{env('REF_USER_REWARD')}}">
                            <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">إشعار التحذير:</label>
                        <div class="form-selectgroup">
                            <label class="form-selectgroup-item text-no-wrap">
                                <input type="radio" name="earning_notification" value="1" class="form-selectgroup-input" {{ env('EARNING_NOTIFICATION') == '1' ? 'checked' : '' }}>
                                <span class="form-selectgroup-label">مفعل</span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="earning_notification" value="0" class="form-selectgroup-input" {{ env('EARNING_NOTIFICATION') == '1' ? '' : 'checked' }}>
                                <span class="form-selectgroup-label">ملغي</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">الفاصل الزمني لمزامنة الرصيد:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="balance_interval" value="{{env('BALANCE_INTERVAL')}}">
                            <span class="input-group-text">ثواني</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hr-text my-4 text-primary">نظام التصنيف اليومي للمتصدرين</div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">مكافأة تصنيف المتصدرين:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="leaderboard_reward" value="{{env('LEADERBOARD_REWARD')}}">
                            <span class="input-group-text">{{strtolower(env('CURRENCY_NAME'))}}s في اليوم</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">كم عدد المستخدمين الذين سيتم تصنيفهم؟</label>
                        <div class="input-group">
                            <input id='lbl' type="text" class="form-control" value="{{env('LEADERBOARD_LIMIT')}}" readonly="">
                            <span class="input-group-text">المستخدمين يوميا</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success">تحديث إعدادات النظام</button>
        </div>
    </form>
	<form class="card mt-3" action="{{route('email_update')}}" method="post">
        @csrf
        <div class="card-header d-flex pt-3 pb-1">
            <div class="h3 font-weight-bold">إعداد SMTP لنظام تسليم البريد الإلكتروني</div>
        </div>
        <div class="card-body">
            <div class="row mt-2">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">Host:</label>
                        <input type="text" class="form-control" name="host" value="{{env('MAIL_HOST')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">Port:</label>
                        <input type="text" class="form-control" name="port" value="{{env('MAIL_PORT')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">Username:</label>
                        <input type="text" class="form-control" name="username" value="{{env('MAIL_USERNAME')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">Password:</label>
                        <input type="text" class="form-control" name="password" value="{{env('MAIL_PASSWORD')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">Encryption method:</label>
                        <input type="text" class="form-control" name="encryption" value="{{env('MAIL_ENCRYPTION')}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">From address:</label>
                        <input type="text" class="form-control" name="from_address" value="{{env('MAIL_FROM_ADDRESS')}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">قم بتحديث إعدادات SMTP</button>
			
        </div>
		
    </form>
	<div class="ml-auto pb-2"><a class="btn btn-sm btn-danger text-white" data-toggle="modal" data-target="#cat-cache" data-backdrop="static" data-keyboard="false">مسح الذاكرة الخفية للنظام</a></div>
</div>
<form method="post" action="{{route('clear_system')}}" class="modal modal-blur fade" id="cat-cache" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">هل أنت متأكد؟</div>
                <div>أنت على وشك إعادة تعيين ذاكرة التخزين المؤقت للنظام. قد يتأثر المستخدمون المباشرون بهذا الإجراء مثل مسابقة المسابقة المستمرة. تأكد من عدم وجود أي لعبة حية مستمرة.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">الغاء</button>
                <button type="submit" class="btn btn-danger">نعم,قم بتنظيفهt</button>
            </div>
        </div>
    </div>
</form>
<form method="post" action="{{route('clear_system')}}" class="modal modal-blur fade" id="lbl-modal" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">الترتيب اليومي للمتصدرين</div>
            <div id='modal-err'></div>
            <div id="lbl-content" class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">الغاء</button>
                <button id="modal-submit" type="submit" class="btn btn-primary">تقديم</button>
            </div>
        </div>
    </div>
	
</form>
@endsection
@section('css')
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
@section('javascript')
<script>
    var can_submit = 0;
    var element = document.getElementById("lbl-content");
    $(document).on("click", "#lbl", function (ev) {
        ev.preventDefault();
        can_submit = 0;
        $("#lbl-modal").modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $("#modal-err").html('');
        $("#modal-submit").text('Submit');
        element.innerHTML =
            '<div class="mb-3">' +
            '<label class="form-label">How many users will be ranked?</label>' +
            '<div class="input-group">' +
            '<input id="modal-limit" type="text" class="form-control" name="balance_interval">' +
            '<span class="input-group-text">users</span>' +
            '</div>' +
            '</div>';
    });

    $(document).on("input", "#modal-limit", function () {
        this.value = Number(this.value.replace(/\D/g, ''));
    });
    var limit = 0;
    $(document).on("click", "#modal-submit", function (ev) {
        ev.preventDefault();
        if (can_submit == 1) {
            var dta = {};
            dta['limit'] = limit;
            for (var i = 0; i < limit; i++) {
                var key = 'rank_' + (i + 1);
                dta[key] = $("#" + key).val();
            }
            $.ajax({
                type: 'GET',
                url: '{{route("settings_update_lb")}}',
                data: dta,
                success: function (data) {
                    if (data.status == 1) {
                        $("#modal-err").html('');
                        element.innerHTML = '<div class="text-center">تم التحديث بنجاح.</div>';
                        can_submit = 2;
                        $("#modal-submit").text('Close dialog');
                        $("#lbl").val(limit);
                    } else {
                        $("#modal-err").html('<div class="alert alert-danger mx-3">' + data.message + '</div>');
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });
        } else if (can_submit == 0) {
            limit = $("#modal-limit").val();
            if (limit == 0) return;
            var htmls = '<input type="hidden" name="limit" value="' + limit + '">';
            for (var i = 0; i < limit; i++) {
                htmls += addInput(i + 1);
            }
            element.innerHTML = htmls;
            can_submit = 1;
        } else if (can_submit == 2) {
            $("#lbl-modal").modal('hide');
        }
    });

    function addInput(id) {
        return '<div class="mb-3">' +
            '<label class="form-label">Percentage for rank ' + id + '</label>' +
            '<div class="input-group">' +
            '<input id="rank_' + id + '" type="text" class="form-control" name="balance_interval">' +
            '<span class="input-group-text">percent</span>' +
            '</div>' +
            '</div>';
    }

</script>
@endsection
