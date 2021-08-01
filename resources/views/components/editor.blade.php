@props(['name', 'mode'])

<div>

    <pre {{ $attributes->class([
        'w-full',
    ])->merge([
        'style' => 'height: 400px',
        'id' => $name,
        'wire:ignore',
    ]) }}>{{ $slot }}</pre>

    @push('ace-editor')
        <script>
            var editor = ace.edit('{{ $name }}');
            editor.setTheme("ace/theme/monokai");
            @isset($mode)
                editor.session.setMode('ace/mode/{{ $mode }}');
            @endisset
        </script>
    @endpush

</div>
