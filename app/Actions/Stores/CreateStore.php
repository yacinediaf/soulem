<?php

namespace App\Actions\Stores;

use App\Enums\PlanSlug;
use App\Models\Plan;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateStore
{
    public function handle(User $user, string $name): Store
    {
        return DB::transaction(function () use ($user, $name) {
            $plan = Plan::where('slug', PlanSlug::Free)->firstOrFail();

            $store = $user->stores()->create([
                'name' => $name,
                'plan_id' => $plan->id,
            ]);

            $user->update(['current_store_id' => $store->id]);

            return $store->load('plan');
        });
    }
}
