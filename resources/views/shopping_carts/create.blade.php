@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>{{ \App\Classes\langClass::trans('Kosár') }}</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::open(['route' => 'shoppingCarts.store']) !!}

            <div class="card-body">

                <div class="row">
                    @include('shopping_carts.fields')
                </div>

            </div>

            <div class="card-footer">
                {!! Form::submit(\App\Classes\langClass::trans('Ment'), ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('shoppingCarts.index') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
