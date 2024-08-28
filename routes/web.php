<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckAdminStatus;
use App\Models\TeamUser;
use App\Models\User;
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/audit-trail', function () {
        return Inertia::render('AuditItems');
    })->name('audit-trail');

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
