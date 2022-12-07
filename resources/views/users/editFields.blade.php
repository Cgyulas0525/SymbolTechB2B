@section('css')
    @include('layouts.costumcss')
@endsection

<div class="form-group col-sm-5">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('employee_id', \App\Classes\langClass::trans('Felhasználó:')) !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('name', $users->name,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'name', 'readonly' => 'true' ]) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-2">
                {!! Form::label('email', 'Email:') !!}
            </div>
            <div class="mylabel col-sm-10">
                {!! Form::email('email', $users->email, ['class' => 'form-control','maxlength' => 191,'maxlength' => 191, 'id' => 'email', 'readonly' => 'true']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-2">
                {!! Form::label('rendszergazda', \App\Classes\langClass::trans('Státusz:')) !!}
            </div>
            <div class="mylabel col-sm-10">
                {!! Form::select('rendszergazda', ddwClass::belsoStatuszDDW(), $users->rendszergazda + 1,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'rendszergazda']) !!}
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
                {!! Form::textarea('megjegyzes', $users->megjegyzes, ['class' => 'form-control', 'rows' => 4, 'id' => 'megjegyzes']) !!}
            </div>
        </div>
    </div>
</div>

@section('scripts')

    @include('functions.sweetalert_js')

    <script type="text/javascript">
        $(function () {

            $('#saveBtn').click(function (e) {
                let rendszergazda = $('#rendszergazda').val();
                if ( parseInt(rendszergazda) == 0) {
                    swMove(<?php echo "'" . App\Classes\langClass::trans("Nem adott meg státuszt!") . "'"; ?>);
                    e.preventDefault();
                    $('#rendszergazda').focus();
                    return false;
                }
            });
        });

    </script>

@endsection

