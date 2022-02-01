<a
    class="block min-h-10 p-2 text-center font-bold bg-yellow-300 text-yellow-900 hover:bg-yellow-400 transition-background ease-in-out duration-quick"
    href="/billing"
>{{ trans_choice('billing.trial-bar', user()->trialEndsAt()->diffAsCarbonInterval(now())->ceilDays()->totalDays) }}</a>
