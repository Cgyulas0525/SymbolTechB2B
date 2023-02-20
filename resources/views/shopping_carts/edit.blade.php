@extends('layouts.app')

@section('css')
    @include('layouts.costumcss')
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <h4>{{ langClass::trans('Kosár') }} ( {{ langClass::trans('Tétel: ') }} {{ $shoppingCart->shoppingcartdetaildata->count() }} db)</h4>
                </div>
                <div class="col-lg-6 col-md-6 col-xs-12">
{{--                    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ route('importExcel') }}" class="form-horizontal  pull-right" method="post" enctype="multipart/form-data">--}}
{{--                        {{ csrf_field() }}--}}
{{--                        <input type="file" name="import_file" accept=".xlsx, .xls, .csv" id="import_file"/>--}}
{{--                        <label for="code">Excel kód:</label>--}}
{{--                        <input type="number" id="code" name="code" pattern="[0-9]+([\.,][0-9]+)?" step="1" style="width: 3em;" value={{ session('excelCode') }}>--}}
{{--                        <label for="quantity">Mennyiség:</label>--}}
{{--                        <input type="number" id="quantity" name="quantity" pattern="[0-9]+([\.,][0-9]+)?" step="1" style="width: 3em;" value={{ session('excelQuantity') }}>--}}
{{--                        <button class="btn btn-primary importButton">Import</button>--}}
{{--                    </form>--}}
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($shoppingCart, ['route' => ['shoppingCarts.update', $shoppingCart->Id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('shopping_carts.editFields')
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection

