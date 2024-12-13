<?php

namespace App\Http\Middleware;

use App\Enums\PersonalAccessTokenAbility;
use App\Enums\VoucherSetType;
use App\Models\Country;
use App\Models\Team;
use App\Models\TeamUser;
use Auth;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param Request $request
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $team = Team::find($request->user()?->current_team_id);

        return [
            ...parent::share($request),
            'auth' => [
                'user'           => $request->user(),
                'currentTeam'    => $team,
                'availableTeams' => TeamUser::with('team')->where('user_id', Auth::id())->get(),
                'teamCountry'    => Country::find($team?->country_id),
            ],
            'personalAccessTokenAbilities' => PersonalAccessTokenAbility::groupsAbilityCasesWithDefinitions(),
            'platformAppTokenAbilities'    => PersonalAccessTokenAbility::platformAppTokenAbilities(),
            'redemptionAppTokenAbilities'  => PersonalAccessTokenAbility::redemptionAppTokenAbilities(),
            'isImpersonating'              => session('vine:impersonator'),
            'voucherSetTypes'              => VoucherSetType::values(),
        ];
    }
}
