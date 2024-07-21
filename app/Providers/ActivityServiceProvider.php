<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class ActivityServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Activity::saving(function (Activity $activity) {
            $request = Request::instance();
            // Even validation and user attach action is done in middleware they will be executed before saving 
            // because savig acxtion is triggered after the validation and user attach action
            $user = Auth::user();
            if ($user) {
                $activity->causedBy($user);
            }

            if (isset($activity->properties['attributes']['password'])) {
                $activity->properties['attributes']['password'] = '***';
            }
            if (isset($activity->properties['old']['password'])) {
                $activity->properties['old']['password'] = '***';
            }
            // Add extra data to the activity log
            $activity->properties = $activity->properties->put('extra_data', [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
            //...
        });
    }
}
