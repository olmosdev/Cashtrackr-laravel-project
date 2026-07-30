<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(["name", "amount", "category", "budget_id"])]
class Expense extends Model
{
    use SoftDeletes;
    // Establishing an inverse relationship of Expenses N:1 Budget
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
