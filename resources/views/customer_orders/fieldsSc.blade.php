@section('css')
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

<div class="col-lg-12 col-md-12 col-xs-12 topmarginMinusz1em">
    <section class="content-header">
        <h4>{{ langClass::trans('Kosár Tételek') }} </h4>
    </section>
    @include('flash::message')
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body"  >
            <table class="table table-hover table-bordered partners-table w-100"></table>
        </div>
    </div>
    <div class="text-center"></div>
</div>

@section('scripts')
    @include('layouts.datatables_js')
    @include('functions.customNumberFormat_js')
    @include('functions.sweetalert_js')

    <script type="text/javascript">

        var table;
        var vmi = 0;

        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('[data-widget="pushmenu"]').PushMenu('collapse');

            table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 400,
                scrollX: true,
                paging: false,
                select: true,
                order: [[1, 'asc']],
                ajax: "{{ route('shoppingCartDetailIndex', $shoppingCart->Id ) }}",
                columns: [
                    {
                        title: '',
                        data: null,
                        defaultContent: '',
                        orderable: false,
                        className: 'select-checkbox',
                        targets:   0
                    },

                    {title: <?php echo "'" . langClass::trans('Termék') . "'"; ?>, data: 'ProductName', name: 'ProductName'},
                    {title: <?php echo "'" . langClass::trans('Mennyiség') . "'"; ?>, data: 'Quantity', width: '150px', name: 'Quantity', id: 'Quantity'},
                    {title: <?php echo "'" . langClass::trans('Me.egys') . "'"; ?>, data: 'QuantityUnitName', name: 'QuantityUnitName'},
                    {title: <?php echo "'" . langClass::trans('Egys.ár') . "'"; ?>, data: 'UnitPrice', name: 'UnitPrice', id: 'UnitPrice'},
                    {title: <?php echo "'" . langClass::trans('Netto') . "'"; ?>, data: 'NetValue', name: 'NetValue', id: 'NetValueD'},
                    {title: <?php echo "'" . langClass::trans('ÁFA') . "'"; ?>, data: 'VatValue', name: 'VatValue', id: 'VatValueD'},
                    {title: <?php echo "'" . langClass::trans('Bruttó') . "'"; ?>, data: 'GrossValue', name: 'GrossValue', id: 'GrossValueD'},
                    {title: <?php echo "'" . langClass::trans('Pénznem') . "'"; ?>, data: 'CurrencyName', name: 'CurrencyName'},
                    {{--{title: <?php echo "'" . langClass::trans('Státusz') . "'"; ?>, data: 'StatusName', name: 'StatusName'},--}}
                    {title: 'Id', data: 'Id', name: 'Id'},
                    {title: 'Product', data: 'Product', name: 'Product'},
                    {title: 'VatRate', data: 'VatRate', name: 'VatRate', id: 'VatRate'},
                ],
                columnDefs: [
                    {
                        targets: [9,10,11],
                        visible: false
                    },
                    {
                        targets: [2,4,5,6,7],
                        render: $.fn.dataTable.render.number( '.', ',', 4),
                        sClass: 'text-right',
                        width: '150px'
                    },
                    {
                        targets: [8],
                        sClass: "text-center",
                        width:'50px'
                    },
                ],
                buttons: [],
            });

        });

        $('#saveBtn').click(function (e) {
            if ( table.rows( { selected: true } ).count() > 0) {
                swal.fire({
                    title: <?php echo "'" . langClass::trans("Tételek kosárba másolás!") . "'"; ?>,
                    text: <?php echo "'" . langClass::trans("Biztosan kosárba másolja a tételeket?") . "'"; ?>,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: <?php echo "'" . langClass::trans("Kosárba") . "'"; ?>,
                    cancelButtonText: <?php echo "'" . langClass::trans("Kilép") . "'"; ?>
                }).then((result) => {
                    if (result.isConfirmed) {
                        var rows = table.rows({ selected: true } ).data();
                        for ( i = 0; i < rows.length; i++) {
                            $.ajax({
                                type:"GET",
                                url:"{{url('api/copyShoppingCartToShoppingCart')}}",
                                data: { Id: rows[i].Id, Product: rows[i].Product},
                                success: function (response) {
                                    console.log('Error:', response);
                                },
                                error: function (response) {
                                    console.log('Error:', response);
                                    alert('nem ok');
                                }
                            });
                        }
                    }
                });
            } else {
                swMove(<?php echo "'" . langClass::trans("Nincs kijelölt tétel!") . "'"; ?>);
                e.preventDefault();
            };
        });
    </script>
@endsection

