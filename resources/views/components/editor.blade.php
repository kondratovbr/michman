@props(['mode'])

<div
    x-data="{
        content: @entangle($attributes->wire('model')).defer,
        silent: false,
    }"
    x-init="$nextTick(() => {

        let editor = ace.edit($refs.editor);

        editor.setTheme('ace/theme/monokai');
        @isset($mode)
            editor.session.setMode('ace/mode/{{ $mode }}');
        @endisset

        editor.on('change', function () {
            silent = true;
            content = editor.getValue();
        });

        $watch('content', function (value) {
            if (silent) {
                silent = false;
            } else {
                editor.setValue(value);
            }
        })

    })"
    wire:ignore
>

    <pre {{ $attributes->class([
        'w-full',
    ])->merge([
        'id' => $attributes->wire('model'),
        'style' => 'height: 400px',
    ])->except('wire:model') }}
        x-ref="editor"
    >{{ $slot }}</pre>

</div>
