@if($errors->any())
    <div {{ $attributes->merge([
        'class' => 'py-5 px-6 bg-red-700 text-red-100 rounded-lg',
    ]) }}>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
