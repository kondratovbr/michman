<div
    x-data="{
        stuff: @entangle('state.stuff'),
        moreStuff: @entangle('state.moreStuff'),
        @alpine($foobars),
    }"
>

    <input type="text" x-model.debounce="stuff">
    <button wire:click="doABarrelRoll">Press me!</button>
    <p>Alpine: <span x-text="stuff"></span></p>
    <p>Livewire: {{ $state['stuff'] }}</p>

    <input type="text" x-model.debounce="moreStuff">
    <button wire:click="doABarrelRoll">Press me!</button>
    <p>Alpine: <span x-text="moreStuff"></span></p>
    <p>Livewire: {{ $state['moreStuff'] }}</p>

</div>
