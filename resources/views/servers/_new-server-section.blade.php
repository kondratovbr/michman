@if($user->hasActiveProvider())

    @can('create', App\Models\Server::class)
        <livewire:servers.create-server-form/>
    @else
        <x-servers.upgrade-subscription/>
    @endcan

@else

    <x-servers.add-provider/>

@endif
