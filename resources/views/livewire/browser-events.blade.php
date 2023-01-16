<div wire:init="triggerEvents">
    @pushonce('livewire')
        <script>

            Livewire.on('reddit-event', function (name, payload = null) {

                console.log('rdt', name, payload);

                try {

                    rdt('track', name, payload);

                } catch (e) {
                    console.log(e);
                }

            });

        </script>
    @endpushonce
</div>
