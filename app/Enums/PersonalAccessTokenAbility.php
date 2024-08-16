<?php

namespace App\Enums;

/**
 * Configure Personal Access Token Abilities here.
 */
enum PersonalAccessTokenAbility: string
{

    case SUPER_ADMIN                = 'super-admin'; // Allowed to do everything
    case MY_PROFILE_CREATE          = 'my-profile-create';
    case MY_PROFILE_READ            = 'my-profile-read';
    case MY_PROFILE_UPDATE          = 'my-profile-update';
    case MY_PROFILE_DELETE          = 'my-profile-delete';
    case MY_TEAM_CREATE             = 'my-team-create';
    case MY_TEAM_READ               = 'my-team-read';
    case MY_TEAM_UPDATE             = 'my-team-update';
    case MY_TEAM_DELETE             = 'my-team-delete';
    case MY_TEAM_AUDIT_ITEMS_CREATE = 'my-team-audit-items-create';
    case MY_TEAM_AUDIT_ITEMS_READ   = 'my-team-audit-items-read';
    case MY_TEAM_AUDIT_ITEMS_UPDATE = 'my-team-audit-items-update';
    case MY_TEAM_AUDIT_ITEMS_DELETE = 'my-team-audit-items-delete';
    case MY_TEAM_VOUCHERS_CREATE    = 'my-team-vouchers-create';
    case MY_TEAM_VOUCHERS_READ      = 'my-team-vouchers-read';
    case MY_TEAM_VOUCHERS_UPDATE    = 'my-team-vouchers-update';
    case MY_TEAM_VOUCHERS_DELETE    = 'my-team-vouchers-delete';
    case SYSTEM_STATISTICS_CREATE   = 'system-statistics-create';
    case SYSTEM_STATISTICS_READ     = 'system-statistics-read';
    case SYSTEM_STATISTICS_UPDATE   = 'system-statistics-update';
    case SYSTEM_STATISTICS_DELETE   = 'system-statistics-delete';

}
