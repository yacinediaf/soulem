<?php

namespace App\ValueObjects;

use App\Models\Store;

class CurrentStore
{
    public function __construct(
        public readonly Store $store,
    ) {}

    public function __get(string $name): mixed
    {
        return $this->store->{$name};
    }

    public function features(): array
    {
        return $this->store->plan->features->pluck('key')->toArray();
    }

    public function hasFeature(string $key): bool
    {
        return in_array($key, $this->features());
    }
}
