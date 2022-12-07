@extends('layouts.app')

@section('css')
    @include('layouts.costumcss')
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <h4>Excel import. File: <a id="fejszoveg"> </a></h4>
                </div>
                <div class="col-lg-6 col-md-6 col-xs-12">
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::open(['route' => 'excelBetolt', 'files' => true]) !!}

            <div class="card-body">
                <div class="row">
                    @include('shopping_carts.excelImportFields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Import', ['class' => 'btn btn-primary']) !!}
               <a href="#" class="btn btn-success" id="shopButton">{{ \App\Classes\langClass::trans('Kosárba') }}</a>
               <a href="{{ route('excelImportUseRecordsDelete') }}" class="btn btn-danger">{{ \App\Classes\langClass::trans('Törlés') }}</a>
               <a href="{{ route('shoppingCarts.edit', $shoppingCart->Id) }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection

