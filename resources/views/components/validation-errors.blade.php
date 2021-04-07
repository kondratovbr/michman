@if($errors->any())
    <x-message
        {{ $attributes }}
        colors="danger"
    >
        {{-- If there's only one error - don't render it as a list item. --}}
        @if($errors->count() > 1)
            <ul class="list-disc list-outside ml-3 max-w-prose">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @else
            <p>{{ $errors->first() }}</p>
        @endif
    </x-message>
@endif
