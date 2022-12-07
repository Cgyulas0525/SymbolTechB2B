@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>{{ \App\Classes\langClass::trans('Kedvenc termékek') }}</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($customerContactFavoriteProduct, ['route' => ['customerContactFavoriteProducts.update', $customerContactFavoriteProduct->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('customer_contact_favorite_products.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('customerContactFavoriteProducts.index') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kliép') }}</a>
            </div>

           {!! Form::close() !!}

        </div>
    </div>
@endsection
