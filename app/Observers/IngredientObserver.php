<?php

namespace App\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendLowStockAlert;
use App\Models\Ingredient;

class IngredientObserver implements ShouldHandleEventsAfterCommit
{
    public function updated(Ingredient $ingredient): void
    {
        if ($this->shouldSendEmail($ingredient)) {
            $ingredient->load('merchant');
            Bus::chain([
                new SendLowStockAlert($ingredient),
                fn () => $ingredient->update(['merchant_notified_at' => now()]),
            ])->catch(function () {
                // Send notification to admin
            })->dispatch();
        }
    }

    private function shouldSendEmail(Ingredient $ingredient): bool
    {
        return $ingredient->current_stock < ($ingredient->original_stock / 2) && empty($ingredient->merchant_notified_at);
    }
}
