<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Checkout;
use App\Models\Ruleset;
use App\Enums\Item;

class TestCheckout extends TestCase
{
    private Ruleset $ruleset;

    public function setUp(): void
    {
        parent::setUp();
        $this->ruleset = new Ruleset();
        
        $this->ruleset->addRule(Item::A, 1, 50);
        $this->ruleset->addRule(Item::B, 1, 30);
        $this->ruleset->addRule(Item::C, 1, 20);
        $this->ruleset->addRule(Item::D, 1, 15);
        $this->ruleset->addRule(Item::A, 3, 130);
        $this->ruleset->addRule(Item::B, 2, 45);
    }

    private function price(string $goods): int
    {
        $co = new Checkout($this->ruleset);

        foreach (str_split($goods) as $good) {
            $co->scan(Item::from($good));
        }

        return $co->getTotal();
    }

    public function test_totals(): void
    {
        $this->assertEquals(  0, $this->price(''));
        $this->assertEquals( 50, $this->price('A'));
        $this->assertEquals( 80, $this->price('AB'));
        $this->assertEquals(115, $this->price('CDBA'));

        $this->assertEquals(100, $this->price('AA'));
        $this->assertEquals(130, $this->price('AAA'));
        $this->assertEquals(180, $this->price('AAAA'));
        $this->assertEquals(230, $this->price('AAAAA'));
        $this->assertEquals(260, $this->price('AAAAAA'));

        
        $this->assertEquals(160, $this->price('AAAB'));
        $this->assertEquals(175, $this->price('AAABB'));
        $this->assertEquals(190, $this->price('AAABBD'));
        $this->assertEquals(190, $this->price('DABABA'));
    }

    public function test_incremental(): void
    {
        $co = new Checkout($this->ruleset);

        $this->assertEquals(0, $co->getTotal());
        $co->scan(Item::A); $this->assertEquals( 50, $co->getTotal());
        $co->scan(Item::B); $this->assertEquals( 80, $co->getTotal());
        $co->scan(Item::A); $this->assertEquals(130, $co->getTotal());
        $co->scan(Item::A); $this->assertEquals(160, $co->getTotal());
        $co->scan(Item::B); $this->assertEquals(175, $co->getTotal());
    }
}
