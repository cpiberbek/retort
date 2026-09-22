@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row">

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    @include('partials.card-1')
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @include('partials.card-2')
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    @include('partials.card-4')
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    @include('partials.card-3')
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    @include('partials.card-6')
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    @include('partials.card-5')
                </div>
            </div>
        </div>

    </div>

</div>
@endsection