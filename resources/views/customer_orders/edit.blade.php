@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-3">
                    <h4>{{ $customerOrder->VoucherNumber }} - {{ date('Y.m.d', strtotime($customerOrder->VoucherDate)) }}</h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ \App\Classes\langClass::trans('Nettó') }}: {{ number_format($customerOrder->NetValue, 4, ',', '.') }}</h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ \App\Classes\langClass::trans('ÁFA') }}: {{ number_format($customerOrder->VatValue, 4, ',', '.') }}</h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ \App\Classes\langClass::trans('Bruttó') }}: {{ number_format($customerOrder->GrossValue, 4, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($customerOrder, ['route' => ['customerOrders.update', $customerOrder->Id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('customer_orders.fields')
                </div>
            </div>

            <div class="card-footer">
                <a href="#" class="btn btn-primary" id="saveBtn">{{ \App\Classes\langClass::trans('Kosárba') }}</a>
                <a href="{{ route('customerOrders.index') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
            </div>

           {!! Form::close() !!}

        </div>
    </div>
@endsection
