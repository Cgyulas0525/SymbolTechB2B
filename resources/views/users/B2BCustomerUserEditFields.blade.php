@section('css')
    @include('layouts.costumcss')
@endsection

<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('customer', \App\Classes\langClass::trans('Cég:')) !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('customer', $users->customerName, ['class'=>'form-control', 'required' => 'true', 'id' => 'customer', 'readonly' => 'true']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('customercontact_id', \App\Classes\langClass::trans('Felhasználó:')) !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('userName', $users->name, ['class'=>'form-control', 'required' => 'true', 'id' => 'userName', 'readonly' => 'true']) !!}
                {!! Form::hidden('customercontact_id', null,['class'=>'form-control', 'required' => 'true', 'id' => 'customercontact_id', 'readonly' => 'true' ]) !!}
                {!! Form::hidden('employee_id', null,['class'=>'form-control', 'required' => 'true', 'id' => 'employee_id', 'readonly' => 'true' ]) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('email', 'Email:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::email('email', $users->email, ['class' => 'form-control','maxlength' => 191, 'id' => 'email', 'readonly' => 'true']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-2">
                {!! Form::label('CustomerAddress', \App\Classes\langClass::trans('Telephely:')) !!}
            </div>
            <div class="mylabel col-sm-10">
                {!! Form::select('CustomerAddress', ddwClass::customerAddressDDW($users->customerId), $users->CustomerAddress,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'CustomerAddress']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('TransportMode', \App\Classes\langClass::trans('Szállítási mód:')) !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::select('TransportMode', ddwClass::transportmodeDDW(), null,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'TransportMode']) !!}
            </div>
        </div>
    </div>
</div>

<div class="form-group col-sm-12">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-1">
                {!! Form::label('megjegyzes', \App\Classes\langClass::trans('Megjegyzés:')) !!}
            </div>
            <div class="mylabel col-sm-11">
                {!! Form::textarea('megjegyzes', null, ['class' => 'form-control', 'rows' => 4, 'id' => 'megjegyzes']) !!}
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @include('functions.ajax_js')
    @include('functions.sweetalert_js')

    <script type="text/javascript">
        $(function () {

            ajaxSetup();

            $('#customer').on('change',function(){
                var customer = $(this).val();
                console.log(customer);
                if(customer){
                    $.ajax({
                        type:"GET",
                        url:"{{url('api/customerContactDDW')}}?customer="+customer,
                        success:function(res){
                            if(res){
                                $("#customercontact_id").empty();
                                $("#customercontact_id").append('<option></option>');
                                $.each(res,function(key,value){
                                    console.log(value);
                                    $("#customercontact_id").append('<option value="'+value.Id+'">'+value.Name+'</option>');
                                });

                                if ( res.length == 1 ) {
                                    $('#customercontact_id').val(res[0].id);
                                }

                            }else{
                                $("#customercontact_id").empty();
                            }
                        }
                    });
                }else{
                    $("#customercontact_id").empty();
                }
            });

            $('#customercontact_id').change(function() {
                let customercontact_id = $('#customercontact_id').val();
                $.ajax({
                    type:"GET",
                    url:"{{url('api/getCustomerContact')}}",
                    data: { id: customercontact_id },
                    success:function(res){
                        if(res.Email != null || res.Email == ''){
                            $("#email").val(res.Email);
                            $('#email').prop('readonly', true);
                            $("#megjegyzes").focus();
                        }else{
                            sw(<?php echo "'" . App\Classes\langClass::trans("Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!") . "'"; ?>);
                            $("#customercontact_id").val(null);
                            $("#customercontact_id").focus();
                        }
                    }
                });
            });

        });

    </script>
@endsection
