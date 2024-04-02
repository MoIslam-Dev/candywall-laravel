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
    <form class="p-0" action="{{route('networks_sdk_update')}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$data['id']}}">
        <div class="card-header bg-dark-lt h3 text-dark bold pt-2 pb-2">
            <img src="{{$data['image']}}" class="rounded text-truncate img-thumbnail text-small avatar-md mr-2" alt="{{$data['network_name']}}">
            تحديث الشبكة {{$data['network_name']}}
        </div>
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
            <div class="row">
                <div class="col col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اسم الشبكة:</label>
                    <input type="text" class="form-control" name="network_name" value="{{old('network_name', $data['network_name'])}}">
                </div>
                <div class="col col-md-8 col-sm-6 col-12 mb-3">
                    <label class="form-label">وصف جدار العرض:</label>
                    <input type="text" class="form-control" name="offerwall_description" value="{{old('offerwall_description', $data['offerwall_description'])}}">
                </div>
                <div class="col col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">سبيكة الشبكة</small>:</label>
                    <input type="text" class="form-control" value="{{$data['network_slug']}}" readonly>
                </div>
                <div class="col col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">تفعيل والغاء تفعيل الشبكىة</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="enabled" value="1" class="form-selectgroup-input" {{ old('enabled') == '2' ? '' : ($data['enabled'] == '2' ? '' : 'checked') }}>
                            <span class="form-selectgroup-label">تفعيل</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="enabled" value="2" class="form-selectgroup-input" {{ old('enabled') == '2' ? 'checked' : ($data['enabled'] == '2' ? 'checked' : '') }}>
                            <span class="form-selectgroup-label">الغاء</span>
                        </label>
                    </div>
                </div>

                @foreach($data['extra'] as $d)
                <div class="row mb-3">
                    <div class="col-6">
                        <input type="text" class="form-control" value="{{$d['name']}}" readonly>
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="values[]" value="{{$d['value']}}">
                    </div>
                </div>
                @endforeach
            </div>
              <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <div class="form-label">ايقونة الشبكة</div>
                    <div class="form-file">
                        <input type="file" name="network_image" class="form-file-input img-input" id="imagefile">
                        <label class="form-file-label" for="customFile">
                            <span class="form-file-text img-choose">اختر صورة</span>
                            <span class="form-file-button bg-success mr-1">تصفح المستعرض</span>
                        </label>
                    </div>
                </div>
            <div class="hr-text mt-4 mb-3 text-blue hr-text-left bold">ضبط واعداد البوست باك Postback</div>
            <div class="row">
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">اظهار واخفاء معلمات البوست باك من الرابط</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="postback_type_key" value="1" class="form-selectgroup-input" {{ $data['postback_type_key'] == '2' ? '' : 'checked' }}>
                            <span class="form-selectgroup-label">اظهار</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="postback_type_key" value="2" class="form-selectgroup-input" {{ $data['postback_type_key'] == '2' ? 'checked' : '' }}>
                            <span class="form-selectgroup-label">اخفاء</span>
                        </label>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">من سيدير سعر الصرف؟</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="postback_exchange" value="1" class="form-selectgroup-input" {{ $data['postback_exchange'] == '2' ? '' : 'checked' }}>
                            <span class="form-selectgroup-label">لوحة التحكم</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="postback_exchange" value="2" class="form-selectgroup-input" {{ $data['postback_exchange'] == '2' ? 'checked' : '' }}>
                            <span class="form-selectgroup-label">الشبكة الاعلانية</span>
                        </label>
                    </div>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">مفتاح عشوائي لحماية رابط البوست باك</label>
                    <input type="text" class="form-control" name="postback_url_secret_key" value="{{old('postback_url_secret_key', $data['postback_url_secret_key'])}}">
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label text-truncate">معلمة ل <span class="text-with bold">Reward Amount:</span></label>
                    <input type="text" class="form-control" value="{{$data['postback_reward_amount_key']}}" readonly>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">معلمة ل <span class="text-with bold">User ID:</span></label>
                    <input type="text" class="form-control" value="{{$data['postback_user_id_key']}}" readonly>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">معلمة ل <span class="text-with bold">Offer ID:</span></label>
                    <input type="text" class="form-control" value="{{$data['postback_offer_id_key']}}" readonly>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">معلمة ل <span class="text-with bold">IP address:</span></label>
                    <input type="text" class="form-control" value="{{$data['postback_ip_address_key']}}" readonly>
                </div>
                <div class="col col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">معلمة التحقق <span class="text-danger h5">(ان وجد)</span>:</label>
                    <input type="text" class="form-control" name="verify" value="{{$data['verify']}}">
                </div>
            </div>
            <div class="d-flex mt-2">
                <input type="submit" class="btn btn-success" value="تحديث الشبكة" />
                <a href="{{$data['back']}}" class="btn btn-white mr-4">الغاء</a>
            </div>
        </div>
    </form>
</div>

@endsection

@section('javascript')
<script>
    function closeMe(element) {
        $(element).parent().remove();
    }

    function addMore() {
        var container = $('#list');
        var item = container.find('.default').clone();
        item.removeClass('default');
        item.appendTo(container).show();
    }

</script>
@endsection
