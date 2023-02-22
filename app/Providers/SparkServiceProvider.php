<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Spark\Plan;
use Spark\Spark;

class SparkServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Spark::billable(User::class)->resolve(function (Request $request) {
            return $request->user();
        });

        Spark::billable(User::class)->authorize(function (User $billable, Request $request) {
            return $request->user() &&
                   $request->user()->id == $billable->id;
        });

        Spark::billable(User::class)->checkPlanEligibility(function (User $billable, Plan $newPlan) {
            $unlimitedProjects = $newPlan->options['unlimited_projects'] ?? false;
            $unlimitedServers = $newPlan->options['unlimited_servers'] ?? false;

            if ($unlimitedServers && $unlimitedProjects) {
                return true;
            }

            if ( ! $unlimitedProjects && $billable->projects()->count() > $newPlan->options['projects']) {
                throw ValidationException::withMessages([
                    'plan' => __('billing.too-many-projects'),
                ]);
            }

            if ( ! $unlimitedServers && $billable->servers()->count() > $newPlan->options['servers']) {
                throw ValidationException::withMessages([
                    'plan' => __('billing.too-many-servers'),
                ]);
            }

            return true;
        });
    }
}
