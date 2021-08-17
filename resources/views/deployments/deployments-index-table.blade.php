<x-table-section>

    <x-slot name="title">{{ __('deployments.table.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
            <x-th></x-th>
            <x-th>{{ __('deployments.table.started-at') }}</x-th>
            <x-th>{{ __('deployments.table.branch') }}</x-th>
            <x-th>{{ __('deployments.table.commit') }}</x-th>
            <x-th>{{ __('deployments.table.duration') }}</x-th>
            <x-th>{{ __('deployments.table.status') }}</x-th>
            <x-th></x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($deployments as $deployment)
            <x-tr>
                <x-td></x-td>
                <x-td>{{ $deployment->createdAt }}</x-td>
                <x-td><x-code>{{ $deployment->branch }}</x-code></x-td>
{{--                TODO: IMPORTANT! Don't forget to make this a link to the VCS page with this commit, like Forge does.--}}
                <x-td>{{ Str::substr($deployment->commit, 0, 8) }}</x-td>
                <x-td>{{ $deployment->finished ? $deployment->duration->forHumans() : '—' }}</x-td>
                <x-td><x-deployments.status-badge :deployment="$deployment" /></x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($deployments->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('deployments.table.empty') }}</p>
        </x-slot>
    @endif

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <div class="text-sm">Deploying the <x-code>{{ $project->branch }}</x-code> branch</div>
            <x-buttons.primary
                wire:click.prevent="deploy"
            >{{ __('deployments.deploy-button') }}</x-buttons.primary>
        </div>
    </x-slot>

</x-table-section>
