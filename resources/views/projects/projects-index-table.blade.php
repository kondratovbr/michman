<x-table-section>

    <x-slot name="title">{{ __('projects.index.title') }}</x-slot>

    <x-slot name="header">
        <x-tr-header>
{{--            TODO: CRITICAL! Implement the SSL itself and don' forget this badge.--}}
            <x-th>SSL</x-th>
            <x-th>{{ __('projects.index.table.domain') }}</x-th>
{{--            TODO: CRITICAL! Don't forget to implement these as well.--}}
            <x-th>{{ __('projects.index.table.repo') }}</x-th>
            <x-th>{{ __('projects.index.table.last-deployed') }}</x-th>
        </x-tr-header>
    </x-slot>

    <x-slot name="body">
        @foreach($projects as $project)
            <x-tr>
                <x-td></x-td>
{{--                TODO: Should somehow handle long domain names here.--}}
                <x-td>
                    <div class="ml-2 flex flex-col">
                        <x-app-link href="{{ route('projects.show', [$project, 'deployment']) }}">
                            {{ $project->fullDomainName }}
                        </x-app-link>
{{--                        TODO: CRITICAL! Put here some additional info about the project the same way I do in servers index table.--}}
                        <p class="text-sm">Foobar</p>
                    </div>
                </x-td>
                <x-td></x-td>
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
