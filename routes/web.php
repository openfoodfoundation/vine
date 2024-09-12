<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckAdminStatus;
use App\Models\Team;
use App\Models\TeamMerchantTeam;
use App\Models\TeamUser;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/api-documentation', function () {
    return View::make('scribe.index');
});

Route::get('/', function () {
    return Redirect::to('/dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/my-team', function () {
        return Inertia::render('App/MyTeam');
    })->name('my-team');

    Route::get('/my-voucher-sets', function () {
        return Inertia::render('App/MyVoucherSets');
    })->name('my-voucher-sets');

    Route::get('/my-vouchers', function () {
        return Inertia::render('App/MyVouchers');
    })->name('my-vouchers');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/audit-trail', function () {
        return Inertia::render('AuditItems');
    })->name('audit-trail');

    Route::get('/redeem/{voucherSetId}/{voucherId}', function ($voucherSetId, $voucherId) {

        $voucher = Voucher::with('voucherSet')->where('voucher_set_id', $voucherSetId)->find($voucherId);

        /**
         * Voucher doesn't exist
         */
        if (!$voucher) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherSetId' => $voucherSetId,
                'voucherId'    => $voucherId,
                'title'        => 'Voucher Redeem',
                'text'         => 'Voucher does not exist.',
            ]);
        }

        /**
         * User does not have team
         */
        $team = Team::find(Auth::user()->current_team_id);
        if (!$team) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherSetId' => $voucherSetId,
                'voucherId'    => $voucherId,
                'title'        => 'Voucher Redeem',
                'text'         => 'You have not set your team.',
            ]);
        }

        /**
         * User's team is NOT a merchant of the voucher's service team
         */
        $teamMerchant = TeamMerchantTeam::where('team_id', $voucher->allocated_to_service_team_id)
            ->where('merchant_team_id', $team->id)
            ->first();

        /**
         * User's team is NOT a merchant for the voucher set
         */
        $teamMerchantOfVoucherSet = VoucherSetMerchantTeam::where('voucher_set_id', $voucherSetId)
            ->where('merchant_team_id', $team->id)
            ->first();

        if (!$teamMerchant || !$teamMerchantOfVoucherSet) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherSetId' => $voucherSetId,
                'voucherId'    => $voucherId,
                'title'        => 'Voucher Redeem',
                'text'         => 'Your team is not allowed to redeem this voucher, sorry.',
            ]);
        }

        return Inertia::render('App/Vouchers/VoucherRedeem', [
            'voucherSetId' => $voucherSetId,
            'voucherId'    => $voucherId,
        ]);
    })->name('voucher-redeem');

    Route::get('/voucher/{voucherId}', function ($voucherId) {

        $voucher = Voucher::find($voucherId);

        /**
         * Voucher doesn't exist
         */
        if (!$voucher) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherId' => $voucherId,
                'title'     => 'Voucher',
                'text'      => 'Voucher does not exist.',
            ]);
        }

        /**
         * User does not have team
         */
        $team = Team::find(Auth::user()->current_team_id);
        if (!$team) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherId' => $voucherId,
                'title'     => 'Voucher',
                'text'      => 'You have not set your team',
            ]);
        }

        /**
         * User's team is NOT a merchant of the voucher's service team
         */
        $teamMerchant = TeamMerchantTeam::where('team_id', $voucher->allocated_to_service_team_id)
            ->where('merchant_team_id', $team->id)
            ->first();

        /**
         * User's team is NOT a merchant for the voucher set
         */
        $teamMerchantOfVoucherSet = VoucherSetMerchantTeam::where('voucher_set_id', $voucher->voucher_set_id)
            ->where('merchant_team_id', $team->id)
            ->first();

        if (($voucher->created_by_team_id != $team->id && $voucher->allocated_to_service_team_id != $team->id) && !$teamMerchant && !$teamMerchantOfVoucherSet) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherId' => $voucherId,
                'title'     => 'Voucher',
                'text'      => 'Your team is not allowed to see this voucher, sorry.',
            ]);
        }

        return Inertia::render('App/Vouchers/Voucher', [
            'voucherId' => $voucherId,
        ]);
    })->name('voucher');

    Route::get('/voucher-set/{voucherSetId}', function ($voucherSetId) {

        $voucherSet = VoucherSet::find($voucherSetId);

        /**
         * Voucher set doesn't exist
         */
        if (!$voucherSet) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherSetId' => $voucherSetId,
                'title'        => 'Voucher Set',
                'text'         => 'Voucher set does not exist.',
            ]);
        }

        /**
         * User does not have team
         */
        $team = Team::find(Auth::user()->current_team_id);
        if (!$team) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherSetId' => $voucherSetId,
                'title'        => 'Voucher Set',
                'text'         => 'You have not set your team',
            ]);
        }

        /**
         * User's team is NOT a merchant of the voucher set's service team
         */
        $teamMerchant = TeamMerchantTeam::where('team_id', $voucherSet->allocated_to_service_team_id)
            ->where('merchant_team_id', $team->id)
            ->first();

        /**
         * User's team is NOT a merchant for the voucher set
         */
        $teamMerchantOfVoucherSet = VoucherSetMerchantTeam::where('voucher_set_id', $voucherSetId)
            ->where('merchant_team_id', $team->id)
            ->first();

        if (($voucherSet->created_by_team_id != $team->id && $voucherSet->allocated_to_service_team_id != $team->id) && !$teamMerchant && !$teamMerchantOfVoucherSet) {
            return Inertia::render('App/ErrorMessagePage', [
                'voucherId' => $voucherSetId,
                'title'     => 'Voucher Set',
                'text'      => 'Your team is not allowed to see this voucher set, sorry.',
            ]);
        }

        return Inertia::render('App/Vouchers/VoucherSet', [
            'voucherSetId' => $voucherSetId,
        ]);
    })->name('voucher-set');

    Route::get('/stop-impersonating', function (Request $request) {
        // We will make sure we have an impersonator's user ID in the session and if the
        // value doesn't exist in the session we will log this user out of the system
        // since they aren't really impersonating anyone and manually hit this URL.
        if (!$request->session()->has('vine:impersonator')) {
            Auth::logout();

            return redirect('/');
        }

        $userId = $request->session()->pull(
            'vine:impersonator'
        );

        // After removing the impersonator user's ID from the session so we can retrieve
        // the original user. Then, we will flush the entire session to clear out any
        // stale data from while we were doing the impersonation of the other user.
        $request->session()->flush();

        Auth::login(User::findOrFail($userId));

        return Redirect::to('/admin');
    })->name('stop-impersonating');

    Route::get('/switch-team/{id}', function ($id) {

        $teamUserForThisTeam = TeamUser::where('user_id', Auth::id())
            ->where('team_id', $id)->first();

        if ($teamUserForThisTeam) {
            Auth::user()->current_team_id = $id;
            Auth::user()->save();

        }

        return Redirect::to('/my-team');

    })->name('switch-team');

    /**
     * Admin routes
     */
    Route::prefix('admin')->middleware([CheckAdminStatus::class])->group(function () {
        Route::get('/', function () {
            return Inertia::render('Admin/AdminHome');
        })->name('admin.home');

        Route::get('/audit-trail', function () {
            return Inertia::render('AuditItems');
        })->name('admin.audit-trail');

        Route::get('/api-access-tokens', function () {
            return Inertia::render('Admin/APIAccessTokens/APIAccessTokens');
        })->name('admin.api-access-tokens');

        Route::get('/api-access-token/{id}', function (int $id) {
            return Inertia::render('Admin/APIAccessTokens/APIAccessToken', [
                'id' => $id,
            ]);
        })->name('admin.api-access-token');

        Route::get('/impersonate/{userId}', function (Request $request, $userId) {
            $request->session()->flush();

            // We will store the original user's ID in the session so we can remember who we
            // actually are when we need to stop impersonating the other user, which will
            // allow us to pull the original user back out of the database when needed.
            $request->session()->put(
                'vine:impersonator',
                $request->user()->id
            );
            Auth::login(User::findOrFail($userId));

            return redirect('/');
        })->name('admin.impersonate');

        Route::get('/vouchers', function () {
            return Inertia::render('Admin/Vouchers/Vouchers');
        })->name('admin.vouchers');

        Route::get('/voucher/{id}', function (string $id) {
            return Inertia::render('Admin/Vouchers/Voucher', [
                'id' => $id,
            ]);
        })->name('admin.voucher');

        Route::get('/voucher-sets', function () {
            return Inertia::render('Admin/Vouchers/VoucherSets');
        })->name('admin.voucher-sets');

        Route::get('/voucher-set/{id}', function (string $id) {
            return Inertia::render('Admin/Vouchers/VoucherSet', [
                'id' => $id,
            ]);
        })->name('admin.voucher-set');

        Route::get('/voucher-redemptions', function () {
            return Inertia::render('Admin/Vouchers/VoucherRedemptions');
        })->name('admin.voucher-redemptions');

        Route::get('/teams', function () {
            return Inertia::render('Admin/Teams/Teams');
        })->name('admin.teams');

        Route::get('/teams/new', function () {
            return Inertia::render('Admin/Teams/TeamNew');
        })->name('admin.teams.new');

        Route::get('/team/{id}', function (int $id) {
            return Inertia::render('Admin/Teams/Team', [
                'id' => $id,
            ]);
        })->name('admin.team');

        Route::get('/team-voucher-templates/new', function () {
            return Inertia::render('Admin/VoucherTemplates/VoucherTemplateNew');
        })->name('admin.team-voucher-templates.new');

        Route::get('/team-voucher-template/{id}', function (int $id) {
            return Inertia::render('Admin/VoucherTemplates/VoucherTemplateEdit', [
                'id' => $id,
            ]);
        })->name('team-voucher-template');

        Route::get('/users', function () {
            return Inertia::render('Admin/Users/Users');
        })->name('admin.users');

        Route::get('/user/{id}', function (int $id) {
            return Inertia::render('Admin/Users/User', [
                'id' => $id,
            ]);
        })->name('admin.user');

    });

});

require __DIR__ . '/auth.php';
