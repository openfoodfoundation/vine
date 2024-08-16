<?php

use App\Enums\PersonalAccessTokenAbility;
use App\Http\Controllers\Api\V1\Admin\ApiAdminAuditItemsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminSearchController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminSystemStatisticsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamMerchantTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamServiceTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamUsersController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminUserPersonalAccessTokensController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminUsersController;
use App\Http\Controllers\Api\V1\ApiMyTeamAuditItemsController;
use App\Http\Controllers\Api\V1\ApiMyTeamController;
use App\Http\Controllers\Api\V1\ApiMyTeamVouchersController;
use App\Http\Controllers\Api\V1\ApiSystemStatisticsController;
use App\Http\Middleware\CheckAdminStatus;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    /**
     * App API Routes
     */
    Route::middleware(['auth:sanctum'])
        ->group(function () {

            Route::resource('/my-team', ApiMyTeamController::class)->names('api.v1.my-team');

            /**
             * My Audit Items
             */
            Route::post('/my-team-audit-items', [ApiMyTeamAuditItemsController::class, 'store'])
                ->name('api.v1.my-team-audit-items.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_CREATE->value,
                    ]
                );

            Route::get('/my-team-audit-items', [ApiMyTeamAuditItemsController::class, 'index'])
                ->name('api.v1.my-team-audit-items.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_READ->value,
                    ]
                );

            Route::get('/my-team-audit-items/{id}', [ApiMyTeamAuditItemsController::class, 'show'])
                ->name('api.v1.my-team-audit-items.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_READ->value,
                    ]
                );

            Route::put('/my-team-audit-items/{id}', [ApiMyTeamVouchersController::class, 'update'])
                ->name('api.v1.my-team-audit-items.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_UPDATE->value,
                    ]
                );

            Route::delete('/my-team-audit-items/{id}', [ApiMyTeamAuditItemsController::class, 'destroy'])
                ->name('api.v1.my-team-audit-items.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_DELETE->value,
                    ]
                );

            /**
             * My Vouchers
             */
            Route::post('/my-team-vouchers', [ApiMyTeamVouchersController::class, 'store'])
                ->name('api.v1.my-team-vouchers.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_CREATE->value,
                    ]
                );

            Route::get('/my-team-vouchers', [ApiMyTeamVouchersController::class, 'index'])
                ->name('api.v1.my-team-vouchers.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
                    ]
                );

            Route::get('/my-team-vouchers/{id}', [ApiMyTeamVouchersController::class, 'show'])
                ->name('api.v1.my-team-vouchers.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
                    ]
                );

            Route::put('/my-team-vouchers/{id}', [ApiMyTeamVouchersController::class, 'update'])
                ->name('api.v1.my-team-vouchers.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_UPDATE->value,
                    ]
                );

            Route::delete('/my-team-vouchers/{id}', [ApiMyTeamVouchersController::class, 'destroy'])
                ->name('api.v1.my-team-vouchers.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_DELETE->value,
                    ]
                );

            /**
             * System Statistics
             */
            Route::post('/system-statistics', [ApiSystemStatisticsController::class, 'store'])
                ->name('api.v1.system-statistics.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SYSTEM_STATISTICS_CREATE->value,
                    ]
                );

            Route::get('/system-statistics', [ApiSystemStatisticsController::class, 'index'])
                ->name('api.v1.system-statistics.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value,
                    ]
                );

            Route::get('/system-statistics/{id}', [ApiSystemStatisticsController::class, 'show'])
                ->name('api.v1.system-statistics.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value,
                    ]
                );

            Route::put('/system-statistics/{id}', [ApiSystemStatisticsController::class, 'update'])
                ->name('api.v1.system-statistics.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SYSTEM_STATISTICS_UPDATE->value,
                    ]
                );

            Route::delete('/system-statistics/{id}', [ApiSystemStatisticsController::class, 'destroy'])
                ->name('api.v1.system-statistics.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SYSTEM_STATISTICS_DELETE->value,
                    ]
                );

        });

    /**
     * Admin Api Routes
     */
    Route::prefix('admin')
        ->middleware(['auth:sanctum', CheckAdminStatus::class])
        ->group(function () {
            Route::resource('/audit-items', ApiAdminAuditItemsController::class)->names('api.v1.admin.audit-items');
            Route::resource('/search', ApiAdminSearchController::class)->names('api.v1.admin.search');
            Route::resource('/system-statistics', ApiAdminSystemStatisticsController::class)->names('api.v1.admin.system-statistics');
            Route::resource('/team-merchant-teams', ApiAdminTeamMerchantTeamsController::class)->names('api.v1.admin.team-merchant-teams');
            Route::resource('/team-service-teams', ApiAdminTeamServiceTeamsController::class)->names('api.v1.admin.team-service-teams');
            Route::resource('/team-users', ApiAdminTeamUsersController::class)->names('api.v1.admin.team-users');
            Route::resource('/teams', ApiAdminTeamsController::class)->names('api.v1.admin.teams');
            Route::resource('/user-personal-access-tokens', ApiAdminUserPersonalAccessTokensController::class)->names('api.v1.admin.tokens');
            Route::resource('/users', ApiAdminUsersController::class)->names('api.v1.admin.users');

        });

});
