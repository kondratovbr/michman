<x-table-section>

    <x-slot name="title">{{ __('projects.index.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
{{--            TODO: CRITICAL! Implement the SSL itself and don' forget this badge.--}}
            <x-th>SSL</x-th>
            <x-th>{{ __('projects.index.table.domain') }}</x-th>
            <x-th>{{ __('projects.index.table.repo') }}</x-th>
{{--            TODO: CRITICAL! CONTINUE. Don't forget to implement this as well.--}}
            <x-th>{{ __('projects.index.table.last-deployed') }}</x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($projects as $project)
            <x-tr>
                <x-td></x-td>
{{--                TODO: Should somehow handle long domain names here.--}}
                <x-td>
                    <div class="flex flex-col">
                        <x-app-link href="{{ route('projects.show', [$project, 'deployment']) }}">
                            {{ $project->fullDomainName }}
                        </x-app-link>
                        <p class="text-sm">{{ $project->shortInfo() }}</p>
                    </div>
                </x-td>
                <x-td>
                    <div class="flex flex-col">
{{--                        TODO: Add a small VCS provider logo icon here. Will look nicer.--}}
                        <x-app-link href="{{ $project->repoUrl }}" external :icon="false">{{ $project->repo }}</x-app-link>
                        <p class="text-sm">{{ $project->vcsProviderName }}</p>
                    </div>
                </x-td>
                <x-td></x-td>
            </x-tr>
        @endforeach
    </x-slot>

    @if($projects->isEmpty())
        <x-slot name="empty">
            <p class="max-w-prose">{{ __('projects.index.empty') }}</p>
        </x-slot>
    @endif

</x-table-section>
