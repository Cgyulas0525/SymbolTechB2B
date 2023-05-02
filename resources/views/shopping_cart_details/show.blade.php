@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $shoppingcartdetail->shoppingCartRelation->VoucherNumber }}</h1>
                    <h1>{{ $shoppingcartdetail->productRelation->Name }}</h1>
                </div>
            </div>
        </div>
    </section>
@endsection
