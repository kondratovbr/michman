


{{--NOTE: This page is not in use at this point. I have a separate marketing site where the legal stuff is hosted.--}}

<x-layouts.guest>
    <div class="pt-4 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <x-auth-logo/>

            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
                {!! $terms !!}
            </div>
        </div>
    </div>
</x-layouts.guest>
