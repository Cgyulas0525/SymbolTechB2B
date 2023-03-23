<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customercontractdetail', function (Blueprint $table) {
            $table->bigInteger('Id')->unique('PK_CustomerContractDetail');
            $table->bigInteger('CustomerContract')->index('FK_CustomerContractDetail_CustC');
            $table->bigInteger('Product')->index('FK_CustomerContractDetail_Prod');
            $table->decimal('ShareQuantity', 18, 4)->nullable()->index('IDX_CustomerContractDetail_SQ');
            $table->decimal('Price', 18, 4)->default(0);
            $table->bigInteger('Currency')->index('FK_CustomerContractDetail_Curr');
            $table->bigInteger('QuantityUnit')->nullable()->index('FK_CustomerContractDetail_QUnit');
            $table->decimal('InvoiceQty', 18, 4)->default(0)->index('IDX_CustomerContractDetail_InvQ');
            $table->bigInteger('Vat')->index('FK_CustomerContractDetail_Vat');
            $table->timestamp('ValidFrom')->useCurrent();
            $table->timestamp('ValidTo')->nullable();
            $table->binary('InvoiceOccurence')->nullable();
            $table->smallInteger('SuppressPriceAffect')->default(0);
            $table->smallInteger('OfferOverride')->default(0);
            $table->binary('Comment')->nullable();
            $table->smallInteger('CopyCommentToInvoice')->default(0);
            $table->timestamp('RowVersion')->useCurrent();
            $table->smallInteger('Deleted')->default(0);
            $table->integer('RowOrder')->default(0);
            $table->bigInteger('Investment')->nullable()->index('FK_CustomerContractDetail_Inv');

            $table->index(['ValidFrom', 'ValidTo'], 'IDX_CustomerContractDetail_PCQ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customercontractdetail');
    }
};
