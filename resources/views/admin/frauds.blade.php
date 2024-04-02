@extends('layouts.head')
@section('css')
<style>
    .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.5);
        color: #ffffff;
        padding: 0px 5px;
    }

</style>
@endsection
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
<div class="row">
    <div class="col-12">
        <form class="card" method="post" action="{{route('frauds_update')}}">
            @csrf
            <div class="card-header bg-blue-lt pt-3 pb-2">
                <h4 class="text-dark">الحماية من البريد المزعج</h4>
            </div>
            <div class="card-body">
                <div class="row mt-2">
                    <div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">هل يمكن لأي شخص التسجيل عن طريق تطبيق الهاتف المحمول؟</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item text-no-wrap">
                                    <input type="radio" name="registration_disable" value="2" class="form-selectgroup-input" {{ env('REG_DISABLED') == '1' ? '' : 'checked' }}>
                                    <span class="form-selectgroup-label">نعم</span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="registration_disable" value="1" class="form-selectgroup-input" {{ env('REG_DISABLED') == '1' ? 'checked' : '' }}>
                                    <span class="form-selectgroup-label">لا</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">التسجيل يتطلب التحقق من البريد الإلكتروني؟</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item text-no-wrap">
                                    <input type="radio" name="registration_validation" value="1" class="form-selectgroup-input" {{ env('REG_VALIDATION') == '1' ? 'checked' : '' }}>
                                    <span class="form-selectgroup-label">نعم</span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="registration_validation" value="2" class="form-selectgroup-input" {{ env('REG_VALIDATION') == '1' ? '' : 'checked' }}>
                                    <span class="form-selectgroup-label">لا</span>
                                </label>
                            </div>
                        </div>
                    </div>
					<div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">منع التسجيل باستخدام البريد الإلكتروني القابل للتصرف؟</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item text-no-wrap">
                                    <input type="radio" name="disposable_email" value="1" class="form-selectgroup-input" {{ env('DISPOSABLE_CHECK') == '1' ? 'checked' : '' }}>
                                    <span class="form-selectgroup-label">نعم</span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="disposable_email" value="2" class="form-selectgroup-input" {{ env('DISPOSABLE_CHECK') == '1' ? '' : 'checked' }}>
                                    <span class="form-selectgroup-label">لا</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">التسجيل دعوة فقط؟</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item text-no-wrap">
                                    <input type="radio" name="invitation_only" value="1" class="form-selectgroup-input" {{ env('REG_INVITATION_ONLY') == '2' ? '' : 'checked' }}>
                                    <span class="form-selectgroup-label">نعم</span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="invitation_only" value="2" class="form-selectgroup-input" {{ env('REG_INVITATION_ONLY') == '2' ? 'checked' : '' }}>
                                    <span class="form-selectgroup-label">لا</span>
                                </label>
                            </div>
                        </div>
                    </div>
					<div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">حظر المحاكيات <sup class="text-red">beta</sup></label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item text-no-wrap">
                                    <input type="radio" name="block_emu" value="1" class="form-selectgroup-input" {{ env('EMULATOR_DETECT') == '1' ? 'checked' : '' }}>
                                    <span class="form-selectgroup-label">نعم</span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="block_emu" value="2" class="form-selectgroup-input" {{ env('EMULATOR_DETECT') == '1' ? '' : 'checked' }}>
                                    <span class="form-selectgroup-label">لا</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">كم عدد التسجيلات التي سيتم قبولها في الساعة؟</label>
                            <input type="text" class="form-control" name="registration_limit_per_hour" value="{{env('REG_LIMIT_PER_HR') > 0 ? env('REG_LIMIT_PER_HR') : 'unlimited'}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">تحديث الحماية</button>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <form class="card" method="post" action="{{route('frauds_update')}}">
            @csrf
            <div class="card-header bg-gray-lt pt-3 pb-2">
                <h4 class="text-dark">منع الغش</h4>
            </div>
            <div class="card-body">
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="single_account" value="1" class="form-selectgroup-input" @if(env('SINGLE_ACCOUNT')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/single_account.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">حساب واحد لكل جهاز</div>
                                <div class="h5 text-muted text-right">لا تسمح للمستخدمين بفتح أكثر من حساب واحد من الجهاز.</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="vpn_block" value="1" class="form-selectgroup-input" @if(env('VPN_BLOCK')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/block_vpn.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">منع الوصول إلى VPN</div>
                                <div class="h5 text-muted text-right">لا تسمح للمستخدم بفتح العروض باستخدام VPN.</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="vpn_monitor" value="0" class="form-selectgroup-input" @if(env('VPN_MONITOR')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/monitor_vpn.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">مراقبة الوصول إلى VPN</div>
                                <div class="h5 text-muted text-right">اكتشف بصمت عدد المرات التي حاول فيها المستخدمون استخدام VPN.</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="root_block" value="1" class="form-selectgroup-input" @if(env('ROOT_BLOCK')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/block_rooted.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">حظر الجهاز الجذور</div>
                                <div class="h5 text-muted text-right">لن يعمل التطبيق على الجهاز الجذر إذا تم تنشيط هذا الخيار.</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="auto_ban_multi" value="1" class="form-selectgroup-input" @if(env('AUTO_BAN_MULTI')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/auto_ban_multi.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">الحظر التلقائي لحسابات متعددة</div>
                                <div class="h5 text-muted text-right">الحظر التلقائي لمن يحاول إنشاء حسابات متعددة.</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="auto_ban_vpn" value="1" class="form-selectgroup-input" @if(env('AUTO_BAN_VPN')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/auto_ban_vpn.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">الحظر التلقائي لمن يحاول إنشاء حسابات متعددة.</div>
                                <div class="h5 text-muted text-right">الحظر التلقائي لمن يحاول استخدام اتصال VPN في العروض</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="auto_ban_root" value="1" class="form-selectgroup-input" @if(env('AUTO_BAN_ROOT')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/auto_ban_rooted.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">حظر تلقائي للجهاز الجذر</div>
                                <div class="h5 text-muted text-right">الحظر التلقائي للحساب الذي يستخدم الجهاز الجذر</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="ban_cc_change" value="1" class="form-selectgroup-input" @if(env('BAN_CC_CHANGE')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/cc_change.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">الحظر التلقائي لتغيير البلد</div>
                                <div class="h5 text-muted text-right">الحظر التلقائي لمستخدم الحساب من الوصول إلى التطبيق من بلد مختلف</div>
                            </div>
                        </div>
                    </div>
                </label>
				<label class="form-selectgroup-item flex-fill">
                    <input type="checkbox" name="prv_acc_del" value="1" class="form-selectgroup-input" @if(env('PRV_ACC_DEL')==1) checked @endif>
                    <div class="form-selectgroup-label d-flex align-items-center pl-3 pr-3 pt-1 pb-1 p-3 mb-1">
                        <div class="mr-3">
                            <span class="form-selectgroup-check"></span>
                        </div>
                        <div class="form-selectgroup-label-content d-flex align-items-center">
                            <span class="avatar rounded ml-3" style="background-image: url(/public/img/user_del.png)"></span>
                            <div class="lh-sm">
                                <div class="strong text-right">حذف الحسابات القديمة تلقائيًا</div>
                                <div class="h5 text-muted text-right">سيتم حذف الحساب القديم إذا قام المستخدم بإنشاء حساب جديد.</div>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">تحديث منع الاحتيال</button>
            </div>
        </form>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header pt-3 pb-2">
                <h4>مراقبة الوصول إلى VPN</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    @foreach($data as $d)
                    <div class="col-6">
                        <div class="card card-body">
                            <a class="row align-items-center" href="{{route('userinfo', ['userid' => $d->userid])}}">
                                <div class="col-auto">
                                    <span class="avatar avatar-md rounded" style="background-image: url({{$d->avatar}})"></span>
                                </div>
                                <div class="col text-truncate">
                                    <span class="text-body d-block text-truncate">{{$d->name}}</span>
                                    <small class="d-block text-muted text-truncate mt-0">Attempted {{$d->attempted}} times</small>
                                </div>
                            </a>
                            <a href="{{route('frauds_clear', ['id' => $d->id])}}" class="btn-close">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" />
                                    <line x1="4" y1="7" x2="20" y2="7" />
                                    <line x1="10" y1="11" x2="10" y2="17" />
                                    <line x1="14" y1="11" x2="14" y2="17" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">عرض <span>{{$data->firstItem()}}</span> ل <span>{{$data->lastItem()}}</span> من <span>{{$data->total()}}</span> المدخلات</p>
                <ul class="pagination m-0 ml-auto">
                    {{ $data->appends(request()->except('page'))->links() }}
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
@section('css')
<link href="/public/css/selectize.css" rel="stylesheet" />
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="/public/js/selectize.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#input-tags').selectize({
            maxItems: 15,
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            create: function (input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
    });

</script>
@endsection