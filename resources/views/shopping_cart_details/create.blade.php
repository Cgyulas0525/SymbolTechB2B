@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-3">
                    <h4>{{ $shoppingCart->VoucherNumber }}</h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ langClass::trans('Nettó') }}: <a id="netto">{{ number_format($shoppingCart->NetValue, 4, ',', '.') }}</a></h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ langClass::trans('ÁFA') }}: <a id="vat">{{ number_format($shoppingCart->VatValue, 4, ',', '.') }}</a></h4>
                </div>
                <div class="col-sm-3 text-right">
                    <h4>{{ langClass::trans('Bruttó') }}: <a id="brutto">{{ number_format($shoppingCart->GrossValue, 4, ',', '.') }}</a></h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::open(['route' => 'shoppingCartDetails.store']) !!}

            <div class="card-body">

                <div class="row">
                    @include('shopping_cart_details.fieldsFind')
                </div>

            </div>

            <div class="card-footer">
                <a href="{{ route('shoppingCarts.edit', $shoppingCart->Id) }}" class="btn btn-success">{{ langClass::trans('Kosár módosítás') }}</a>
                <a href="{{ route('shoppingCartIndex', ['customerContact' => ( (empty($_COOKIE['scContact']) ? 0 : $_COOKIE['scContact']) == 0 ? myUser::user()->customercontact_id : -99999),
                                                            'year' => empty($_COOKIE['scYear']) ? date('Y') : $_COOKIE['scYear']]) }}" class="btn btn-default">{{ langClass::trans('Kosár') }}</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
