@extends('layouts.head')
@section('css')
<style>
    .fixed-height {
        height: 150px !important;
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
<span class="page-title text-nowrap">Scratch Cards</span>
<div class="card mt-3">
    <div class="card-body">
        <form class="row" method="post" action="{{route('game_scratch_limit')}}">
            @csrf
            <div class="col-auto">
                <label class="form-label font-weight-bold">Daily card purchase limit:</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="purchase_limit" placeholder="10" value="{{env('GAME_SC_DLIMIT')}}">
                    <span class="input-group-text">Cards</span>
                </div>
            </div>
            <div class="col">
                <label class="form-label">.</label>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            <div class="col-auto mt-3">
                <a href="{{route('game_scratcher')}}" class="btn btn-success mr-2 my-1">Add a card</a>
                <a href="{{route('game_scratcher_clean')}}" class="btn btn-danger my-1">Clean up expired cards</a>
            </div>
        </form>
    </div>
</div>
<div class="row">
    @foreach($data as $d)
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card card-sm">
            <a href="{{route('game_scratcher', ['id' => $d->id])}}" class="d-block text-center"><img src="{{$d->card}}" class="card-img-top fixed-height"></a>
            <div class="card-body">
                <div class="font-weight-bold mb-1">{{$d->name}}</div>
                <div class="small">{{env('CURRENCY_NAME')}}s range: <span class="font-italic">{{$d->min.' - '.$d->max}}</span></div>
                <div class="small font-italic">Card expires after {{$d->days}} days</div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="col-lg-12 d-flex justify-content-center mt-3">
    <ul class="pagination">
        {{ $data->appends(request()->except('page'))->links() }}
    </ul>
</div>
@endsection
