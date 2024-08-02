<?php

namespace App\Models;

use App\Enums\Item;

class Checkout
{
    /* @var Item[] $items */
    private array $items = [];

    public function __construct(private readonly Ruleset $ruleset)
    {
    }

    public function scan(Item $item): void
    {
        if (!$this->ruleset->itemExist($item)) {
            throw new \Exception("Item does not exist in ruleset");
        }

        $this->items[] = $item;
    }

    public function getTotal(): int
    {
        $amounts = [];
        $total = 0;

        foreach ($this->items as $item) {
            if (!isset($amounts[$item->value])) {
                $amounts[$item->value] = 0;
            }

            $amounts[$item->value] += 1;
        }

        foreach ($amounts as $item => $amount) {
            while ($amount > 0) {
                $rule = $this->ruleset->getHighestAmountRuleForItem(
                    Item::from($item),
                    $amount,
                );

                if ($rule === null) {
                    throw new \Exception(sprintf('There is no rule for item %s, for such small amount (%d). Either increase the amount by scanning more of the same item or add a rule for the amount', $item, $amount));
                }

                $total += $rule->getPrice() * (int)($amount / $rule->getAmount());
                $amount = $amount % $rule->getAmount();
            }
        }

        return $total;
    }
}
