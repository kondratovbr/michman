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
                <x-td>{{ $project->fullDomainName() }}</x-td>
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
