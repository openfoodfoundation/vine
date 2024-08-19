<?php

namespace Tests\Unit\Enums;

use App\Enums\PersonalAccessTokenAbility;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonalAccessTokenAbilityEnumTest extends TestCase
{
    #[Test]
    public function itReturnsAllAbilityLabels(): void
    {
        self::assertIsArray(PersonalAccessTokenAbility::abilityLabels());
        self::assertSameSize(PersonalAccessTokenAbility::cases(), PersonalAccessTokenAbility::abilityLabels());

        foreach (PersonalAccessTokenAbility::abilityLabels() as $ability => $label) {
            self::assertContains($ability, PersonalAccessTokenAbility::values());
        }
    }

    #[Test]
    public function itReturnsPlatformAppTokenAbilities(): void
    {
        $expectedPlatformAppTokenAbilities = [
            PersonalAccessTokenAbility::TEAMS_READ->value             => PersonalAccessTokenAbility::abilityLabels()[PersonalAccessTokenAbility::TEAMS_READ->value],
            PersonalAccessTokenAbility::TEAMS_CREATE->value           => PersonalAccessTokenAbility::abilityLabels()[PersonalAccessTokenAbility::TEAMS_CREATE->value],
            PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value => PersonalAccessTokenAbility::abilityLabels()[PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value],
        ];

        self::assertIsArray(PersonalAccessTokenAbility::platformAppTokenAbilities());
        self::assertSameSize($expectedPlatformAppTokenAbilities, PersonalAccessTokenAbility::platformAppTokenAbilities());

        foreach (PersonalAccessTokenAbility::platformAppTokenAbilities() as $key => $label) {
            self::assertContains($key, array_keys($expectedPlatformAppTokenAbilities));
            self::assertContains($label, $expectedPlatformAppTokenAbilities);
            self::assertSame($label, $expectedPlatformAppTokenAbilities[$key]);
        }
    }
}
