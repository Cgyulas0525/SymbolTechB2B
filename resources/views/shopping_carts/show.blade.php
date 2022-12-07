@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ \App\Classes\langClass::trans('Kosár törlés') }}</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('shoppingCarts.index') }}">
                        {{ \App\Classes\langClass::trans('Vissza') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">

            <div class="card-body">
                <div class="row">
                    @include('shopping_carts.show_fields')
                </div>
            </div>

            <div class="card-footer">
                <a class="btn btn-primary"
                   href="{{ route('shoppingCarts.index') }}">
                    {{ \App\Classes\langClass::trans('Vissza') }}
                </a>
                {!! Form::submit(\App\Classes\langClass::trans('Töröl'), ['class' => 'btn btn-danger', 'id' => 'saveBtn']) !!}
            </div>

        </div>
    </div>
@endsection
