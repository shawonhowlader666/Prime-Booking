<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Helpers\CurrencyHelper;

class CurrencyHelperTest extends TestCase
{
    public function test_currency_defaults_to_bdt(): void
    {
        $this->assertEquals('BDT', CurrencyHelper::current());
    }

    public function test_currency_conversion_rates(): void
    {
        $usdAmount = CurrencyHelper::convert(12000, 'USD');
        $this->assertEquals(99.6, $usdAmount);

        $formattedBdt = CurrencyHelper::format(12000, 'BDT');
        $this->assertStringContainsString('BDT', $formattedBdt);
    }

    public function test_switching_currency(): void
    {
        CurrencyHelper::setCurrency('USD');
        $this->assertEquals('USD', CurrencyHelper::current());

        CurrencyHelper::setCurrency('BDT');
        $this->assertEquals('BDT', CurrencyHelper::current());
    }
}
