@section('css')
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

<?php $category = -888888; ?>

<!-- Customercontact Id Field -->
<div class="form-group col-sm-12">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel8 col-sm-2">
                {!! Form::label('customercontact_id', \App\Classes\langClass::trans('Termék kategória') . ':', ['class'=>'font20px']) !!}
            </div>
            <div class="mylabel8 col-sm-6">
                {!! Form::select('productcategory', ddwClass::productCategoryDDW(), null,['class'=>'select2 form-control', 'id' => 'productCategory']) !!}
            </div>
            <div class="mylabel8 col-sm-2">
                <a href="#" class="btn btn-dark mind" title={{ \App\Classes\langClass::trans('Minden termék') }}><i class="fas fa-warehouse"></i></a>
                <a href="#" class="btn btn-primary kedvenc" title={{ \App\Classes\langClass::trans('Kedvenc') }}><i class="fas fa-hand-holding-heart"></i></a>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-xs-12">
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

@section('scripts')

    @include('layouts.datatables_js')
    @include('functions.customNumberFormat_js')

    <script type="text/javascript">

        var table;
        var customerContact = <?php echo myUser::user()->customercontact_id; ?>;

        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 320,
                scrollX: true,
                pagingType: 'full_numbers',
                pageLength: 50,
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Mind"]],
                order: [[0, 'asc']],
                dom: 'Bfrtip',
                ajax: "{{ route('productCategoryProductindex', $category ) }}",
                columns: [
                    {title: <?php echo "'" . App\Classes\langClass::trans('Termék') . "'"; ?>, data: 'Name', name: 'Name'},
                ],
                buttons: [],

            });

            function filteredData() {
                let productCategory = $('#productCategory').val().length != 0 ? $('#productCategory').val() : -999999;
                let url = '{{ route('productCategoryProductindex', [":category"]) }}';
                url = url.replace(':category',  productCategory);
                table.ajax.url(url).load();
            }

            $('.mind').click(function () {
                $('#productCategory').val('');
                let url = '{{ route('productCategoryProductindex', -999999) }}';
                table.ajax.url(url).load();
            });

            $('#productCategory').change(function() {
                filteredData();
            });

            $('.kedvenc').click(function () {
                let productId = null;
                let selectedCount = table.rows( '.selected' ).count();
                if (selectedCount > 0) {
                    let selectedRows = table.rows( '.selected' );
                    for ( i = 0; i < selectedRows[0].length; i++) {
                        productId = table.rows(selectedRows[0][i]).data();
                        $.ajax({
                            type:"GET",
                            url:"{{url('api/makeCustomerContactFavoriteProduct')}}",
                            data: { customerContact: customerContact, product: productId[0].Id },
                            success: function (response) {
                                console.log('Error:', response);
                            },
                            error: function (response) {
                                // console.log('Error:', response);
                                alert('nem ok');
                            }
                        });
                    }
                    filteredData();
                } else {
                    let stitle = <?php echo "'" . App\Classes\langClass::trans('Nem jelölt ki sort') . "'"; ?>;
                    alert(stitle);
                }
            });

        });

    </script>
@endsection
