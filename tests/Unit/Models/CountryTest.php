<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use League\ISO3166\ISO3166;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use function Aws\map;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function testSeedsAllCountriesExactlyOnce(): void
    {
        $countries = Country::all();

        $isoList = new ISO3166();

        $this->assertCount(count($isoList->all()), $countries);

        $expectedCountryNames = collect($isoList->all())
            ->pluck('name')
            ->map(fn (string $countryName) => ucwords(strtolower(trim($countryName))))
            ->toArray();

        foreach ($countries as $country) {
            $name = ucwords(strtolower(trim($country->name)));
            $this->assertContains($name, $expectedCountryNames, $name . ' not in ' . json_encode($expectedCountryNames));
        }
    }
}
