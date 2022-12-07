@section('css')
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-2">
                {!! Form::label('Product', \App\Classes\langClass::trans('Termék:')) !!}
            </div>
            <div class="mylabel col-sm-10">
                {!! Form::select('Product', ddwClass::productDDW(), null,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'Product']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-2">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-6">
                {!! Form::label('QuantityUnit', \App\Classes\langClass::trans('Mennyiségi egység:')) !!}
            </div>
            <div class="mylabel col-sm-6">
                {!! Form::number('QuantityUnit', null, ['class' => 'form-control', 'id' => 'QuantityUnit']) !!}
            </div>
        </div>
    </div>
</div>

<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('Quantity', \App\Classes\langClass::trans('Mennyiség:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::number('Quantity', null, ['class' => 'form-control', 'id' => 'Quantity']) !!}
            </div>
        </div>
    </div>
</div>

<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('UnitPrice', \App\Classes\langClass::trans('Egységár:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::number('UnitPrice', null, ['class' => 'form-control', 'id' => 'UnitPrice']) !!}
            </div>
        </div>
    </div>
</div>

<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-5 formalignright">
                {!! Form::label('Vat', \App\Classes\langClass::trans('ÁFA:')) !!}
            </div>
            <div class="mylabel col-sm-7">
                {!! Form::number('Vat', null, ['class' => 'form-control', 'id' => 'Vat']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-5 formalignright">
                {!! Form::label('Currency', \App\Classes\langClass::trans('Pénznem:')) !!}
            </div>
            <div class="mylabel col-sm-7">
                {!! Form::number('Currency', null, ['class' => 'form-control', 'id' => 'Currency']) !!}
            </div>
        </div>
    </div>
</div>

<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('CurrencyRate', \App\Classes\langClass::trans('Árfolyam:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::number('CurrencyRate', null, ['class' => 'form-control', 'id' => 'CurrencyRate']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('NetValue', \App\Classes\langClass::trans('Nettó összeg:')) !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::number('NetValue', null, ['class' => 'form-control', 'id' => 'NetValue']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('VatValue', \App\Classes\langClass::trans('ÁFA összeg:')) !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::number('VatValue', null, ['class' => 'form-control', 'id' => 'VatValue']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('GrossValue', \App\Classes\langClass::trans('Bruttó összeg:')) !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::number('GrossValue', null, ['class' => 'form-control', 'id' => 'GrossValue']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-12">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-1">
                {!! Form::label('Comment', \App\Classes\langClass::trans('Megjegyzés:')) !!}
            </div>
            <div class="mylabel col-sm-11">
                {!! Form::textarea('Comment', null, ['class' => 'form-control', 'rows' => '2', 'placeholder' => 'Megjegyzés', 'id' => 'Comment']) !!}
            </div>
        </div>
    </div>
</div>

@section('scripts')

    <script type="text/javascript">
        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('[data-widget="pushmenu"]').PushMenu('collapse');

        });
    </script>
@endsection
