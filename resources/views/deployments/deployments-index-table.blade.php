<x-table-section>

    <x-slot name="title">Deployments</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th></x-th>
            <x-th></x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($deployments as $deployment)
            <x-tr>
                <x-td></x-td>
                <x-td></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($deployments->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">The project hasn't been deployed yet.</p>
        </x-slot>
    @endif

</x-table-section>
