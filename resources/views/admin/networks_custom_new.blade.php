@extends('layouts.head')
@section('css')
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
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
    <form class="card" action="{{route('networks_custom_add')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card-header">
            <h4 class="card-title">{{$data['title']}}</h4>
        </div>
        <div class="card-body row">
            <div class="alert alert-info" role="alert">
                <span class="font-weight-bold">Allowed macros are:</span> <span class="text-red"><span class="font-weight-bold">[app_oid]</span> for Offer ID</span> and <span class="text-red"><span class="font-weight-bold">[app_uid]</span> for User ID</span>.<br>
                <span class="font-weight-bold">Reminder:</span> system automatically adds <span class="text-red">uid, offerid</span> parameters into the given URL. Catch them in your tracker.
            </div>
            <div class="mb-3 col-12 col-lg-4">
                <label class="form-label">Title: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" placeholder="Clash of clans" value="{{old('title')}}">
            </div>
            <div class="mb-3 col-12 col-lg-8">
                <label class="form-label">Instruction: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="description" placeholder="Install the app to claim reward" value="{{old('description')}}">
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">Offer Icon: <span class="text-danger">*</span></label>
                <div class="form-file">
                    <input type="file" name="offer_icon" class="form-file-input img-input" id="imagefile">
                    <label class="form-file-label" for="customFile">
                        <span class="form-file-text img-choose">Choose image...</span>
                        <span class="form-file-button">Browse</span>
                    </label>
                </div>
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">Enter Country ISO <small>(blank for all countries)</small>:</label>
                <input type="text" class="form-control" name="country" placeholder="US,AU,GB" value="{{old('country')}}">
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">Amount of Rewarding {{env('CURRENCY_NAME')}}s: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="points" placeholder="100" value="{{old('points')}}">
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">Maximum lead quantity: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="max" placeholder="150" value="{{old('max')}}">
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">Offer URL: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="url" placeholder="https://domain.tld/some/offer/id" value="{{old('url')}}">
            </div>
            <div class="d-flex flex-row mt-4">
                <input type="submit" class="btn btn-primary" value="Create this offer" />
                <a href="{{route('networks_custom')}}" class="btn btn-white ml-4">Cancel</a>
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