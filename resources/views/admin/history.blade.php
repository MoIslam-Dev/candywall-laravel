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
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">تاريخ الأنشطة</h3>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="d-flex">
                    <div class="text-muted">
                        عرض
                        <div class="mx-2 d-inline-block">
                            <form method="get" action="{{ route('history') }}">
                                <input name="e" type="text" class="form-control form-control-sm" value="{{$hist['entries']}}" size="2">
                            </form>
                        </div>
                        إدخالات
                    </div>
                    <div class="ml-auto text-muted">
                        البحث:
                        <div class="ml-2 d-inline-block">
                            <form method="get" action="{{ route('search_history') }}">
                                <input name="e" type="hidden" value="{{$hist['entries']}}">
                                <input name="s" type="text" class="form-control form-control-sm">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th class="w-1">#</th>
                            <th>معرف المستخدم</th>
                            <th>شبكة</th>
                            <th>معرف او اسم العرض</th>
                            <th>عنوان IP</th>
                            <th>نقاط{{env('CURRENCY_NAME')}}s</th>
                            <th>التاريخ</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hist['data'] as $h)
                        <tr>
                            <td><span class="text-muted">{{$h->id}}<span></td>
                            <td>{{$h->userid}}</td>
                            <td>{{$h->network}}</td>
                            <td>{{$h->offerid}}</td>
                            <td>{{$h->ip}}</td>
                            @if($h->points < 0) <td><span class="text-red">{{$h->points}}</span></td>
                                @else
                                <td><span class="text-green">{{$h->points}}</span></td>
                                @endif
                                <td>{{$h->created_at}}</td>
                                <td class="text-right">
                                    <span class="dropdown ml-1">
                                        <button class="btn btn-white btn-sm dropdown-toggle align-text-top" data-boundary="viewport" data-toggle="dropdown">اجراءات</button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{route('userinfo', ['userid' => $h->userid])}}">ابحث عن معلومات المستخدم هذه</a>
                                            <a class="dropdown-item text-blue" href="{{route('del_history', ['id' => $h->id])}}">حذف هذا التاريخ فقط</a>
                                            <a class="dropdown-item text-red" href="{{route('del_history', ['id' => $h->id, 'deduct' => '1'])}}">حذف وضبط الرصيد</a>
                                        </div>
                                    </span>
                                </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">عرض <span>{{$hist['data']->firstItem()}}</span> to <span>{{$hist['data']->lastItem()}}</span> of <span>{{$hist['data']->total()}}</span> إدخالات</p>
                <ul class="pagination m-0 ml-auto">
                    {{ $hist['data']->appends(request()->except('page'))->links() }}
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
