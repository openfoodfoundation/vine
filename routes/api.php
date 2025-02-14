<?php

use App\Enums\PersonalAccessTokenAbility;
use App\Http\Controllers\Api\V1\Admin\ApiAdminAuditItemsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminFileUploadsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminSearchController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminSystemStatisticsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamMerchantTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamServiceTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamUsersController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminTeamVoucherTemplatesController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminUserPersonalAccessTokensController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminUsersController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminVoucherRedemptionsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminVouchersController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminVoucherSetMerchantTeamsController;
use App\Http\Controllers\Api\V1\Admin\ApiAdminVoucherSetsController;
use App\Http\Controllers\Api\V1\ApiCountriesController;
use App\Http\Controllers\Api\V1\ApiMyProfileController;
use App\Http\Controllers\Api\V1\ApiMyTeamAuditItemsController;
use App\Http\Controllers\Api\V1\ApiMyTeamController;
use App\Http\Controllers\Api\V1\ApiMyTeamsController;
use App\Http\Controllers\Api\V1\ApiMyTeamSearchController;
use App\Http\Controllers\Api\V1\ApiMyTeamVouchersController;
use App\Http\Controllers\Api\V1\ApiMyTeamVoucherSetsAllocatedController;
use App\Http\Controllers\Api\V1\ApiMyTeamVoucherSetsController;
use App\Http\Controllers\Api\V1\ApiMyTeamVoucherSetsCreatedController;
use App\Http\Controllers\Api\V1\ApiMyTeamVSMTARController;
use App\Http\Controllers\Api\V1\ApiShopsController;
use App\Http\Controllers\Api\V1\ApiSystemStatisticsController;
use App\Http\Controllers\Api\V1\ApiVoucherBeneficiaryDistributionController;
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
             * Countries
             */
            Route::post('/countries', [ApiCountriesController::class, 'store'])
                ->name('api.v1.countries.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::COUNTRIES_CREATE->value,
                    ]
                );
            Route::get('/countries', [ApiCountriesController::class, 'index'])
                ->name('api.v1.countries.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::COUNTRIES_READ->value,
                    ]
                );

            Route::get('/countries/{id}', [ApiCountriesController::class, 'show'])
                ->name('api.v1.countries.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::COUNTRIES_READ->value,
                    ]
                );

            Route::put('/countries/{id}', [ApiCountriesController::class, 'update'])
                ->name('api.v1.countries.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::COUNTRIES_UPDATE->value,
                    ]
                );

            Route::delete('/countries/{id}', [ApiCountriesController::class, 'destroy'])
                ->name('api.v1.countries.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::COUNTRIES_DELETE->value,
                    ]
                );

            /**
             * My Team
             */
            Route::get('/my-team', [ApiMyTeamController::class, 'index'])
                ->name('api.v1.my-team.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_READ->value,
                    ]
                );

            Route::put('/my-team/{id}', [ApiMyTeamController::class, 'update'])
                ->name('api.v1.my-team.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_UPDATE->value,
                    ]
                );

            Route::post('/my-team', [ApiMyTeamController::class, 'store'])
                ->name('api.v1.my-team.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_CREATE->value,
                    ]
                );

            Route::get('/my-team/{id}', [ApiMyTeamController::class, 'show'])
                ->name('api.v1.my-team.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_READ->value,
                    ]
                );

            Route::delete('/my-team/{id}', [ApiMyTeamController::class, 'destroy'])
                ->name('api.v1.my-team.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_DELETE->value,
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

            Route::put('/my-team-audit-items/{id}', [ApiMyTeamAuditItemsController::class, 'update'])
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
             * My Profile
             */
            Route::post('/my-profile', [ApiMyProfileController::class, 'store'])
                ->name('api.v1.my-profile.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_PROFILE_CREATE->value,
                    ]
                );
            Route::get('/my-profile', [ApiMyProfileController::class, 'index'])
                ->name('api.v1.my-profile.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_PROFILE_READ->value,
                    ]
                );

            Route::get('/my-profile/{id}', [ApiMyProfileController::class, 'show'])
                ->name('api.v1.my-profile.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_PROFILE_READ->value,
                    ]
                );

            Route::put('/my-profile/{id}', [ApiMyProfileController::class, 'update'])
                ->name('api.v1.my-profile.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_PROFILE_UPDATE->value,
                    ]
                );

            Route::delete('/my-profile/{id}', [ApiMyProfileController::class, 'destroy'])
                ->name('api.v1.my-profile.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_PROFILE_DELETE->value,
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
             * My Search
             */
            Route::post('/my-team-search', [ApiMyTeamSearchController::class, 'store'])
                ->name('api.v1.my-team-search.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_SEARCH_CREATE->value,
                    ]
                );

            Route::get('/my-team-search', [ApiMyTeamSearchController::class, 'index'])
                ->name('api.v1.my-team-search.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_SEARCH_READ->value,
                    ]
                );

            Route::get('/my-team-search/{id}', [ApiMyTeamSearchController::class, 'show'])
                ->name('api.v1.my-team-search.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_SEARCH_READ->value,
                    ]
                );

            Route::put('/my-team-search/{id}', [ApiMyTeamSearchController::class, 'update'])
                ->name('api.v1.my-team-search.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_SEARCH_UPDATE->value,
                    ]
                );

            Route::delete('/my-team-search/{id}', [ApiMyTeamSearchController::class, 'destroy'])
                ->name('api.v1.my-team-search.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_SEARCH_DELETE->value,
                    ]
                );

            /**
             * My Voucher Sets
             */
            Route::post('/my-team-voucher-sets', [ApiMyTeamVoucherSetsController::class, 'store'])
                ->name('api.v1.my-team-voucher-sets.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_CREATE->value,
                    ]
                );

            Route::get('/my-team-voucher-sets', [ApiMyTeamVoucherSetsController::class, 'index'])
                ->name('api.v1.my-team-voucher-sets.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
                    ]
                );

            Route::get('/my-team-voucher-sets/{id}', [ApiMyTeamVoucherSetsController::class, 'show'])
                ->name('api.v1.my-team-voucher-sets.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
                    ]
                );

            Route::put('/my-team-voucher-sets/{id}', [ApiMyTeamVoucherSetsController::class, 'update'])
                ->name('api.v1.my-team-voucher-sets.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_UPDATE->value,
                    ]
                );

            Route::delete('/my-team-voucher-sets/{id}', [ApiMyTeamVoucherSetsController::class, 'destroy'])
                ->name('api.v1.my-team-voucher-sets.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_DELETE->value,
                    ]
                );

            /**
             * My Team Vouchers (created by or allocated to my team)
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
             * My Team Voucher Sets (created by my team)
             */
            Route::post('/my-team-voucher-sets-created', [ApiMyTeamVoucherSetsCreatedController::class, 'store'])
                ->name('api.v1.my-team-voucher-sets-created.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_CREATE->value,
                    ]
                );

            Route::get('/my-team-voucher-sets-created', [ApiMyTeamVoucherSetsCreatedController::class, 'index'])
                ->name('api.v1.my-team-voucher-sets-created.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
                    ]
                );

            Route::get('/my-team-voucher-sets-created/{id}', [ApiMyTeamVoucherSetsCreatedController::class, 'show'])
                ->name('api.v1.my-team-voucher-sets-created.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
                    ]
                );

            Route::put('/my-team-voucher-sets-created/{id}', [ApiMyTeamVoucherSetsCreatedController::class, 'update'])
                ->name('api.v1.my-team-voucher-sets-created.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_UPDATE->value,
                    ]
                );

            Route::delete('/my-team-voucher-sets-created/{id}', [ApiMyTeamVoucherSetsCreatedController::class, 'destroy'])
                ->name('api.v1.my-team-voucher-sets-created.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_DELETE->value,
                    ]
                );

            /**
             * My Team Voucher Sets (allocated to my team)
             */
            Route::post('/my-team-voucher-sets-allocated', [ApiMyTeamVoucherSetsAllocatedController::class, 'store'])
                ->name('api.v1.my-team-voucher-sets-allocated.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_CREATE->value,
                    ]
                );

            Route::get('/my-team-voucher-sets-allocated', [ApiMyTeamVoucherSetsAllocatedController::class, 'index'])
                ->name('api.v1.my-team-voucher-sets-allocated.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
                    ]
                );

            Route::get('/my-team-voucher-sets-allocated/{id}', [ApiMyTeamVoucherSetsAllocatedController::class, 'show'])
                ->name('api.v1.my-team-voucher-sets-allocated.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
                    ]
                );

            Route::put('/my-team-voucher-sets-allocated/{id}', [ApiMyTeamVoucherSetsAllocatedController::class, 'update'])
                ->name('api.v1.my-team-voucher-sets-allocated.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_UPDATE->value,
                    ]
                );

            Route::delete('/my-team-voucher-sets-allocated/{id}', [ApiMyTeamVoucherSetsAllocatedController::class, 'destroy'])
                ->name('api.v1.my-team-voucher-sets-allocated.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_DELETE->value,
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
             * Voucher Beneficiary Distributions
             */
            Route::post('/voucher-beneficiary-distributions', [ApiVoucherBeneficiaryDistributionController::class, 'store'])
                ->name('api.v1.voucher-beneficiary-distributions.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_CREATE->value,
                    ]
                );

            Route::get('/voucher-beneficiary-distributions', [ApiVoucherBeneficiaryDistributionController::class, 'index'])
                ->name('api.v1.voucher-beneficiary-distributions.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_READ->value,
                    ]
                );

            Route::get('/voucher-beneficiary-distributions/{id}', [ApiVoucherBeneficiaryDistributionController::class, 'show'])
                ->name('api.v1.voucher-beneficiary-distributions.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_READ->value,
                    ]
                );

            Route::put('/voucher-beneficiary-distributions/{id}', [ApiVoucherBeneficiaryDistributionController::class, 'update'])
                ->name('api.v1.voucher-beneficiary-distributions.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_UPDATE->value,
                    ]
                );

            Route::delete('/voucher-beneficiary-distributions/{id}', [ApiVoucherBeneficiaryDistributionController::class, 'destroy'])
                ->name('api.v1.voucher-beneficiary-distributions.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_DELETE->value,
                    ]
                );

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
             * Voucher set merchant team approval request
             */
            Route::get('/my-team-vsmtar', [ApiMyTeamVSMTARController::class, 'index'])
                ->name('api.v1.my-team-vsmtar.getMany')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_READ->value,
                    ]
                );

            Route::post('/my-team-vsmtar', [ApiMyTeamVSMTARController::class, 'store'])
                ->name('api.v1.my-team-vsmtar.post')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_CREATE->value,
                    ]
                );

            Route::get('/my-team-vsmtar/{id}', [ApiMyTeamVSMTARController::class, 'show'])
                ->name('api.v1.my-team-vsmtar.get')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_READ->value,
                    ]
                );

            Route::put('/my-team-vsmtar/{id}', [ApiMyTeamVSMTARController::class, 'update'])
                ->name('api.v1.my-team-vsmtar.put')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_UPDATE->value,
                    ]
                );

            Route::delete('/my-team-vsmtar/{id}', [ApiMyTeamVSMTARController::class, 'destroy'])
                ->name('api.v1.my-team-vsmtar.delete')
                ->middleware(
                    [
                        'abilities:' .
                        PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
                        PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_DELETE->value,
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
                        ApiAdminFileUploadsController::class
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
                        '/voucher-redemptions',
                        ApiAdminVoucherRedemptionsController::class
                    )->names('api.v1.admin.voucher-redemptions');

                    Route::resource(
                        '/team-voucher-templates',
                        ApiAdminTeamVoucherTemplatesController::class
                    )->names('api.v1.admin.team-voucher-templates');

                    /**
                     * User
                     */
                    Route::post('/users', [ApiAdminUsersController::class, 'store'])
                        ->name('api.v1.admin.users.post');

                    Route::get('/users', [ApiAdminUsersController::class, 'index'])
                        ->name('api.v1.admin.users.getMany');

                    Route::get('/users/{id}', [ApiAdminUsersController::class, 'show'])
                        ->name('api.v1.admin.users.get');

                    Route::put('/users/{id}', [ApiAdminUsersController::class, 'update'])
                        ->name('api.v1.admin.users.put');

                    Route::delete('/users/{id}', [ApiAdminUsersController::class, 'destroy'])
                        ->name('api.v1.admin.users.delete');

                    /**
                     * Vouchers
                     */
                    Route::post('/vouchers', [ApiAdminVouchersController::class, 'store'])
                        ->name('api.v1.admin-vouchers.post');

                    Route::get('/vouchers', [ApiAdminVouchersController::class, 'index'])
                        ->name('api.v1.admin-vouchers.getMany');

                    Route::get('/vouchers/{id}', [ApiAdminVouchersController::class, 'show'])
                        ->name('api.v1.admin-vouchers.get');

                    Route::put('/vouchers/{id}', [ApiAdminVouchersController::class, 'update'])
                        ->name('api.v1.admin-vouchers.put');

                    Route::delete('/vouchers/{id}', [ApiAdminVouchersController::class, 'destroy'])
                        ->name('api.v1.admin-vouchers.delete');

                    /**
                     * Voucher Sets
                     */
                    Route::post('/voucher-sets', [ApiAdminVoucherSetsController::class, 'store'])
                        ->name('api.v1.admin-voucher-sets.post');

                    Route::get('/voucher-sets', [ApiAdminVoucherSetsController::class, 'index'])
                        ->name('api.v1.admin-voucher-sets.getMany');

                    Route::get('/voucher-sets/{id}', [ApiAdminVoucherSetsController::class, 'show'])
                        ->name('api.v1.admin-voucher-sets.get');

                    Route::put('/voucher-sets/{id}', [ApiAdminVoucherSetsController::class, 'update'])
                        ->name('api.v1.admin-voucher-sets.put');

                    Route::delete('/voucher-sets/{id}', [ApiAdminVoucherSetsController::class, 'destroy'])
                        ->name('api.v1.admin-voucher-sets.delete');

                    Route::resource(
                        '/user-personal-access-tokens',
                        ApiAdminUserPersonalAccessTokensController::class
                    )->names('api.v1.admin.tokens');

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
                        ApiAdminFileUploadsController::class
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
                        '/voucher-redemptions',
                        ApiAdminVoucherRedemptionsController::class
                    )->names('api.v1.admin.voucher-redemptions');

                    Route::resource(
                        '/team-voucher-templates',
                        ApiAdminTeamVoucherTemplatesController::class
                    )->names('api.v1.admin.team-voucher-templates');

                    /**
                     * Vouchers
                     */
                    Route::post('/vouchers', [ApiAdminVouchersController::class, 'store'])
                        ->name('api.v1.admin-vouchers.post');

                    Route::get('/vouchers', [ApiAdminVouchersController::class, 'index'])
                        ->name('api.v1.admin-vouchers.getMany');

                    Route::get('/vouchers/{id}', [ApiAdminVouchersController::class, 'show'])
                        ->name('api.v1.admin-vouchers.get');

                    Route::put('/vouchers/{id}', [ApiAdminVouchersController::class, 'update'])
                        ->name('api.v1.admin-vouchers.put');

                    Route::delete('/vouchers/{id}', [ApiAdminVouchersController::class, 'destroy'])
                        ->name('api.v1.admin-vouchers.delete');

                    /**
                     * Voucher Sets
                     */
                    Route::post('/voucher-sets', [ApiAdminVoucherSetsController::class, 'store'])
                        ->name('api.v1.admin-voucher-sets.post');

                    Route::get('/voucher-sets', [ApiAdminVoucherSetsController::class, 'index'])
                        ->name('api.v1.admin-voucher-sets.getMany');

                    Route::get('/voucher-sets/{id}', [ApiAdminVoucherSetsController::class, 'show'])
                        ->name('api.v1.admin-voucher-sets.get');

                    Route::put('/voucher-sets/{id}', [ApiAdminVoucherSetsController::class, 'update'])
                        ->name('api.v1.admin-voucher-sets.put');

                    Route::delete('/voucher-sets/{id}', [ApiAdminVoucherSetsController::class, 'destroy'])
                        ->name('api.v1.admin-voucher-sets.delete');

                    /**
                     * Voucher Set Merchant Teams
                     */
                    Route::post('/voucher-set-merchant-teams', [ApiAdminVoucherSetMerchantTeamsController::class, 'store'])
                        ->name('api.v1.admin-voucher-set-mt.post');

                    Route::get('/voucher-set-merchant-teams', [ApiAdminVoucherSetMerchantTeamsController::class, 'index'])
                        ->name('api.v1.admin-voucher-set-mt.getMany');

                    Route::get('/voucher-set-merchant-teams/{id}', [ApiAdminVoucherSetMerchantTeamsController::class, 'show'])
                        ->name('api.v1.admin-voucher-set-mt.get');

                    Route::put('/voucher-set-merchant-teams/{id}', [ApiAdminVoucherSetMerchantTeamsController::class, 'update'])
                        ->name('api.v1.admin-voucher-set-mt.put');

                    Route::delete('/voucher-set-merchant-teams/{id}', [ApiAdminVoucherSetMerchantTeamsController::class, 'destroy'])
                        ->name('api.v1.admin-voucher-set-mt.delete');

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
