<x-inputs.hidden
    {{ $attributes }}
    name="_token"
    value="{{ csrf_token() }}"
/>
