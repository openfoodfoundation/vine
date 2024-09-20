<?php

namespace App\Enums;

/**
 * Configure Personal Access Token Abilities here.
 */
enum PersonalAccessTokenAbility: string
{
    case SUPER_ADMIN                             = 'super-admin'; // Allowed to do everything
    case COUNTRIES_CREATE                        = 'countries-create';
    case COUNTRIES_READ                          = 'countries-read';
    case COUNTRIES_UPDATE                        = 'countries-update';
    case COUNTRIES_DELETE                        = 'countries-delete';
    case MY_PROFILE_CREATE                       = 'my-profile-create';
    case MY_PROFILE_READ                         = 'my-profile-read';
    case MY_PROFILE_UPDATE                       = 'my-profile-update';
    case MY_PROFILE_DELETE                       = 'my-profile-delete';
    case MY_TEAM_CREATE                          = 'my-team-create';
    case MY_TEAM_READ                            = 'my-team-read';
    case MY_TEAM_UPDATE                          = 'my-team-update';
    case MY_TEAM_DELETE                          = 'my-team-delete';
    case MY_TEAM_AUDIT_ITEMS_CREATE              = 'my-team-audit-items-create';
    case MY_TEAM_AUDIT_ITEMS_READ                = 'my-team-audit-items-read';
    case MY_TEAM_AUDIT_ITEMS_UPDATE              = 'my-team-audit-items-update';
    case MY_TEAM_AUDIT_ITEMS_DELETE              = 'my-team-audit-items-delete';
    case MY_TEAM_VOUCHERS_CREATE                 = 'my-team-vouchers-create';
    case MY_TEAM_VOUCHERS_READ                   = 'my-team-vouchers-read';
    case MY_TEAM_VOUCHERS_UPDATE                 = 'my-team-vouchers-update';
    case MY_TEAM_VOUCHERS_DELETE                 = 'my-team-vouchers-delete';
    case SHOPS_CREATE                            = 'shops-create';
    case SHOPS_READ                              = 'shops-read';
    case SHOPS_UPDATE                            = 'shops-update';
    case SHOPS_DELETE                            = 'shops-delete';
    case SYSTEM_STATISTICS_CREATE                = 'system-statistics-create';
    case SYSTEM_STATISTICS_READ                  = 'system-statistics-read';
    case SYSTEM_STATISTICS_UPDATE                = 'system-statistics-update';
    case SYSTEM_STATISTICS_DELETE                = 'system-statistics-delete';
    case VOUCHER_BENEFICIARY_DISTRIBUTION_CREATE = 'voucher-beneficiary-distributions-create';
    case VOUCHER_BENEFICIARY_DISTRIBUTION_READ   = 'voucher-beneficiary-distributions-read';
    case VOUCHER_BENEFICIARY_DISTRIBUTION_UPDATE = 'voucher-beneficiary-distributions-update';
    case VOUCHER_BENEFICIARY_DISTRIBUTION_DELETE = 'voucher-beneficiary-distributions-delete';
    case VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_CREATE = 'voucher-set-merchant-team-approval-requests-create';
    case VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_READ   = 'voucher-set-merchant-team-approval-requests-read';
    case VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_UPDATE = 'voucher-set-merchant-team-approval-requests-update';
    case VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_DELETE = 'voucher-set-merchant-team-approval-requests-delete';
    case VOUCHER_REDEMPTIONS_CREATE              = 'voucher-redemptions-create';
    case VOUCHER_REDEMPTIONS_READ                = 'voucher-redemptions-read';
    case VOUCHER_REDEMPTIONS_UPDATE              = 'voucher-redemptions-update';
    case VOUCHER_REDEMPTIONS_DELETE              = 'voucher-redemptions-delete';
    case MY_TEAM_SEARCH_CREATE                   = 'my-team-search-create';
    case MY_TEAM_SEARCH_READ                     = 'my-team-search-read';
    case MY_TEAM_SEARCH_UPDATE                   = 'my-team-search-update';
    case MY_TEAM_SEARCH_DELETE                   = 'my-team-search-delete';
    case MY_TEAM_VOUCHER_SETS_CREATE             = 'my-team-voucher-sets-create';
    case MY_TEAM_VOUCHER_SETS_READ               = 'my-team-voucher-sets-read';
    case MY_TEAM_VOUCHER_SETS_UPDATE             = 'my-team-voucher-sets-update';
    case MY_TEAM_VOUCHER_SETS_DELETE             = 'my-team-voucher-sets-delete';

    public static function abilityLabels(): array
    {

        return [
            self::SUPER_ADMIN->value                             => 'Super Admin: Perform most API actions',
            self::COUNTRIES_CREATE->value                        => 'Countries Create',
            self::COUNTRIES_READ->value                          => 'Countries Read',
            self::COUNTRIES_UPDATE->value                        => 'Countries Update',
            self::COUNTRIES_DELETE->value                        => 'Countries Delete',
            self::MY_PROFILE_CREATE->value                       => 'My Profile Create',
            self::MY_PROFILE_READ->value                         => 'My Profile Read',
            self::MY_PROFILE_UPDATE->value                       => 'My Profile Update',
            self::MY_PROFILE_DELETE->value                       => 'My Profile Delete',
            self::MY_TEAM_CREATE->value                          => 'My Team Create',
            self::MY_TEAM_READ->value                            => 'My Team Read',
            self::MY_TEAM_UPDATE->value                          => 'My Team Update',
            self::MY_TEAM_DELETE->value                          => 'My Team Delete',
            self::MY_TEAM_AUDIT_ITEMS_CREATE->value              => 'My Team Audit Items Create',
            self::MY_TEAM_AUDIT_ITEMS_READ->value                => 'My Team Audit Items Read',
            self::MY_TEAM_AUDIT_ITEMS_UPDATE->value              => 'My Team Audit Items Update',
            self::MY_TEAM_AUDIT_ITEMS_DELETE->value              => 'My Team Audit Items Delete',
            self::MY_TEAM_SEARCH_CREATE->value                   => 'My Team Search Create',
            self::MY_TEAM_SEARCH_READ->value                     => 'My Team Search Read',
            self::MY_TEAM_SEARCH_UPDATE->value                   => 'My Team Search Update',
            self::MY_TEAM_SEARCH_DELETE->value                   => 'My Team Search Delete',
            self::MY_TEAM_VOUCHERS_CREATE->value                 => 'My Team Vouchers Create',
            self::MY_TEAM_VOUCHERS_READ->value                   => 'My Team Vouchers Read',
            self::MY_TEAM_VOUCHERS_UPDATE->value                 => 'My Team Vouchers Update',
            self::MY_TEAM_VOUCHERS_DELETE->value                 => 'My Team Vouchers Delete',
            self::MY_TEAM_VOUCHER_SETS_CREATE->value             => 'My Team Voucher Sets Create',
            self::MY_TEAM_VOUCHER_SETS_READ->value               => 'My Team Voucher Sets Read',
            self::MY_TEAM_VOUCHER_SETS_UPDATE->value             => 'My Team Voucher Sets Update',
            self::MY_TEAM_VOUCHER_SETS_DELETE->value             => 'My Team Voucher Sets Delete',
            self::SHOPS_CREATE->value                            => 'Shops Create: Create a shop that redeems vouchers',
            self::SHOPS_READ->value                              => 'Shops Read: Retrieve shop details from the API',
            self::SHOPS_UPDATE->value                            => 'Shops Update: Update a shop',
            self::SHOPS_DELETE->value                            => 'Shops Delete: Delete a shop',
            self::SYSTEM_STATISTICS_CREATE->value                => 'System Statistics Create',
            self::SYSTEM_STATISTICS_READ->value                  => 'System Statistics Read',
            self::SYSTEM_STATISTICS_UPDATE->value                => 'System Statistics Update',
            self::SYSTEM_STATISTICS_DELETE->value                => 'System Statistics Delete',
            self::VOUCHER_BENEFICIARY_DISTRIBUTION_CREATE->value => 'Voucher Beneficiary Distribution Create',
            self::VOUCHER_BENEFICIARY_DISTRIBUTION_READ->value   => 'Voucher Beneficiary Distribution Read',
            self::VOUCHER_BENEFICIARY_DISTRIBUTION_UPDATE->value => 'Voucher Beneficiary Distribution Update',
            self::VOUCHER_BENEFICIARY_DISTRIBUTION_DELETE->value => 'Voucher Beneficiary Distribution Delete',
            self::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_CREATE->value => 'Voucher Set Merchant Team Approval Request Create',
            self::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_READ->value   => 'Voucher Set Merchant Team Approval Request Read',
            self::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_UPDATE->value => 'Voucher Set Merchant Team Approval Request Update',
            self::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_DELETE->value => 'Voucher Set Merchant Team Approval Request Delete',
            self::VOUCHER_REDEMPTIONS_CREATE->value              => 'Voucher Redemptions Create',
            self::VOUCHER_REDEMPTIONS_READ->value                => 'Voucher Redemptions Read',
            self::VOUCHER_REDEMPTIONS_UPDATE->value              => 'Voucher Redemptions Update',
            self::VOUCHER_REDEMPTIONS_DELETE->value              => 'Voucher Redemptions Delete',
        ];
    }

    /**
     * The abilities that a "platform" app API token should have.
     *
     * Example: The OFN platform has a shop in its organisation chart, and the shop opts in to the vouchers' system.
     * The OFN API token needs to create the team in the vouchers API, create a user for the shop, create an API
     * token for that user, and save the API token locally in the OFN DB so that the shop may perform actions like redeeming, etc.
     *
     * @return PersonalAccessTokenAbility[]
     */
    public static function platformAppTokenAbilities(): array
    {
        return [
            self::SHOPS_CREATE->value           => self::abilityLabels()[self::SHOPS_CREATE->value],
            self::SHOPS_READ->value             => self::abilityLabels()[self::SHOPS_READ->value],
            self::SYSTEM_STATISTICS_READ->value => self::abilityLabels()[self::SYSTEM_STATISTICS_READ->value],
        ];
    }

    /**
     * The abilities that a "redemption" app API token should have.
     *
     * @return array
     */
    public static function redemptionAppTokenAbilities(): array
    {
        return [
            self::VOUCHER_REDEMPTIONS_CREATE->value => self::abilityLabels()[self::VOUCHER_REDEMPTIONS_CREATE->value],
            self::VOUCHER_REDEMPTIONS_READ->value   => self::abilityLabels()[self::VOUCHER_REDEMPTIONS_READ->value],
        ];
    }

    public static function groupsAbilityCasesWithDefinitions(): array
    {
        return [
            [
                'name'        => 'Super admin abilities',
                'description' => 'A group of API abilities that allow an app to perform any / all actions on the API. Be careful assigning this ability!',
                'abilities'   => [
                    self::SUPER_ADMIN->value => self::abilityLabels()[self::SUPER_ADMIN->value],
                ],
            ],
            [
                'name'        => 'Platform App',
                'description' => 'Perform administrative tasks for your OFN platform implementation.',
                'abilities'   => self::platformAppTokenAbilities(),
            ],
            [
                'name'        => 'Redemptions',
                'description' => 'A group of API abilities that allow an app to perform redemptions on the system.',
                'abilities'   => self::redemptionAppTokenAbilities(),
            ],
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
