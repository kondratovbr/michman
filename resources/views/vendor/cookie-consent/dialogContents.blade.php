{{--TODO: CRITICAL! Don't forget to adapt this to mobile - my bottom menu hides the thing. Make sure it works on tablets too. And just test it on touch and on my devices with Safari and adblocks - just to make sure cookies work as intended.--}}

<div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 pb-2">
    <div class="max-w-2xl mx-auto px-6">
        <div class="p-2 rounded-lg bg-navy-500">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 items-center hidden md:inline">
                    <p class="ml-3 cookie-consent__message">
                        {!! trans('cookie-consent::texts.message') !!}
                    </p>
                </div>
                <div class="mt-2 flex-shrink-0 w-full sm:mt-0 sm:w-auto">
                    <a class="js-cookie-consent-agree cookie-consent__agree cursor-pointer flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium bg-gold-800 hover:bg-gold-700 text-gray-900">
                        {{ trans('cookie-consent::texts.agree') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
