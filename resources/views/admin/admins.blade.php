@extends('layouts.head')
@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-10 row">
        <div class="col-md-12">
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
        </div>
        <div class="col-md-6">
            <form method="post" class="card px-0 mb-4" action="{{route('admins_update')}}">
                @csrf
                <div class="card-header bg-dark-lt text-dark">
                    <span class="card-title">إعدادات ملف تعريف المسؤول</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم الكامل:</label>
                        <input type="text" class="form-control" name="name" value="{{old('name', $data->name)}}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">عنوان البريد الإلكتروني:</label>
                        <input type="text" class="form-control" name="email" value="{{old('email', $data->email)}}">
                    </div>
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label class="form-label">كلمة المرور الجديدة:</label>
                            <input type="password" class="form-control" name="password_1" value="{{old('password_1')}}">
                        </div>
                        <div class="mb-3 col-6">
                            <label class="form-label">تأكيد كلمة المرور الجديدة:</label>
                            <input type="password" class="form-control" name="password_2" value="{{old('password_2')}}">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">كلمة السر الحالية:</label>
                        <input type="password" class="form-control" name="password" value="">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-dark">تحديث الاعدادات</button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form method="post" class="card px-0" action="{{route('admins_change')}}">
                @csrf
                <div class="card-header bg-secondary text-white">
                    <span class="card-title">نقل حقوق المسؤول:</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">عنوان البريد الإلكتروني للمسؤول الجديد:</label>
                        <input type="text" class="form-control" name="a_email" value="{{old('a_email')}}">
                    </div>
                    <div>
                        <label class="form-label">كلمة السر الحالية:</label>
                        <input type="password" class="form-control" name="password" value="">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
