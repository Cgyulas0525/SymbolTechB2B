@section('css')
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('VoucherNumber', \App\Classes\langClass::trans('Bizonylatszám:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::text('VoucherNumber', App\Classes\ShoppingCart\voucherNumber::nextB2BVoucherNumber(), ['class' => 'form-control', 'readonly' => 'true']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-8">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-2">
                {!! Form::label('CustomerAddress', \App\Classes\langClass::trans('Telephely:')) !!}
            </div>
            <div class="mylabel col-sm-10">
                {!! Form::select('CustomerAddress', ddwClass::customerAddressDDW(session('customer_id')), null,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'CustomerAddress']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('PaymentMethod', \App\Classes\langClass::trans('Fizetési mód:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::select('PaymentMethod', ddwClass::customerPaymentMethodDDW(), null, ['class'=>'select2 form-control', 'required' => 'true', 'id' => 'PaymentMethod']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('TransportMode', \App\Classes\langClass::trans('Szállítási mód:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::select('TransportMode', ddwClass::transportmodeDDW(), myUser::user()->TransportMode,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'TransportMode']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('DepositValue', \App\Classes\langClass::trans('Előleg:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::number('DepositValue', 0, ['class' => 'form-control text-right', 'id' => 'DepositValue', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.01"']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('DepositPercent', \App\Classes\langClass::trans('Előleg %:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::number('DepositPercent', 0, ['class' => 'form-control text-right', 'id' => 'DepositPercent', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.01"']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-2">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('NetValue', \App\Classes\langClass::trans('Nettó érték:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::number('NetValue', 0, ['class' => 'form-control text-right', 'id' => 'NetValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.01"']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-2">
    <div class="row">
        <div class="mylabel col-sm-4">
            {!! Form::label('VatValue', \App\Classes\langClass::trans('Áfa:')) !!}
        </div>
        <div class="mylabel col-sm-8">
            {!! Form::number('VatValue', 0, ['class' => 'form-control text-right', 'id' => 'VatValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.01"']) !!}
        </div>
    </div>
</div>
<div class="form-group col-sm-2">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('GrossValue', \App\Classes\langClass::trans('Bruttó érték:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::number('GrossValue', 0, ['class' => 'form-control text-right', 'id' => 'GrossValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.01"']) !!}
            </div>
        </div>
    </div>
</div>
{!! Form::hidden('VoucherDate', \Carbon\Carbon::now(), ['class' => 'form-control', 'required' => 'true', 'id'=>'VoucherDate']) !!}
{!! Form::hidden('DeliveryDate', Carbon\Carbon::now()->addDays(8)->format('Y-m-d'), ['class' => 'form-control', 'required' => 'true', 'id'=>'DeliveryDate']) !!}

<!-- Comment Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Comment', \App\Classes\langClass::trans('Megjegyzés:')) !!}
    {!! Form::textarea('Comment', null, ['class' => 'form-control', 'rows' => '2', 'placeholder' => 'Megjegyzés', 'id' => 'Comment']) !!}
</div>


{{--<div class="form-group col-sm-3">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-4">--}}
{{--                {!! Form::label('Currency', 'Pénznem:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-8">--}}
{{--                {!! Form::text('Currency', utilityClass::currencyId('HUF'), ['class'=>'form-control', 'required' => 'true', 'id' => 'Currency']) !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form-group col-sm-3">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-4">--}}
{{--                {!! Form::label('CurrencyRate', 'Árfolyam:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-8">--}}
{{--                {!! Form::text('CurrencyRate', 1, ['class'=>'form-control', 'required' => 'true', 'id' => 'CurrencyRate']) !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form-group col-sm-3">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-4">--}}
{{--                {!! Form::label('VoucherDate', 'Kelt:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-8">--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form-group col-sm-3">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-4">--}}
{{--                {!! Form::label('DeliveryDate', 'Szállítási határidő:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-8">--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
