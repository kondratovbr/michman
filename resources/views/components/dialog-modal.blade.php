@props(['id' => null, 'maxWidth' => null])

<x-modal
    {{ $attributes }}
    :id="$id"
    :maxWidth="$maxWidth"
>
    <div class="px-6 py-4">
        <div class="text-lg">
            {{ $title }}
        </div>

        <div class="mt-4">
            {{ $content }}
        </div>
    </div>

    <div class="px-6 py-4 bg-navy-200 text-right">
        {{ $footer }}
    </div>
</x-modal>
