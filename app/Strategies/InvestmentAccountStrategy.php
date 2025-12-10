<?php

namespace App\Strategies;

use App\Models\AccountModel;
use App\Models\ِAsset_Protfolisos;
use DB;

class InvestmentAccountStrategy extends AccountStrategy
{
    protected AccountModel $accountModel;
    protected float $amount;

    public function __construct(AccountModel $accountModel, float $amount)
    {
        $this->accountModel = $accountModel;
        $this->amount = $amount;
    }

    /**
     * الإيداع: زيادة الرصيد النقدي المتاح في حساب الاستثمار (Funding).
     */
    public function deposit(): bool
    {
        try {
            $this->validateAmount($this->amount);
            $this->validateStatus();

            // منطق الإيداع يضيف المبلغ إلى الرصيد النقدي (balance)
            $newBalance = $this->getBalance() + $this->amount;
            $this->updateBalance($this->accountModel, $newBalance);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('فشل الإيداع: ' . $e->getMessage());
        }
    }

    /**
     * السحب: تقليل الرصيد النقدي المتاح للسحب.
     */
    public function withdraw(): bool
    {
        try {
            $this->validateAmount($this->amount);
            $this->validateStatus();

            // يجب التحقق من أن الرصيد النقدي كافٍ للسحب
            if ($this->getBalance() < $this->amount) {
                throw new \Exception('رصيد الحساب النقدي غير كافٍ للسحب.');
            }

            // منطق السحب يطرح المبلغ من الرصيد النقدي (balance)
            $newBalance = $this->getBalance() - $this->amount;
            $this->updateBalance($this->accountModel, $newBalance);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('فشل السحب: ' . $e->getMessage());
        }
    }

    /**
     * التحويل: تحويل الأموال من الرصيد النقدي للحساب إلى حساب آخر.
     */
    public function transfer(AccountModel $toAccount): bool
    {
        try {
            $this->validateAmount($this->amount);
            $this->validateStatus();
            $this->validateTargetStatus($toAccount); // التحقق من حالة الحساب الهدف

            // التحقق من الرصيد النقدي الكافي للتحويل
            if ($this->getBalance() < $this->amount) {
                throw new \Exception('رصيد الحساب النقدي غير كافٍ للتحويل.');
            }

            // ضمان أن تكون العملية ذرية (Transactional)
            DB::transaction(function () use ($toAccount) {
                // 1. سحب المبلغ من حساب الاستثمار (الرصيد النقدي)
                $this->updateBalance($this->accountModel, $this->getBalance() - $this->amount);

                // 2. إيداع المبلغ في الحساب الهدف (يجب استخدام استراتيجية الإيداع الخاصة بالحساب الهدف في تطبيق حقيقي)
                $this->updateBalance($toAccount, $toAccount->balance + $this->amount);
            });

            return true;
        } catch (\Exception $e) {
            throw new \Exception('فشل التحويل: ' . $e->getMessage());
        }
    }

    // ------------------------------------------------------------------
    // العمليات الخاصة بحساب الاستثمار (Investment Specific Operations)
    // ------------------------------------------------------------------

    /**
     * [عملية أساسية] شراء أصل مالي (مثل سهم أو سند) باستخدام الرصيد النقدي.
     * @param string $assetSymbol رمز الأصل (مثل: TASI)
     * @param float $quantity الكمية المراد شراؤها
     * @param float $assetPrice سعر الوحدة الواحدة
     */
    public function buyAsset(string $assetSymbol, float $quantity, float $assetPrice, AccountModel $toAccount): bool
    {
        $totalCost = $quantity * $assetPrice;
        $this->validateAmount($totalCost);
        $this->validateStatus();

        if ($this->getBalance() < $totalCost) {
            throw new \Exception('الرصيد النقدي غير كافٍ لإتمام عملية الشراء.');
        }

        $assetHolding = ِAsset_Protfolisos::updateOrCreate(
                [
                    'account_id' => $toAccount->id,
                    'asset_symbol' => $assetSymbol,
                ],
                [
                    // يتم تحديث الكمية (سنقوم بالزيادة هنا)
                    'quantity' => DB::raw("quantity + {$quantity}"),

                    // تحديث متوسط تكلفة الشراء (منطق متقدم لكننا نبسطه للمثال)
                    'cost_basis' => $assetPrice,
                ]
            );

            // 3. (اختياري) تسجيل المعاملة في سجل التدقيق
            // TransactionLog::create(...)

        return true;
    }

    /**
     * [عملية أساسية] بيع أصل مالي وإضافة العائدات إلى الرصيد النقدي.
     * @param string $assetSymbol رمز الأصل
     * @param float $quantity الكمية المراد بيعها
     * @param float $assetPrice سعر الوحدة الواحدة
     */
    public function sellAsset(string $assetSymbol, float $quantity, float $assetPrice): bool
    {
        // المنطق يتحقق من ملكية الكمية ويطرحها من محفظة الأصول ويضيف العائدات إلى الرصيد النقدي
        $saleProceeds = $quantity * $assetPrice;
        $this->validateAmount($saleProceeds);

        $holding = ِAsset_Protfolisos::where('account_id', $this->accountModel->id)
                                    ->where('asset_symbol', $assetSymbol)
                                    ->firstOrFail();

        if (!$holding || $holding->quantity < $quantity) {
            throw new \Exception('لا تملك الكمية الكافية من الأصل ('.$assetSymbol.'( لإتمام عملية البيع.');
        }

        // 3. ضمان الذرية (Atomic Transaction)
        DB::transaction(function () use ($holding, $quantity, $saleProceeds) {

            // أ. تخفيض محفظة الأصول (جدول asset_portfolios)

            $newQuantity = $holding->quantity - $quantity;

            if ($newQuantity < 0) {
                 // يجب أن يكون هذا التحقق زائداً إذا نجح التحقق في الخطوة 2
                 throw new \Exception('خطأ في حساب الكمية المتبقية.');
            }

            // تحديث الكمية (إذا كانت الكمية الجديدة صفراً، يمكن حذف السجل)
            if ($newQuantity == 0) {
                $holding->delete();
            } else {
                $holding->quantity = $newQuantity;
                // يجب إعادة حساب Cost Basis هنا لتمثيل الربح/الخسارة الضريبية
                $holding->save();
            }

            // ب. إضافة عائدات البيع إلى الرصيد النقدي (جدول accounts)
            $newBalance = $this->getBalance() + $saleProceeds;
            $this->updateBalance($this->accountModel, $newBalance);

            // 4. (اختياري) تسجيل الأرباح أو الخسائر المحققة (متقدم)
        });

        return true;
    }

}
