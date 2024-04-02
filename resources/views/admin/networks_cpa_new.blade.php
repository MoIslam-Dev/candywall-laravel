@extends('layouts.head')
@section('css')
<style>
    .bold {
        font-weight: bold !important
    }

</style>
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
@section('content')
<div class="row card">
    <form class="p-0" action="{{route('networks_cpa_add')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card-header bg-dark-lt h3 text-dark bold">اضافة شبكة جديدة من نوع API</div>
        <div class="card-body">
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
                {!!$errors->first()!!}
            </div>
            @endif
            <div class="alert alert-info" role="alert">
                <span class="bold mr-2">الماكروهات المسموح بها من التطبيق هي:</span>
                <span class="text-nowrap mr-2"><span class="text-red">[app_uid]</span> = لمعرف المستخدم</span>
                <span class="text-nowrap mr-2"><span class="text-red">[app_country]</span> = لرمز البلد</span>
                <span class="text-nowrap mr-2"><span class="text-red">[app_ip]</span> = لعنوان IP للمستخدم</span>
                <span class="text-nowrap"><span class="text-red">[app_gaid]</span> = لمعرّف GAID</span>
            </div>
            <div class="row">
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم الشبكة الاعلانية</label>
                    <input type="text" class="form-control" name="network_name" value="{{old('network_name')}}" placeholder="CPALead">
                </div>
                <div class="col col-lg-6 col-md-5 col-sm-6 col-12 mb-3">
                    <label class="form-label">رابط عروض API</label>
                    <input type="text" class="form-control" name="offer_api_url" value="{{old('offer_api_url')}}" placeholder="https://www.cpalead.com/dashboard/reports/campaign_json_load_offers.php?format=json&incentive=y&id=814901&device=android&payout_type=cpi&&country=[app_country]&&aff_sub5=[app_uid]">
                </div>
                <div class="col col-lg-3 col-md-3 col-sm-6 col-12 mb-3">
                    <label class="form-label">راس <small>(ان وجد)</small>:</label>
                    <input type="text" class="form-control" name="offer_api_auth" placeholder="Authorization:Bearer 123478sad6fas878" value="{{old('offer_api_suth')}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Offerwall description:</label>
                    <input type="text" class="form-control" name="offerwall_description" value="{{old('offerwall_description')}}" placeholder="Write a description...">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">نوع العروض</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="offerwall_type" value="1" class="form-selectgroup-input" {{ old('offerwall_type') == '2' ? '' : 'checked' }}>
                            <span class="form-selectgroup-label">عروض CPI</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="offerwall_type" value="2" class="form-selectgroup-input" {{ old('offerwall_type') == '2' ? 'checked' : '' }}>
                            <span class="form-selectgroup-label">عروض CPA</span>
                        </label>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم ل <span class="text-with bold">JSON Array</span> مفتاح:</label>
                    <input type="text" class="form-control" name="json_array_key" value="{{old('json_array_key')}}" placeholder="offers">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم ل <span class="text-with bold">Offer ID</span> مفتاح:</label>
                    <input type="text" class="form-control" name="offer_id_key" value="{{old('offer_id_key')}}" placeholder="campid">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم ل<span class="text-with bold">Offer Title</span> مفتاح:</label>
                    <input type="text" class="form-control" name="offer_title_key" value="{{old('offer_title_key')}}" placeholder="title">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم ل <span class="text-with bold">Offer Description</span> مفتاح:</label>
                    <input type="text" class="form-control" name="offer_description_key" value="{{old('offer_description_key')}}" placeholder="description">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم ل <span class="text-with bold">Reward Amount</span> مفتاح:</label>
                    <input type="text" class="form-control" name="reward_amount_key" value="{{old('reward_amount_key')}}" placeholder="amount">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم ل <span class="text-with bold">Icon URL</span> مفتاح:</label>
                    <input type="text" class="form-control" name="icon_url_key" value="{{old('icon_url_key')}}" placeholder="mobile_app_icon_url">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم ل <span class="text-with bold">Offer URL</span> مفتاح:</label>
                    <input type="text" class="form-control" name="offer_url_key" value="{{old('offer_url_key')}}" placeholder="link">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Offer URL suffix (ان وجد):</label>
                    <input type="text" class="form-control" name="offer_url_suffix" value="{{old('offer_url_suffix')}}" placeholder="&sid=[app_uid]">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <div class="form-label">ايقونة الشبكة:</div>
                    <div class="form-file">
                        <input type="file" name="network_image" class="form-file-input img-input" id="imagefile">
                        <label class="form-file-label" for="customFile">
                            <span class="form-file-text img-choose">اختر صورة...</span>
                            <span class="form-file-button">تصفح</span>
                        </label>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">تفعيل والغاء العروض</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="enabled" value="1" class="form-selectgroup-input" {{ old('enabled') == '2' ? '' : 'checked' }}>
                            <span class="form-selectgroup-label">تفعيل</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="enabled" value="2" class="form-selectgroup-input" {{ old('enabled') == '2' ? 'checked' : '' }}>
                            <span class="form-selectgroup-label">الغاء</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="hr-text mt-4 mb-3 text-blue hr-text-left bold">ضبط واعداد البوست باك Postback</div>
            <div class="row">
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اظهار او اخفاء رابط البوست باك</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="postback_type_key" value="1" class="form-selectgroup-input" {{ old('postback_type_key') == '2' ? '' : 'checked' }}>
                            <span class="form-selectgroup-label">تفعيل</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="postback_type_key" value="2" class="form-selectgroup-input" {{ old('postback_type_key') == '2' ? 'checked' : '' }}>
                            <span class="form-selectgroup-label">اخفاء</span>
                        </label>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">من سيدير سعر الصرف؟</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="postback_exchange" value="1" class="form-selectgroup-input" {{ old('postback_exchange') == '2' ? '' : 'checked' }}>
                            <span class="form-selectgroup-label">لوحة التحكم</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="postback_exchange" value="2" class="form-selectgroup-input" {{ old('postback_exchange') == '2' ? 'checked' : '' }}>
                            <span class="form-selectgroup-label">الشبكة الاعلانية</span>
                        </label>
                    </div>
                </div>
               <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
    <label class="form-label">مفتاح سري لحماية الرابط</label>
    <input type="text" class="form-control" name="postback_url_secret_key" value="{{old('postback_url_secret_key')}}" placeholder="ANY_ALPHANUMERIC_CHARS">
</div>
<div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
    <label class="form-label text-truncate">المعلمة ل <span class="text-with bold">مبلغ الجائزة:</span></label>
    <input type="text" class="form-control" name="postback_reward_amount_key" value="{{old('postback_reward_amount_key')}}" placeholder="payout={payout}">
</div>
<div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
    <label class="form-label">المعلمة ل <span class="text-with bold">معرف المستخدم:</span></label>
    <input type="text" class="form-control" name="postback_user_id_key" value="{{old('postback_user_id_key')}}" placeholder="userid={subid}">
</div>
<div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
    <label class="form-label">المعلمة ل <span class="text-with bold">معرف العرض:</span></label>
    <input type="text" class="form-control" name="postback_offer_id_key" value="{{old('postback_offer_id_key')}}" placeholder="offer_id={campaign_id}">
</div>
<div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
    <label class="form-label">المعلمة ل <span class="text-with bold">عنوان IP:</span></label>
    <input type="text" class="form-control" name="postback_ip_address_key" value="{{old('postback_ip_address_key')}}" placeholder="ip={ip_address}">
</div>
<div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
    <label class="form-label">معلمة التحقق <span class="text-danger h5">(ان وجد)</span>:</label>
    <input type="text" class="form-control" name="verify" value="{{old('verify')}}">
</div>

            <div class="d-flex flex-row-reverse mt-2">
                <input type="submit" class="btn btn-success mr-4" value="حفظ الشبكة" />
                <a href="{{route('networks_cpa')}}" class="btn btn-danger mr-4">الغاء</a>
            </div>
        </div>
    </form>
</div>

@endsection

@section('javascript')
<script>
    $('.img-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-file').find('.img-choose').addClass("selected").text(fileName);
    });

</script>
@endsection
