<x-layouts.debug>

    <div class="container mx-auto">
        <div class="w-full bg-white shadow-md rounded my-6 overflow-x-auto text-gray-900">
            <table class="table-auto text-left w-full border-collapse">
                <thead>
                    <tr>
                        <x-th>Name</x-th>
                        <x-th>Domain</x-th>
                        <x-th>Methods</x-th>
                        <x-th>URI</x-th>
                        <x-th>Action</x-th>
                        <x-th>Middleware</x-th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($routes as $route)
                        <tr class="hover:bg-gray-100">
                            <x-td>{{ $route->getName() }}</x-td>
                            <x-td>{{ $route->domain() }}</x-td>
                            <x-td>{{ implode(', ', $route->methods()) }}</x-td>
                            <x-td>{{ $route->uri() }}</x-td>
                            <x-td>{{ $route->getAction()['controller'] ?? 'Closure' }}</x-td>
                            <x-td>
                                @foreach($route->middleware() as $middleware)
                                    {{ $middleware }}
                                    @if(! $loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </x-td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.debug>
