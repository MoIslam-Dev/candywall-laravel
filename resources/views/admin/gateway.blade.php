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
<div class="row">
    <form action="{{route('gateway_add')}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$data['id']}}">
        <div class="card">
            <div class="card-header bg-gray-lt pt-2 pb-0">
                <h4 class="text-dark">اضافة طريقة دفع جديدة</h4>
            </div>
            <div class="card-body pt-2 row">
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <label class="form-label">Gift value</label>
                    <input type="text" class="form-control" name="amount" placeholder="$10" value="{{old('amount')}}">
                </div>
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <label class="form-label">لاستبدال {{env('CURRENCY_NAME')}}s:</label>
                    <input type="text" class="form-control" name="points" placeholder="1000" value="{{old('points')}}">
                </div>
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <label class="form-label">كمية العناصر</label>
                    <input type="text" class="form-control" name="quantity" placeholder="50" value="{{old('quantity')}}">
                </div>
                <div class="col-xl-3 col-md-6 col-sm-12 px-5">
                    <label class="form-label">.</label>
                    <button type="submit" class="btn btn-block btn-dark">أضف هذا العنصر</button>
                </div>
            </div>
        </div>
    </form>
    <div class="col-12">
        <div class="card p-0">
            <div class="card-header text-primary font-weight-bold">{{$data['name']}} عناصر:</div>
            <div class="card-body border-bottom p-0">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>قيمة البطاقة</th>
                                <th>{{env('CURRENCY_NAME')}} مطلوب</th>
                                <th>متوفر في المخزن</th>
                                <th>اجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['items'] as $d)
                            <tr>
                                <td>{{$d->amount}}</td>
                                <td>{{$d->points}}</td>
                                <td>{{$d->quantity}}</td>
                                <td><a class="btn btn-sm btn-secondary" href="{{route('gateway_del', ['id' => $d->id])}}">حذف</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center pb-0">
            <ul class="pagination">
                {{ $data['items']->appends(request()->except('p'))->links() }}
            </ul>
        </div>
    </div>
</div>
@endsection
