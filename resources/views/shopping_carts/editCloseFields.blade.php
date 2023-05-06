@section('css')
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

<div class="form-group col-lg-12">
    <div class="row topmarginMinusz1em">
        <div class="form-group col-sm-6">
            <div class="form-group col-sm-12">
                <div class="row">
                    <div class="mylabel8 col-sm-2">
                        {!! Form::label('VoucherNumber', langClass::trans('Bizonylatszám:')) !!}
                    </div>
                    <div class="mylabel8 col-sm-10">
                        {!! Form::text('VoucherNumber', $shoppingCart->VoucherNumber, ['class' => 'form-control cellLabel', 'readonly' => 'true']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <div class="form-group col-sm-12">
                <div class="row">
                    <div class="mylabel8 col-sm-4">
                        {!! Form::label('PaymentMethod', langClass::trans('Fizetési mód:')) !!}
                    </div>
                    <div class="mylabel8 col-sm-8">
                        {!! Form::text('VoucherNumber', utilityClass::paymentMethodName($shoppingCart->PaymentMethod), ['class' => 'form-control cellLabel', 'readonly' => 'true']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <div class="form-group col-sm-12">
                <div class="row">
                    <div class="mylabel8 col-sm-4">
                        {!! Form::label('CurrencyName', langClass::trans('Pénznem:')) !!}
                    </div>
                    <div class="mylabel8 col-sm-8">
                        {!! Form::text('CurrencyName', $shoppingCart->CurrencyName, ['class'=>'form-control cellLabel', 'id' => 'CurrencyName', 'readonly' => 'true']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row topmarginMinusz2em">
        <div class="form-group col-sm-8">
            <div class="row">
                <div class="mylabel8 col-sm-1">
                    {!! Form::label('CustomerAddress', langClass::trans('Telephely:')) !!}
                </div>
                <div class="mylabel8 col-sm-11">
                    {!! Form::text('CustomerAddress', $shoppingCart->CustomerAddressName, ['class'=>'form-control cellLabel', 'readonly' => 'true', 'id' => 'CustomerAddress']) !!}
                </div>
            </div>
        </div>
        <div class="form-group col-sm-4">
            <div class="form-group col-sm-12">
                <div class="row">
                    <div class="mylabel8 col-sm-2">
                        {!! Form::label('TransportMode', langClass::trans('Szállítási mód:')) !!}
                    </div>
                    <div class="mylabel8 col-sm-10">
                        {!! Form::text('TransportMode', $shoppingCart->TransportModeName, ['class'=>'form-control cellLabel', 'readonly' => 'true', 'id' => 'TransportMode']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row topmarginMinusz2em">
        <div class="form-group col-sm-8">
            <div class="row">
                <div class="form-group col-sm-4">
                    <div class="form-group col-sm-12">
                        <div class="row">
                            <div class="mylabel8 col-sm-4">
                                {!! Form::label('NetValue', langClass::trans('Nettó érték:')) !!}
                            </div>
                            <div class="mylabel8 col-sm-8">
                                {!! Form::text('NetValue', number_format($shoppingCart->NetValue, 4, ',', '.'), ['class' => 'form-control cellLabel text-right', 'id' => 'NetValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.0001"']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <div class="row">
                        <div class="mylabel8 col-sm-4">
                            {!! Form::label('VatValue', langClass::trans('Áfa:')) !!}
                        </div>
                        <div class="mylabel8 col-sm-8">
                            {!! Form::text('VatValue', number_format($shoppingCart->VatValue, 4, ',', '.'), ['class' => 'form-control cellLabel text-right', 'id' => 'VatValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.0001"']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <div class="form-group col-sm-12">
                        <div class="row">
                            <div class="mylabel8 col-sm-4">
                                {!! Form::label('GrossValue', langClass::trans('Bruttó érték:')) !!}
                            </div>
                            <div class="mylabel8 col-sm-8">
                                {!! Form::text('GrossValue', number_format($shoppingCart->GrossValue, 4, ',', '.'), ['class' => 'form-control cellLabel text-right', 'id' => 'GrossValue', 'readonly' => 'true', 'pattern="[0-9]+([\.,][0-9]+)?" step="0.0001"']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comment Field -->
    <div class="form-group col-sm-12 topmarginMinusz3em">
        <div class="form-group col-sm-12">
            <div class="row">
                <div class="mylabel8 col-sm-1">
                    {!! Form::label('Comment', langClass::trans('Megjegyzés:')) !!}
                </div>
                <div class="mylabel8 col-sm-11">
                    {!! Form::textarea('Comment', $shoppingCart->Comment, ['class' => 'form-control cellLabel', 'rows' => '2', 'placeholder' => 'Megjegyzés', 'id' => 'Comment', 'readonly' => 'true']) !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::hidden('VoucherDate', $shoppingCart->VoucherDate, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'VoucherDate']) !!}
    {!! Form::hidden('DeliveryDate', $shoppingCart->DeliveryDate, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'DeliveryDate']) !!}
    {!! Form::hidden('Customer', $shoppingCart->Customer, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'Customer']) !!}
    {!! Form::hidden('CustomerContact', $shoppingCart->CustomerContact, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'CustomerContact']) !!}
    {!! Form::hidden('Currency', $shoppingCart->Currency, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'Currency']) !!}
    {!! Form::hidden('CurrencyRate', $shoppingCart->CurrencyRate, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'CurrencyRate']) !!}
    {!! Form::hidden('CustomerContract', $shoppingCart->CustomerContract, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'CustomerContract']) !!}
    {!! Form::hidden('Opened', $shoppingCart->Opened, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'Opened']) !!}
    {!! Form::hidden('PaymentMethod', $shoppingCart->PaymentMethod, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'PaymentMethod']) !!}
    {!! Form::hidden('CustomerOrder', $shoppingCart->CustomerOrder, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'CustomerOrder']) !!}
    {!! Form::hidden('DepositValue', $shoppingCart->DepositValue, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'DepositValue']) !!}
    {!! Form::hidden('DepositPercent', $shoppingCart->DepositPercent, ['class' => 'form-control cellLabel', 'required' => 'true', 'id'=>'DepositPercent']) !!}

    <div class="card-footer">
{{--        {!! Form::submit('Ment', ['class' => 'btn btn-primary']) !!}--}}
        <a href="{{ route('shoppingCartIndex', ['customerContact' => ( (empty($_COOKIE['scContact']) ? 0 : $_COOKIE['scContact']) == 0 ? myUser::user()->customercontact_id : -99999),
                                                       'year' => empty($_COOKIE['scYear']) ? date('Y') : $_COOKIE['scYear']]) }}" class="btn btn-default">{{ langClass::trans('Kilép') }}</a>
    </div>

</div>

<div class="form-group col-lg-12">
    <div class="row">
        <div class="col-lg-2">
            <div class="col-lg-12">
                <h4>Tételek</h4>
            </div>
        </div>
{{--        <div class="col-lg-10">--}}
{{--            <div class="card-footer" style="float: left;">--}}
{{--                <a href="{{ route('shoppingCartDetailCreate', $shoppingCart->Id) }}" class="btn btn-warning pull-right" style="margin-left: 5px; width: 80px;">Új tétel</a>--}}
{{--                <a href="{{ route('excelImport') }}" class="btn btn-primary pull-right" style="margin-left: 5px;">Excel import</a>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
    <br>
</div>
<div class="col-lg-12 col-md-12 col-xs-12 topmarginMinusz2em">
    @include('flash::message')
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body"  >
            <table class="table table-hover table-bordered partners-table" id = "myTable" style="width: 100%;"></table>
        </div>
    </div>
    <div class="text-center"></div>
</div>

@section('scripts')
    @include('layouts.datatables_js')
    @include('functions.customNumberFormat_js')
    @include('functions.sweetalert_js')
    @include('functions.ajax_js')

    <script type="text/javascript">

        var table;
        var vmi = 0;

        $(function () {

            ajaxSetup();

            $('[data-widget="pushmenu"]').PushMenu('collapse');

            table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 250,
                scrollX: true,
                paging: false,
                select: false,
                order: [[0, 'asc']],
                ajax: "{{ route('shoppingCartDetailIndex', $shoppingCart->Id ) }}",
                columns: [
                    {title: <?php echo "'" . langClass::trans('Termék') . "'"; ?>, data: 'ProductName', name: 'ProductName'},
                    {title: <?php echo "'" . langClass::trans('Mennyiség') . "'"; ?>, data: 'Quantity', width: '150px', name: 'Quantity', id: 'Quntity'},
                    {title: <?php echo "'" . langClass::trans('Me.egys') . "'"; ?>, data: 'QuantityUnitName', name: 'QuantityUnitName'},
                    {title: <?php echo "'" . langClass::trans('Egys.ár') . "'"; ?>, data: 'UnitPrice', name: 'UnitPrice', id: 'UnitPrice'},
                    {title: <?php echo "'" . langClass::trans('Netto') . "'"; ?>, data: 'NetValue', name: 'NetValue', id: 'NetValueD'},
                    {title: <?php echo "'" . langClass::trans('ÁFA') . "'"; ?>, data: 'VatValue', name: 'VatValue', id: 'VatValueD'},
                    {title: <?php echo "'" . langClass::trans('Bruttó') . "'"; ?>, data: 'GrossValue', name: 'GrossValue', id: 'GrossValueD'},
                    {title: <?php echo "'" . langClass::trans('Pénznem') . "'"; ?>, data: 'CurrencyName', name: 'CurrencyName'},
                    {title: 'Id', data: 'Id', name: 'Id', id: 'Id'},
                    {title: 'Product', data: 'Product', name: 'Product', id: 'Product'},
                    {title: 'VatRate', data: 'VatRate', name: 'VatRate', id: 'VatRate'},
                    {title: ' ', data: 'action', sClass: "text-center", width: '50px', name: 'action', orderable: false, searchable: false},
                ],
                columnDefs: [
                    {
                        targets: [8,9,10],
                        visible: false
                    },
                    {
                        targets: [1,3,4,5,6],
                        render: $.fn.dataTable.render.number( '.', ',', 4),
                        sClass: 'text-right',
                        width: '150px'
                    },
                    {
                        targets: [7],
                        sClass: "text-center",
                        width:'50px'
                    },
                    // {
                    //     targets: [1],
                    //     render: function ( data, type, full, meta ) {
                    //         return '<input class="form-control text-right" type="number" value="'+ data +'" pattern="[0-9]+([\.,][0-9]+)?" step="0.0001" style="width:250px;height:20px;font-size: 15px;"/>';
                    //         // return '<input class="form-control text-right" type="number" value="'+ data +'" onfocusout="QuantityChange('+meta["row"]+', this.value)" pattern="[0-9]+([\.,][0-9]+)?" step="0.0001" style="width:250px;height:20px;font-size: 15px;"/>';
                    //     },
                    // }
                ],
                buttons: [],
            });

        });

    </script>
@endsection

