<form
    {{ $attributes }}
    @if(in_array($method, ['POST', 'GET']))
        method="{{ $method }}"
    @elseif(! is_null($method))
        method="POST"
    @endif
    @if($withFiles)
        enctype="multipart/form-data"
    @endif
>
    @if(! in_array($method, ['POST', 'GET', null]))
        <x-inputs.method :method="$method" />
    @endif
    <x-inputs.csrf/>
    {{ $slot }}
</form>
