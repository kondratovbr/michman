{{--TODO: IMPORTANT! How I use sections right now this part ends up at the bottom of the section on mobile. Should fix somehow. --}}

<div {{ $attributes }}>
    <div class="px-4 sm:px-0">
        <h3 class="text-lg font-medium text-gray-100">{{ $title }}</h3>

        <p class="mt-1 text-sm text-gray-300 max-w-prose">
            {{ $description }}
        </p>
    </div>
</div>
