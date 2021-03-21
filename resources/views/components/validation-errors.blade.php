@if($errors->any())
    <div {{ $attributes->merge([
        'class' => 'py-5 px-6 bg-red-700 text-red-100 rounded-lg',
    ]) }}>
        {{-- If there's only one error - don't render it as a list item. --}}
        @if($errors->count() > 1)
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @else
            <p>{{ $errors->first() }}</p>
        @endif
    </div>
@endif
