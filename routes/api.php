<?php

use App\Enums\PersonalAccessTokenAbility;
use App\Http\Controllers\Api\V1\Admin\ApiAdminAuditItemsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminSearchController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminSystemStatisticsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamMerchantTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamServiceTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamUsersController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamVoucherTemplatesController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminUserPersonalAccessTokensController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminUsersController;
use App\Http\Controllers\Api\V1\Admin\ApiFileUploadsController;
use App\Http\Controllers\Api\V1\ApiMyTeamAuditItemsController;
use App\Http\Controllers\Api\V1\ApiMyTeamController;
use App\Http\Controllers\Api\V1\ApiMyTeamsController;
use App\Http\Controllers\Api\V1\ApiMyTeamVouchersController;
use App\Http\Controllers\Api\V1\ApiShopsController;
use App\Http\Controllers\Api\V1\ApiSystemStatisticsController;
use App\Http\Controllers\Api\V1\ApiVoucherRedemptionsController;
use App\Http\Controllers\Api\V1\ApiVoucherValidationController;
use App\Http\Middleware\CheckAdminStatus;
use App\Http\Middleware\VerifyApiTokenSignature;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => VerifyApiTokenSignature::class], function () {

    /**
     * Open routes (non-authenticated)
     */

    /**
     * App API Routes
     */
    Route::middleware(['auth:sanctum'])
        ->group(function () {

            /**
             * My Team
             */
            Route::get('/my-team', [ApiMyTeamController::class, 'index'])
                ->name('api.v1.my-team.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_READ->value,
                    ]
                );

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
             * My Teams
             */
            Route::post('/my-teams', [ApiMyTeamsController::class, 'store'])
                ->name('api.v1.my-teams.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_CREATE->value,
                    ]
                );
            Route::get('/my-teams', [ApiMyTeamsController::class, 'index'])
                ->name('api.v1.my-teams.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_READ->value,
                    ]
                );

            Route::get('/my-teams/{id}', [ApiMyTeamsController::class, 'show'])
                ->name('api.v1.my-teams.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_READ->value,
                    ]
                );

            Route::put('/my-teams/{id}', [ApiMyTeamsController::class, 'update'])
                ->name('api.v1.my-teams.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_UPDATE->value,
                    ]
                );

            Route::delete('/my-teams/{id}', [ApiMyTeamsController::class, 'destroy'])
                ->name('api.v1.my-teams.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_DELETE->value,
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
             * Voucher Validation (for unattended redemptions)
             */
            Route::middleware('throttle:validations')->group(function () {

                Route::post('/voucher-validation', [ApiVoucherValidationController::class, 'store'])
                    ->name('api.v1.voucher-validation.post');

                Route::get('/voucher-validation', [ApiVoucherValidationController::class, 'index'])
                    ->name('api.v1.voucher-validation.getMany');

                Route::get('/voucher-validation/{id}', [ApiVoucherValidationController::class, 'show'])
                    ->name('api.v1.voucher-validation.get');

                Route::put('/voucher-validation/{id}', [ApiVoucherValidationController::class, 'update'])
                    ->name('api.v1.voucher-validation.put');

                Route::delete('/voucher-validation/{id}', [ApiVoucherValidationController::class, 'destroy'])
                    ->name('api.v1.voucher-validation.delete');

            });

            /**
             * Voucher Redemptions
             */
            Route::post('/voucher-redemptions', [ApiVoucherRedemptionsController::class, 'store'])
                ->name('api.v1.voucher-redemptions.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_REDEMPTIONS_CREATE->value,
                    ]
                );

            Route::get('/voucher-redemptions', [ApiVoucherRedemptionsController::class, 'index'])
                ->name('api.v1.voucher-redemptions.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_REDEMPTIONS_READ->value,
                    ]
                );

            Route::get('/voucher-redemptions/{id}', [ApiVoucherRedemptionsController::class, 'show'])
                ->name('api.v1.voucher-redemptions.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_REDEMPTIONS_READ->value,
                    ]
                );

            Route::put('/voucher-redemptions/{id}', [ApiVoucherRedemptionsController::class, 'update'])
                ->name('api.v1.voucher-redemptions.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_REDEMPTIONS_UPDATE->value,
                    ]
                );

            Route::delete('/voucher-redemptions/{id}', [ApiVoucherRedemptionsController::class, 'destroy'])
                ->name('api.v1.voucher-redemptions.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_REDEMPTIONS_DELETE->value,
                    ]
                );

            /**
             * Shops
             */
            Route::post('/shops', [ApiShopsController::class, 'store'])
                ->name('api.v1.shops.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SHOPS_CREATE->value,
                    ]
                );

            Route::get('/shops', [ApiShopsController::class, 'index'])
                ->name('api.v1.shops.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SHOPS_READ->value,
                    ]
                );

            Route::get('/shops/{id}', [ApiShopsController::class, 'show'])
                ->name('api.v1.shops.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SHOPS_READ->value,
                    ]
                );

            Route::put('/shops/{id}', [ApiShopsController::class, 'update'])
                ->name('api.v1.shops.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SHOPS_UPDATE->value,
                    ]
                );

            Route::delete('/shops/{id}', [ApiShopsController::class, 'destroy'])
                ->name('api.v1.shops.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::SHOPS_DELETE->value,
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

            /**
             * Admin Api Routes
             */
            Route::prefix('admin')
                ->middleware(['auth:sanctum', CheckAdminStatus::class])
                ->group(function () {

                    Route::resource(
                        '/audit-items',
                        ApiAdminAuditItemsController::class
                    )->names('api.v1.admin.audit-items');

                    Route::resource(
                        '/search',
                        ApiAdminSearchController::class
                    )->names('api.v1.admin.search');

                    Route::resource(
                        '/system-statistics',
                        ApiAdminSystemStatisticsController::class
                    )->names('api.v1.admin.system-statistics');

                    Route::resource(
                        '/file-uploads',
                        ApiFileUploadsController::class
                    )->names('api.v1.admin.file-uploads');

                    Route::resource(
                        '/search',
                        ApiAdminSearchController::class
                    )->names('api.v1.admin.search');

                    Route::resource(
                        '/team-merchant-teams',
                        ApiAdminTeamMerchantTeamsController::class
                    )->names('api.v1.admin.team-merchant-teams');

                    Route::resource(
                        '/team-service-teams',
                        ApiAdminTeamServiceTeamsController::class
                    )->names('api.v1.admin.team-service-teams');

                    Route::resource(
                        '/team-users',
                        ApiAdminTeamUsersController::class
                    )->names('api.v1.admin.team-users');

                    Route::resource(
                        '/teams',
                        ApiAdminTeamsController::class
                    )->names('api.v1.admin.teams');

                    Route::resource(
                        '/user-personal-access-tokens',
                        ApiAdminUserPersonalAccessTokensController::class
                    )->names('api.v1.admin.tokens');

                    Route::resource(
                        '/users',
                        ApiAdminUsersController::class
                    )->names('api.v1.admin.users');

                    Route::resource(
                        '/team-voucher-templates',
                        ApiAdminTeamVoucherTemplatesController::class
                    )->names('api.v1.admin.team-voucher-templates');

                    Route::resource(
                        '/user-personal-access-tokens',
                        ApiAdminUserPersonalAccessTokensController::class
                    )->names('api.v1.admin.tokens');

                    Route::resource(
                        '/users',
                        ApiAdminUsersController::class
                    )->names('api.v1.admin.users');

                });
        });
});
