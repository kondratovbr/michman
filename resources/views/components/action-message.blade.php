@props(['on'])

<div
    {{ $attributes->merge([
        'class' => 'text-sm',
    ]) }}
    x-data="{ shown: false, timeout: null }"
    {{-- Alpine is going to catch a Livewire event declared as $on parameter,
     set "shown" to true and start a timeout to get it back to false. --}}
    x-init="@this.on('{{ $on }}', () => {
        clearTimeout(timeout);
        shown = true;
        timeout = setTimeout(() => { shown = false }, 2000);
    })"
    x-show.transition.opacity.out.duration.1500ms="shown"
    style="display: none;"
>
    {{ $slot->isEmpty() ? __('misc.saved') : $slot }}
</div>
