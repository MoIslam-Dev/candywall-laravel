@extends('layouts.head')
@section('css')
<style>
    .clos_e {
        position: absolute;
        right: 0;
        top: 0;
        background: rgba(0, 0, 0, 0.5);
        color: #ffffff;
        padding: 3px 10px;
        border-radius: 0px 2px 0px 0px
    }

    .clos_e:hover {
        color: #999999
    }

    .btn-trans {
        background-color: rgba(0, 0, 0, 0.5);
        color: #dddddd
    }

    .btn-trans:hover {
        background-color: rgba(0, 0, 0, 0.7);
        color: #ffffff
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
    {{$errors->first()}}
</div>
@endif
<div class="row">
    <div class="card">
        <div class="card-header">
            <span class="card-title">ايام{{$active->count()}} مكافة النشاط اليومية ل </span>

        </div>
        <div class="row card-body">
            @foreach($active as $d)
            <form class="col-md-6 col-xl-3" method="post" action="{{route('activity_reward_edit')}}">
                @csrf
                <input type="hidden" name="id" value="{{$d->id}}">
                <div class="card">
                    <a href="{{route('activity_reward_toggle', ['id' => $d->id, 'a' => $d->active])}}" class="clos_e">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"></path>
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </a>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-white">عنوان لليوم {{$loop->iteration}}:</label>
                            <input type="text" class="form-control" name="name" value="{{$d->name}}">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label text-white">دقيقة:</label>
                                <input type="text" class="form-control" name="min" value="{{$d->min}}">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-white">الأعلى:</label>
                                <input type="text" class="form-control" name="max" value="{{$d->max}}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-block btn-success">تحديث</button>
                    </div>
                </div>
            </form>
            @endforeach
        </div>
    </div>
    <div class="card">
        <div class="card-header">مكافآت النشاط غير النشط</div>
        <div class="card-header row">
            @foreach($inactive as $d)
            <div class="col-md-6 col-xl-3">
                <div class="card bg-gray">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-white">عنوان لمعرف الفتحة {{$d->id}}:</label>
                            <input type="text" class="form-control" value="{{$d->name}}" readonly="">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label text-white">دقيقة:</label>
                                <input type="text" class="form-control" value="{{$d->min}}" readonly="">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-white">الأعلى:</label>
                                <input type="text" class="form-control" value="{{$d->max}}" readonly="">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{route('activity_reward_toggle', ['id' => $d->id, 'a' => $d->active])}}" class="btn btn-block btn-trans">اجعله نشطًا</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
