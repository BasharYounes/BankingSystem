<?php

namespace App\Models;

use App\Factories\StrategyFactory;
use App\Interfaces\Account;
use Illuminate\Database\Eloquent\Model;

class SellTransaction extends Transaction
{

    protected $attributes = [
        'type' => 'sellAsset',
    ];

    public function execute(): bool
    {
        try {
            $this->setStatus(self::STATUS_PROCESSING);
            \Log::info('بدء تنفيذ عملية البيع');
            \Log::info('Status'.$this->getStatus());

            if (!$this->validate()) {
                throw new \Exception('فشل التحقق من صحة المعاملة');
            }

            $fromModelAccount = AccountModel::find($this->from_account_id);
            $toModelAccount = AccountModel::find($this->to_account_id);

            if (!$fromModelAccount) {
                throw new \Exception('الحساب غير موجود');
            }

            $assertSymbol = $this->metadata['asset_symbol'];
            $quantity = $this->metadata['quantity'];


            $assetProtfolio = ِAsset_Protfolisos::where('account_id', $fromModelAccount->id)
                                    ->where('asset_symbol', $assertSymbol)
                                    ->firstOrFail();

            $strategy = StrategyFactory::getInstance()->createStrategy($fromModelAccount, $this->amount);
            // تنفيذ البيع
            $strategy->sellAsset($assertSymbol, $quantity, $this->amount);
            \Log::info('تم البيع بنجاح من الحساب رقم: ' . $fromModelAccount->account_number);

            $strategy->buyAsset($assertSymbol, $quantity, $this->metadata['price_per_unit'],$toModelAccount);
            \Log::info('تم الشراء بنجاح في الحساب رقم: ' . $toModelAccount->account_number);

            $this->setStatus(self::STATUS_COMPLETED);
            \Log::info('اكتملت عملية البيع بنجاح');
            \Log::info('Status'.$this->getStatus());
            return true;

        } catch (\Exception $e) {
            $this->setStatus(self::STATUS_FAILED);
            \Log::error('فشل البيع: ' . $e->getMessage());
            return false;
        }
    }
    public function validate(): bool
    {
        if ($this->amount <= 0) {
            return false;
        }

        if (!isset($this->metadata['asset_symbol']) || !isset($this->metadata['quantity'])) {
            return false;
        }

        return true;
    }
}
