<x-table-section>

    <x-slot name="title">Deployments</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th></x-th>
            <x-th>Started at</x-th>
            <x-th>Branch</x-th>
            <x-th>Commit</x-th>
            <x-th>Duration</x-th>
            <x-th>Status</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($deployments as $deployment)
            <x-tr>
                <x-td></x-td>
                <x-td>{{ $deployment->createdAt }}</x-td>
                <x-td><x-code>{{ $deployment->branch }}</x-code></x-td>
                <x-td>{{ $deployment->commit }}</x-td>
                <x-td>{{ is_null($deployment->completedAt) ? '—' : $deployment->completedAt->sub($deployment->createdAt) }}</x-td>
                <x-td>Status</x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($deployments->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">The project hasn't been deployed yet.</p>
        </x-slot>
    @endif

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <div class="text-sm">Deploying the <x-code>{{ $project->branch }}</x-code> branch</div>
            <x-buttons.primary
                wire:click.prevent="deploy"
            >Deploy Now</x-buttons.primary>
        </div>
    </x-slot>

</x-table-section>
