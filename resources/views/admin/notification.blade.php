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
    {!!$errors->first()!!}
</div>
@endif
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">إرسال رسالة الدفع</h2>
        </div>
    </div>
</div>
<form method="post" class="row" action="{{route('push_msg_send')}}" enctype="multipart/form-data">
    @csrf
    <div class="col-md-6 col-lg-4">
        <div class="mb-3">
            <label class="form-label">من تريد أن ترسل له؟</labell>
            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                <label class="form-selectgroup-item flex-fill">
                    <input type="radio" id="sendtype-1-radio" name="sendtype" value="1" class="form-selectgroup-input" checked>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="lh-sm">
                            <div class="strong mb-2">إرسال إلى مستخدم واحد:</div>
                            <div class="form-check-description">
                                <div class="input-group">
                                    <input id="sendtype-1-input" type="text" name="email_or_userid" class="form-control" @if(isset($data['direct'])) value="{{$data['uid']}}" readonly @else value="{{old('email_or_userid')}}" @endif aria-label="أدخل البريد الإلكتروني أو معرف المستخدم">
                                    <input class="form-control" id="sendtype-1-val" type="hidden" name="email_or_userid_type" value="1">
                                    <button type="button" id="sendtype-1-text" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @if(isset($data['direct'])) readonly @endif>معرف المستخدم</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" id="sendtype-1-opt" data-id="1">بواسطة معرف المستخدم</a>
                                        <a class="dropdown-item" id="sendtype-1-opt" data-id="2">بالبريد الالكتروني</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input id="sendtype-2-radio" type="radio" name="sendtype" value="2" class="form-selectgroup-input" @if(isset($data['direct'])) disabled @endif>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="lh-sm">
                            <div class="strong mb-2">لعدة مستخدمين:</div>
                            <div class="form-check-description">
                                <div class="input-group">
                                    @if(isset($data['direct']))
                                    <span class="input-group-text">من</span>
                                    <input type="text" id="sendtype-2-input" name="sendtype-2-input-from" class="form-control text-center" value="1" disabled>
                                    <span class="input-group-text">ل</span>
                                    <input type="text" id="sendtype-2-input" name="sendtype-2-input-to" class="form-control text-center" value="50" disabled>
                                    <span class="input-group-text">المستخدمين</span>
                                    @else
                                    <span class="input-group-text">من</span>
                                    <input type="text" id="sendtype-2-input" name="sendtype-2-input-from" class="form-control text-center" value="1">
                                    <span class="input-group-text">ل</span>
                                    <input type="text" id="sendtype-2-input" name="sendtype-2-input-to" class="form-control text-center" value="50">
                                    <span class="input-group-text">المستخدمين</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="radio" name="sendtype" value="3" class="form-selectgroup-input" @if(isset($data['direct'])) disabled @endif>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="lh-sm">
                            <div class="strong">المستخدمين المحظورين</div>
                            <div class="form-check-description">آخر 50 مستخدمًا محظورًا ولم يحذفوا التطبيق بعد.</div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="radio" name="sendtype" value="4" class="form-selectgroup-input" @if(isset($data['direct'])) disabled @endif>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                           <div class="lh-sm">
                                <div class="strong">الفائزين المتصدرين</div>
                                <div class="form-check-description">ارسال رسالة للمتصدرين الفائزين</div>
                            </div>
                    </div>
                </label>
				<label class="form-selectgroup-item flex-fill">
                    <input type="radio" name="sendtype" value="5" class="form-selectgroup-input" @if(isset($data['direct'])) disabled @endif>
                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="lh-sm">
                            <div class="strong">لجميع المستخدمين</div>
                            <div class="form-check-description">إرسال رسالة إلى جميع المستخدمين المشتركين.</div>
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-8">
        <label class="form-label">عنوان الرسالة:</label>
        <div class="input-group mb-3">
            <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <path d="M4 21v-13a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-9l-4 4"></path>
                    <line x1="8" y1="9" x2="16" y2="9"></line>
                    <line x1="8" y1="13" x2="14" y2="13"></line>
                </svg>
            </span>
            <input type="text" class="form-control" name="title" value="{{old('title')}}" placeholder="أدخل عنوانا...">
        </div>
        <div class="col-12 d-flex mb-3">
            <div class="col-6">
                <div class="form-label">نوع الرسالة</div>
                <div class="form-selectgroup mr-3">
                    <label class="form-selectgroup-item">
                        <input type="radio" name="text_or_multi" value="1" class="form-selectgroup-input" {{ old('text_or_multi', '1') == '1' ? 'checked' : '' }}>
                        <span class="form-selectgroup-label">نص</span>
                    </label>
                    <label class="form-selectgroup-item">
                        <input type="radio" name="text_or_multi" value="2" class="form-selectgroup-input" {{ old('text_or_multi', '1') == '2' ? 'checked' : '' }}>
                        <span class="form-selectgroup-label">صورة كبيرة</span>
                    </label>
                    <label class="form-selectgroup-item">
                        <input type="radio" name="text_or_multi" value="3" class="form-selectgroup-input" {{ old('text_or_multi', '1') == '3' ? 'checked' : '' }}>
                        <span class="form-selectgroup-label">صورة صغيرة</span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-label">لرسائل الوسائط المتعددة</div>
                <div class="form-file">
                    <input type="file" name="multimedia_image" class="form-file-input img-input" id="imagefile">
                    <label class="form-file-label" for="customFile">
                        <span class="form-file-text img-choose">اختر صورة...</span>
                        <span class="form-file-button">تصفح</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group mb-3">
            <div class="form-label">:نص الرسالة</div>
            <textarea class="form-control" name="message" data-toggle="autosize" placeholder="كتابة شيء ما..." style="height:150px;">{{old('message')}}</textarea>
            <button id="form-submit" type="submit" class="btn btn-success mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3"></path>
                </svg>
                @if (\Session::has('success'))
                Send another
                @else
                ارسال رسالة
                @endif
            </button>
        </div>
    </div>
</form>
@endsection

@section('css')
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
@endsection
@section('javascript')
<script>
    $(document).on("click", "#sendtype-1-input", function () {
        $('input:radio[id=sendtype-1-radio]').click();
    });
    $(document).on("click", "#sendtype-2-input", function () {
        $('input:radio[id=sendtype-2-radio]').click();
    });
    $(document).on("click", "#sendtype-1-opt", function () {
        var id = $(this).data('id');
        if (id == 1) {
            $("#sendtype-1-text").text('User ID');
            $("#sendtype-1-val").val('1');
        } else {
            $("#sendtype-1-text").text('Email');
            $("#sendtype-1-val").val('2');
        }
    });
    $('.img-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).closest('.form-file').find('.img-choose').addClass("selected").text(fileName);
    });

</script>
@endsection
