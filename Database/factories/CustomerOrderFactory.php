<?php

namespace Database\Factories;

use App\Models\CustomerOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'VoucherType' => $this->faker->randomDigitNotNull,
        'VoucherSequence' => $this->faker->word,
        'VoucherNumber' => $this->faker->word,
        'PrimeVoucherNumber' => $this->faker->word,
        'CancelledVoucher' => $this->faker->word,
        'MaintenanceProduct' => $this->faker->word,
        'Customer' => $this->faker->word,
        'CustomerAddress' => $this->faker->word,
        'CustomerContact' => $this->faker->word,
        'VoucherDate' => $this->faker->date('Y-m-d H:i:s'),
        'DeliveryDate' => $this->faker->date('Y-m-d H:i:s'),
        'DeliveryFrom' => $this->faker->date('Y-m-d H:i:s'),
        'DeliveryTo' => $this->faker->date('Y-m-d H:i:s'),
        'PaymentMethod' => $this->faker->word,
        'Currency' => $this->faker->word,
        'CurrencyRate' => $this->faker->word,
        'Investment' => $this->faker->word,
        'Division' => $this->faker->word,
        'Agent' => $this->faker->word,
        'ContactEmployee' => $this->faker->word,
        'Campaign' => $this->faker->word,
        'CustomerContract' => $this->faker->word,
        'Warehouse' => $this->faker->word,
        'TransportMode' => $this->faker->word,
        'DepositValue' => $this->faker->word,
        'DepositPercent' => $this->faker->word,
        'NetValue' => $this->faker->word,
        'GrossValue' => $this->faker->word,
        'VatValue' => $this->faker->word,
        'AmountAsk' => $this->faker->word,
        'Maintenance' => $this->faker->word,
        'SplitForbid' => $this->faker->word,
        'PrimePostage' => $this->faker->word,
        'OrderHidePrice' => $this->faker->word,
        'Closed' => $this->faker->word,
        'ClosedManually' => $this->faker->word,
        'Comment' => $this->faker->word,
        'Cancelled' => $this->faker->word,
        'RowVersion' => $this->faker->date('Y-m-d H:i:s'),
        'MaintOrderSrcCustOrder' => $this->faker->word,
        'ExpirationDate' => $this->faker->date('Y-m-d H:i:s'),
        'InternalComment' => $this->faker->word,
        'FinalizedDate' => $this->faker->date('Y-m-d H:i:s'),
        'ParcelShop' => $this->faker->word,
        'StrExA' => $this->faker->word,
        'StrExB' => $this->faker->word,
        'StrExC' => $this->faker->word,
        'StrExD' => $this->faker->word,
        'DateExA' => $this->faker->date('Y-m-d H:i:s'),
        'DateExB' => $this->faker->date('Y-m-d H:i:s'),
        'DateExC' => $this->faker->date('Y-m-d H:i:s'),
        'DateExD' => $this->faker->date('Y-m-d H:i:s'),
        'NumExA' => $this->faker->word,
        'NumExB' => $this->faker->word,
        'NumExC' => $this->faker->word,
        'NumExD' => $this->faker->word,
        'BoolExA' => $this->faker->word,
        'BoolExB' => $this->faker->word,
        'BoolExC' => $this->faker->word,
        'BoolExD' => $this->faker->word,
        'LookupExA' => $this->faker->word,
        'LookupExB' => $this->faker->word,
        'LookupExC' => $this->faker->word,
        'LookupExD' => $this->faker->word,
        'MemoExA' => $this->faker->word,
        'MemoExB' => $this->faker->word,
        'MemoExC' => $this->faker->word,
        'MemoExD' => $this->faker->word,
        'RowCreate' => $this->faker->date('Y-m-d H:i:s'),
        'RowModify' => $this->faker->date('Y-m-d H:i:s'),
        'NotifyPhone' => $this->faker->word,
        'NotifySms' => $this->faker->word,
        'NotifyEmail' => $this->faker->word,
        'PublicHealthPTFree' => $this->faker->word,
        'FabricDeadLine' => $this->faker->date('Y-m-d H:i:s'),
        'CheckoutBankAccount' => $this->faker->word,
        'OriginalVoucher' => $this->faker->word,
        'DepositGross' => $this->faker->word,
        'ExchangePackage' => $this->faker->word,
        'ChainTransaction' => $this->faker->word,
        'ValidityDate' => $this->faker->date('Y-m-d H:i:s'),
        'CurrRateDate' => $this->faker->date('Y-m-d H:i:s'),
        'CancelReason' => $this->faker->word,
        'CustomerOrderStatus' => $this->faker->word,
        'BankTRID' => $this->faker->word,
        'CloseReason' => $this->faker->word
        ];
    }
}
