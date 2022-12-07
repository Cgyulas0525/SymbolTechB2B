@section('css')
    @include('layouts.costumcss')
@endsection

<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('VoucherNumber', \App\Classes\langClass::trans('Bizonylatszám:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::text('VoucherNumber', $shoppingCart->VoucherNumber, ['class' => 'form-control', 'readonly' => 'true']) !!}
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
                {!! Form::text('CustomerAddress', $shoppingCart->CustomerAddressName,['class'=>'form-control', 'readonly' => 'true', 'id' => 'CustomerAddress']) !!}
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
                {!! Form::text('PaymentMethod', $shoppingCart->PaymentMethodName, ['class'=>'form-control', 'readonly' => 'true', 'id' => 'PaymentMethod']) !!}
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
                {!! Form::text('TransportMode', $shoppingCart->TransportModeName, ['class'=>'form-control', 'readonly' => 'true', 'id' => 'TransportMode']) !!}
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
                {!! Form::text('DepositValue', number_format($shoppingCart->DepositValue, 4, ',', '.'), ['class' => 'form-control text-right', 'id' => 'DepositValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.01"']) !!}
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
                {!! Form::text('DepositPercent', number_format($shoppingCart->DepositPercent, 4, ',', '.'), ['class' => 'form-control text-right', 'id' => 'DepositPercent', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.0001"']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('NetValue', \App\Classes\langClass::trans('Nettó érték:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::text('NetValue', number_format($shoppingCart->NetValue, 4, ',', '.'), ['class' => 'form-control text-right', 'id' => 'NetValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.0001"']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="row">
        <div class="mylabel col-sm-4">
            {!! Form::label('VatValue', \App\Classes\langClass::trans('Áfa:')) !!}
        </div>
        <div class="mylabel col-sm-8">
            {!! Form::text('VatValue', number_format($shoppingCart->VatValue, 4, ',', '.'), ['class' => 'form-control text-right', 'id' => 'VatValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.0001"']) !!}
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-4">
                {!! Form::label('GrossValue', \App\Classes\langClass::trans('Bruttó érték:')) !!}
            </div>
            <div class="mylabel col-sm-8">
                {!! Form::text('GrossValue', number_format($shoppingCart->GrossValue, 4, ',', '.'), ['class' => 'form-control text-right', 'id' => 'GrossValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.0001"']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Comment Field -->
<div class="form-group col-sm-12">
    {!! Form::label('Comment', \App\Classes\langClass::trans('Megjegyzés:')) !!}
    {!! Form::textarea('Comment', $shoppingCart->Comment, ['class' => 'form-control', 'rows' => '2', 'placeholder' => 'Megjegyzés', 'readonly' => 'true', 'id' => 'Comment']) !!}
</div>

@section('scripts')

    @include('functions.sweetalert_js')
    @include('functions.ajax_js')

    <script type="text/javascript">
        $(function () {

            ajaxSetup();

            $('#saveBtn').on('click', function (event) {
                event.preventDefault();

                const url = $(this).attr('href');
                swal.fire({
                    title:  <?php echo "'" . App\Classes\langClass::trans('Biztos törli a tételt?') . "'"; ?>,
                    text:  <?php echo "'" . App\Classes\langClass::trans('Ez a bejegyzés véglegesen törlődik!') . "'"; ?>,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Törlés",
                    cancelButtonText: "Kilép"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "GET",
                            url:"{{url('cartDestroy', [$shoppingCart->Id])}}",
                            success: function (response) {
                                window.location.href = url;
                                if ( response.name != null ) {
                                    sw( <?php echo "'" . App\Classes\langClass::trans('A tétel nem törölhető') . "'"; ?>);
                                }
                            },
                            error: function (response) {
                                console.log('Error:', response);
                                alert('nem ok');
                            }
                        });
                    }
                });
            });

        });

    </script>

@endsection


