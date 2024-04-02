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
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="px-3 py-3 bg-blue-lt">
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($data['userinfo']->avatar != '')
                        <span class="avatar avatar-xl avatar-thumb" style="background-image: url({{$data['userinfo']->avatar}})"></span>
                        @else
                        <span class="avatar avatar-xl avatar-thumb text-dark">{{strtoupper(substr($data['userinfo']->name, 0, 2))}}</span>
                        @endif
                    </div>
                    <div class="col">
                        <div class="h4 mb-0" Style="color: #2bff03;">
                            ID: {{$data['userinfo']->userid}}
                            @if($data['banned'])
                            <span class="badge bg-red ml-1">محظور!</span>
                            @endif
                        </div>
                        <div class="mb-2 small">عضو منذ: {{strtok($data['userinfo']->created_at, " ")}}</div>
                        <span class="badge bg-blue">الرصيد<span class="badge-addon ">{{$data['userinfo']->balance.' '.env('CURRENCY_NAME').'s'}}</span>
                            <a href="#" class="px-1 ml-2 text-white" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-balance-p">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </a>
                            <a href="#" class="ml-1 text-white" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-balance-n">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"></path>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </a>
                        </span>
                        <span class="badge bg-gray">قيد الانتظار<span class="badge-addon ">{{$data['userinfo']->pending.' '.env('CURRENCY_NAME').'s'}}</span></span>
                    </div>
                </div>
            </div>
            <form class="card-body" method="post" action="{{ route('userinfo_update') }}">
                @csrf
                <input type="hidden" name="userid" value="{{$data['userinfo']->userid}}">
                <div class="mb-2">
                    <span class="form-label mb-1">اسم المستخدم:</span>
                    <input type="text" class="form-control" name="name" value="{{$data['userinfo']->name}}">
                </div>
                <div class="mb-2">
                    <span class="form-label mb-1">عنوان البريد الإلكتروني:</span>
                    <input type="text" class="form-control" name="email" value="{{$data['userinfo']->email}}">
                </div>
                <div class="mb-3">
                    <span class="form-label mb-1">الصورة الرمزية إيرل:</span>
                    <input type="text" class="form-control " name="avatar" value="{{$data['userinfo']->avatar}}">
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text">منتسب من قبل:</span>
                    <input type="text" class="form-control" name="refby" value="{{$data['userinfo']->refby}}">
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text">معرف الجهاز:</span>
                    <input class="form-control" type="text" value="{{$data['userinfo']->device_id}}" placeholder="Disabled input" aria-label="Disabled input example" disabled>
                    
                </div>
                 <div class="input-group mb-2">
                    <span class="input-group-text">اسم الجهاز:</span>
                    <input class="form-control" type="text" value="{{$data['userinfo']->device_name}}" placeholder="Disabled input" aria-label="Disabled input example" disabled>
                </div>
                <div class="mt-3 mb-3">
                    <span class="form-label mb-1">كلمة المرور الجديدة:</span>
                    <input type="text" class="form-control" name="pass" value="">
                </div>
                <input type="submit" class="btn btn-block btn-success" name="submit" value="تحديث بيانات المستخدم">
            </form>
            <div class="card-footer">
                <div class="row">
                    <div class="col-6">
                        <form method="post" action="{{ route('push_msg_post') }}">
                            @csrf
                            <input type="hidden" name="uid" value="{{ $data['userinfo']->userid }}">
                            <input type="submit" class="btn btn-block btn-outline-warning" name="submit" value="أرسل رسالة دفع">
                        </form>
                    </div>
                    <div class="col-6">
                        @if($data['banned'])
                        <a href="{{route('unban',['uid' => $data['userinfo']->userid])}}" type="submit" class="btn btn-block btn-outline-success">قم بإلغاء حظر هذا المستخدم</a>
                        @else
                        <a href="#" class="btn btn-block btn-outline-danger" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-ban">حظر هذا المستخدم</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-8">
		@if(count($data['multi']) > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mr-3 text-nowrap">الحسابات المكررة المحتملة:</h3>
            </div>
            <div class="card-body">
                @foreach($data['multi'] as $mt)
                <a class="badge bg-red px-2" href="{{route('userinfo', ['userid' => $mt->userid])}}">{{$mt->userid}}</a>
                @endforeach
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mr-3 text-nowrap">عمليات السحب</h3>
                <div class="ml-auto">
                    <span class="text-nowrap"><kbd>دولة: {{$data['country']}}</kbd></span>
                </div>
            </div>
            <div class=" table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>طريقة الدفع</th>
                            <th>{{env('CURRENCY_NAME')}}s</th>
                            <th>حالة</th>
                            <th style="width:180px">تاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['wdraw'] as $w)
                        <tr>
                            <td>{{$w->g_name}}</td>
                            <td>{{$w->points}}</td>
                            <td>
                                @if($w->is_completed == 1)
                                <span class="text-green">مكتمل</span>
                                @elseif($w->is_completed == 2)
                                <span class="text-red">مرفوض</span>
                                @else
                                <a href="{{route('withdraw')}}" class="text-blue">قيد الانتظار
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-azure" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"></path>
                                        <path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5"></path>
                                        <path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5"></path>
                                    </svg>
                                </a>
                                @endif
                            </td>
                            <td class="text-nowrap">{{$w->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">Showing <span>{{$data['wdraw']->firstItem()}}</span> to <span>{{$data['wdraw']->lastItem()}}</span> of <span>{{$data['wdraw']->total()}}</span> entries</p>
                <ul class="pagination m-0 ml-auto">
                    {{ $data['wdraw']->appends(request()->except('w'))->links() }}
                </ul>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title mr-3 text-nowrap">تاريخ الأنشطة</h3>
                <div class="ml-auto">
                    <span class="text-nowrap"><kbd>Signed-up IP: {{$data['userinfo']->ip}}</kbd></span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>بطاقة تعريف</th>
                            <th>نوع</th>
                            <th>من</th>
                            <th>IP</th>
                            <th>{{env('CURRENCY_NAME')}}s</th>
                            <th>تاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['hist'] as $h)
                        <tr>
                            <td>{{$h->offerid}}</td>
                            <td>{{$h->network}}</td>
                            <td>
                                @if($h->is_lead == 1)
                                Ad Network
                                @elseif($h->is_lead == 2)
                                Game
                                @else
                                Internal
                                @endif
                            </td>
                            <td>{{$h->ip}}</td>
                            <td>{{$h->points}}</td>
                            <td>{{$h->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">Showing <span>{{$data['hist']->firstItem()}}</span> to <span>{{$data['hist']->lastItem()}}</span> of <span>{{$data['hist']->total()}}</span> entries</p>
                <ul class="pagination m-0 ml-auto">
                    {{ $data['hist']->appends(request()->except('h'))->links() }}
                </ul>
            </div>
        </div>
		<div class="card">
            <div class="card-header">
                <h3 class="card-title mr-3 text-nowrap">تاريخ الألعاب</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>لعبة</th>
                            <th>فاز {{env('CURRENCY_NAME') . 's'}} </th>
                            <th>أنفق {{env('CURRENCY_NAME') . 's'}} </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['g_hist'] as $g)
                        <tr>
                            <td>{{$g->game}}</td>
                            <td>{{$g->points}}</td>
                            <td>{{$g->deducted}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<form method="post" action="{{ route('ban') }}" class="modal modal-blur fade" id="modal-ban" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="userid" value="{{$data['userinfo']->userid}}">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حظر هذا المستخدم</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" />
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" /></svg>
                </button>
            </div>
            <div class="modal-body">
                <label class="form-label">ما هو سبب المنع؟</label>
                <textarea class="form-control" name="reason" placeholder="Write down a reason for your action."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white mr-auto" data-dismiss="modal">اغلاق</button>
                <button type="submit" class="btn btn-danger">الحظر الآن</button>
            </div>
        </div>
    </div>
</form>
<form method="post" action="{{ route('penalty') }}" class="modal modal-blur fade" id="modal-balance-n" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="userid" value="{{$data['userinfo']->userid}}">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">كم ثمن {{strtolower(env('CURRENCY_NAME'))}} هل تريد خصم؟</div>
                <div><input type="text" class="form-control" name="points" value="100"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">الغاء</button>
                <button type="submit" class="btn btn-danger">إعطاء عقوبة</button>
            </div>
        </div>
    </div>
</form>
<form method="post" action="{{ route('reward') }}" class="modal modal-blur fade" id="modal-balance-p" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="userid" value="{{$data['userinfo']->userid}}">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">كم ثمن {{strtolower(env('CURRENCY_NAME'))}} هل تريد المكافأة؟</div>
                <div><input type="text" class="form-control" name="points" value="100"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">الغاء</button>
                <button type="submit" class="btn btn-success">إعطاء مكافأة</button>
            </div>
        </div>
    </div>
</form>
@endsection
