@section('css')
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

<div class="col-lg-12 col-md-12 col-xs-12">
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary" >
            <div class="box-body">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <section class="content-header">
                        <div class="row">
                            <div class="col-sm-2">
                                <h4><a id="fejszoveg"> {{ langClass::trans('Minden termék') }}</a></h4>
                            </div>
                            @include('tools.pageLength')

                            <div class="form-group col-sm-6">
                                <div class="form-group col-sm-12">
                                    <div class="row">
                                        <div class="mylabel col-sm-3">
                                            {!! Form::label('ProductCategory', langClass::trans('Termékcsoport:')) !!}
                                        </div>
                                        <div class="mylabel col-sm-9">
                                            {!! Form::select('ProductCategory', ddwClass::productProductCategoryDDW(), null,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'ProductCategory']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ( env('FAVORITE_PRODUCTS') == 1)
                                <div class="col-sm-2">
                                    <div class="pull-left">
                                        @include('tools.button', ['akcio' => ["btn btn-primary akcio",
                                                                              "btn btn-success szerzodes",
                                                                              "btn btn-danger kedvenc",
                                                                              "btn btn-dark mind"],
                                                                  'favIcon' => ['fas fa-percent',
                                                                                'fas fa-handshake',
                                                                                'fas fa-heart',
                                                                                'fas fa-warehouse'],
                                                                  'btnName' => ['Akciós',
                                                                                'Szerződéses',
                                                                                'Kedvencek',
                                                                                'Mind'],
                                                                  'title' => ['Akciós termékek',
                                                                              "Szerződéses termékek",
                                                                              'Kedvenc termékek',
                                                                              'Minden termék']])
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>
                    @include('flash::message')
                    <div class="clearfix"></div>
                    <div class="box box-primary">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered partners-table" style="width: 100%;"></table>
                        </div>
                    </div>
                    <div class="text-center"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')

    @include('layouts.datatables_js')
    @include('functions.customNumberFormat_js')
    @include('functions.pageLength_js')

    <script type="text/javascript">

        var table;
        var sCId = <?php echo $shoppingCart->Id; ?>;

        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // $('[data-widget="pushmenu"]').PushMenu('collapse');

            var groupColumn = 3;

            table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 320,
                scrollX: true,
                pagingType: 'full_numbers',
                pageLength: 50,
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Mind"]],
                order: [[groupColumn, 'asc'], [0, 'asc']],
                dom: 'Bfrtip',
                ajax: "{{ route('productIndex', ['PruductCategory' => -99999] ) }}",
                columns: [
                    {title: <?php echo "'" . langClass::trans('Termék') . "'"; ?>, data: 'ProductName', name: 'ProductName'},
                    {title: <?php echo "'" . langClass::trans('Kód') . "'"; ?>, data: 'Code', name: 'Code'},
                    {title: <?php echo "'" . langClass::trans('Mennyiség') . "'"; ?>, data: 'Quantity', width: '150px', name: 'Quantity', id: 'Quntity'},
                    {title: <?php echo "'" . langClass::trans('Termék csoport') . "'"; ?>, data: 'ProductCategoryName', width: '150px', name: 'ProductCategoryName'},
                    {title: <?php echo "'" . langClass::trans('Lista ár') . "'"; ?>, data: 'lastPrice', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'lastPrice'},
                    {title: <?php echo "'" . langClass::trans('Kedv.%') . "'"; ?>, data: 'discountPercent', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'discountPercent'},
                    {title: <?php echo "'" . langClass::trans('Kedv.ár') . "'"; ?>, data: 'productPrice', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'productPrice'},
                    {title: <?php echo "'" . langClass::trans('Me.egys') . "'"; ?>, data: 'QuantityUnitName', name: 'QuantityUnitName'},
                    {title: <?php echo "'" . langClass::trans('Vonalkód') . "'"; ?>, data: 'Barcode', name: 'Barcode'},
                    // {title: '', data: "Id",
                    //     "render": function ( data, type, row, meta ) {
                    //         return '<button value="'+ data +'" onclick="favoriteProduct('+meta["row"]+', this.value)"><i class="fas fa-heart"></i></button>'
                    //     }
                    // }
                ],
                columnDefs: [
                    {
                        targets: [2],
                        render: function ( data, type, row, meta ) {
                            return '<input class="form-control text-right" type="number" value="'+ data +'" onfocusout="QuantityChange('+meta["row"]+', this.value)" pattern="[0-9]+([\.,][0-9]+)?" step="0.0001" style="width:250px;height:20px;font-size: 15px;"/>';
                        },
                    }
                ],
                buttons: [],

            });

            table.on( 'preDraw', function () {
                var count = table.rows( { selected: true } ).count();
                if ( count > 0 ) {
                    rows = table.rows( { selected: true } ).data();
                    for ( i = 0; i < rows.length; i++ ) {
                        console.log(rows[i]);
                    }
                }
            } );
        });

        function myUrlChange ( szoveg, url ) {
            $('#fejszoveg').text(szoveg);
            let productCategory = $('#ProductCategory').val();
            var url = url + '?ProductCategory='+productCategory;
            table.ajax.url(url).load();
        };

        $('.szerzodes').click(function () {
            myUrlChange(<?php echo "'" . langClass::trans('Szerződéses termékek') . "'"; ?>, '{{ route('customerContractProductIndex') }}');
        });

        $('.akcio').click(function () {
            myUrlChange(<?php echo "'" . langClass::trans('Akciós termékek') . "'"; ?>, '{{ route('customerOfferProductIndex') }}');
        });

        $('.kedvenc').click(function () {
            myUrlChange(<?php echo "'" . langClass::trans('Kedvenc termékek') . "'"; ?>, '{{ route('favoriteProductIndex') }}');
        });

        $('.mind').click(function () {
            myUrlChange(<?php echo "'" . langClass::trans('Minden termék ') . "'"; ?>, '{{ route('productIndex') }}');
        });

        myPageLength('#sorszam');

        function modifyDetail(d, value) {
            const url = $(this).attr('href');
            swal.fire({
                title: <?php echo "'" . langClass::trans("Ebben a kosárban már van ilyen termék!") . "'"; ?>,
                text: <?php echo "'" . langClass::trans("Biztosan hozzáadja ezt a mennyiséget?") . "'"; ?>,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Módosítás",
                cancelButtonText: "Kilép"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type:"GET",
                        url:"{{url('api/setShoppingCartDetail')}}",
                        data: { Id: sCId, Product: d.Id, Quantity: value},
                        success: function (response) {
                            console.log('Error:', response);
                            $('#netto').text(custom_number_format(response.NetValue, 4, ',', '.'));
                            $('#vat').text(custom_number_format(response.VatValue, 4, ',', '.'));
                            $('#brutto').text(custom_number_format(response.GrossValue, 4, ',', '.'));
                        },
                        error: function (response) {
                            console.log('Error:', response);
                            alert('nem ok');
                        }
                    });
                }
            });
        }

        function insertDetail(d, value) {
            $.ajax({
                type:"GET",
                url:"{{url('api/insertShoppingCartDetail')}}",
                data: { Id: sCId, Product: d.Id, Quantity: value},
                success: function (response) {
                    console.log('Error:', response);
                    $('#netto').text(custom_number_format(response.NetValue, 4, ',', '.'));
                    $('#vat').text(custom_number_format(response.VatValue, 4, ',', '.'));
                    $('#brutto').text(custom_number_format(response.GrossValue, 4, ',', '.'));
                },
                error: function (response) {
                    // console.log('Error:', response);
                    alert('nem ok');
                }
            });
        }

        function QuantityChange(Row, value) {
            var d = table.row(Row).data();
            if ( value != 0 && value != d.Quantity) {
                $.ajax({
                    type:"GET",
                    url:"{{url('api/getShoppingCartDetail')}}",
                    data: { Id: sCId, Product: d.Id },
                    success: function (response) {
                        if ( response.Id === undefined || response.Id === null  ) {
                            insertDetail(d, value);
                        } else {
                            modifyDetail(d, value);
                        }
                    },
                    error: function (response) {
                        // console.log('Error:', response);
                        alert('nem ok');
                    }
                });
            } else {
                if ( d.Quantity != value || d.Quantity == 0) {
                    // table.row(Row).deselect();
                }
            }
            d.Quantity = value;
            table.row(Row).invalidate();
        }

        $('#ProductCategory').change(function () {
            let productCategory = $('#ProductCategory').val();
            switch($('#fejszoveg').text()) {
                case <?php echo "'" . langClass::trans('Szerződéses termékek') . "'"; ?>:
                    myUrlChange(<?php echo "'" . langClass::trans('Szerződéses termékek') . "'"; ?>, '{{ route('customerContractProductIndex') }}');
                    break;
                case <?php echo "'" . langClass::trans('Kedvenc termékek') . "'"; ?>:
                    myUrlChange(<?php echo "'" . langClass::trans('Kedvenc termékek') . "'"; ?>, '{{ route('favoriteProductIndex') }}');
                    break;
                case <?php echo "'" . langClass::trans('Akciós termékek') . "'"; ?>:
                    myUrlChange(<?php echo "'" . langClass::trans('Akciós termékek') . "'"; ?>, '{{ route('customerOfferProductIndex') }}');
                    break
                default:
                    myUrlChange(<?php echo "'" . langClass::trans('Minden termék') . "'"; ?>, '{{ route('productIndex') }}');
            }
        })

        // function favoriteProduct(Row, value){
        //     var d = table.row(Row).data();
        //     alert(d.Id, d.ProductName);
        // }


    </script>
@endsection
