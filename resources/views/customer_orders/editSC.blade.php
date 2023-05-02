@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-3">
                    <h4>{{ $shoppingCart->VoucherNumber }} - {{ date('Y.m.d', strtotime($shoppingCart->VoucherDate)) }}</h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ langClass::trans('Nettó') }}: {{ number_format($shoppingCart->NetValue, 4, ',', '.') }}</h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ langClass::trans('ÁFA') }}: {{ number_format($shoppingCart->VatValue, 4, ',', '.') }}</h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ langClass::trans('Bruttó') }}: {{ number_format($shoppingCart->GrossValue, 4, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($shoppingCart, ['route' => ['customerOrders.update', $shoppingCart->Id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('customer_orders.fieldsSc')
                </div>
            </div>

            <div class="card-footer">
                <a href="#" class="btn btn-primary" id="saveBtn">{{ langClass::trans('Kosárba') }}</a>
                <a href="{{ route('customerOrders.index') }}" class="btn btn-default">{{ langClass::trans('Kilép') }}</a>
            </div>

           {!! Form::close() !!}

        </div>
    </div>
@endsection
