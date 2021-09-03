<x-table-section>

    <x-slot name="title">{{ __('projects.queue.index.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th></x-th>
            <x-th></x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($workers as $worker)
            <x-tr>
                <x-td>{{ $worker->server->name }}</x-td>
                <x-td></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($workers->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('projects.queue.index.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>
