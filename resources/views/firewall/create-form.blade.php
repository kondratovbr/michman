<x-form-section submit="store">

    <x-slot name="title">{{ __('servers.firewall.form.title') }}</x-slot>

    <x-slot name="form">

        <x-message colors="info">
            <div class="max-w-prose space-y-2">
                <p>
                    By default the firewall on your server is configured to
                    <strong>deny</strong> all incoming connections and
                    <strong>allow</strong> all outgoing connection.
                </p>
                <p>
                    Opening a port will <strong>allow</strong> all incoming connections to that port.
                </p>
            </div>
        </x-message>

    </x-slot>

    <x-slot name="actions">
        Actions!
    </x-slot>

</x-form-section>
