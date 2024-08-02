<?php

namespace App\Models;

use App\Enums\Item;

class Rule
{
    public function __construct(
        private readonly Item $item,
        private readonly int $amount,
        private readonly int $price,
    ) {
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
