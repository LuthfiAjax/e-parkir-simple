@extends('layout')

@section('title', 'Test BE Bounce Parking')

@section('content')
    <div class="container px-5 py-5">
        <h1 class="fs-3 text-center text-uppercase">Choice Permission Roles</h1>
        <div class="container my-5">
            <div class="row">
                <div class="col-md-6 text-center">
                    <a href="{{ url('/user') }}" class="btn btn-primary rounded">USER PERMISSION</a>
                </div>
                <div class="col-md-6 text-center">
                    <a href="{{ url('/admin') }}" class="btn btn-success rounded">ADMIN PERMISSION</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
   
@endsection