<?php

namespace App\Models;

use App\Enums\Item;

class Ruleset
{
    /* @var Rule[] $rules */
    private array $rules = [];

    public function __construct()
    {
    }

    public function addRule(Item $item, int $amount, int $price): void
    {
        $this->rules[] = new Rule($item, $amount, $price);
    }

    public function itemExist(Item $item): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->getItem() === $item) {
                return true;
            }
        }

        return false;
    }

    public function getHighestAmountRuleForItem(Item $item, int $maxAmount = 1): ?Rule
    {
        $highest = null;

        foreach ($this->rules as $rule) {
            if ($rule->getItem() === $item
                && $rule->getAmount() <= $maxAmount
                && $rule->getAmount() > ($highest ? $highest->getAmount() : 0)
            ) {
                $highest = $rule;
            }
        }

        return $highest;
    }
}
