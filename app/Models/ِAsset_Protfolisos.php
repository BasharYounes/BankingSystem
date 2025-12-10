<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ِAsset_Protfolisos extends Model
{
    protected $table = 'ِ_asset__protfolisos';

    protected $fillable = [
        'account_id',
        'asset_symbol',
        'quantity',
        'cost_basis',
    ];

    /**
     * Get the account that owns the asset portfolio.
     */
    public function account()
    {
        return $this->belongsTo(AccountModel::class);
    }
}
