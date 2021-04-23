<div class="space-y-6">

{{--    @dump($providers, $foobar)--}}

    <p>Livewire provider_id: {{ $state['provider_id'] }}</p>

    <x-field wire:key="provider_id">
        <x-label>API Credentials</x-label>
        <x-select
            :options="$providers"
            name="provider_id"
            wire:model="state.provider_id"
            placeholder="Select API credentials"
        />
    </x-field>

{{--    <x-field wire:key="provider_id">--}}
{{--        <x-label id="provider_id-label">API Credentials</x-label>--}}
{{--        <x-select-proper--}}
{{--            :options="$providers"--}}
{{--            name="provider_id"--}}
{{--            wire:model="state.provider_id"--}}
{{--            wireOptionsChangedEvent="providers-changed"--}}
{{--            labelId="provider_id-label"--}}
{{--        />--}}
{{--    </x-field>--}}

{{--    <x-field wire:key="region">--}}
{{--        <x-label id="region-label">Region</x-label>--}}
{{--        <x-select-proper--}}
{{--            :options="$foobar"--}}
{{--            name="region"--}}
{{--            wire:model="state.region"--}}
{{--            wireOptionsChangedEvent="regions-changed"--}}
{{--            labelId="region-label"--}}
{{--        />--}}
{{--    </x-field>--}}

{{--    <x-field>--}}
{{--        <x-label>API Credentials</x-label>--}}
{{--        <x-select--}}
{{--            :data="$providers"--}}
{{--            data="{'2':'jacobs.com','5':'murphy.org','7':'beier.com','10':'kub.com','11':'weissnat.com','13':'Dev Token'}"--}}
{{--            name="provider_id"--}}
{{--            wireModel="state.provider_id"--}}
{{--        />--}}
{{--    </x-field>--}}

    {{-- TODO: Don't forget to add an explanation here. Not everyone knows where the name will be used and even WTF is it. --}}
{{--    <x-field>--}}
{{--        <x-label>Name</x-label>--}}
{{--        <x-inputs.text name="name" />--}}
{{--    </x-field>--}}

{{--    <x-field>--}}
{{--        <x-label>Type</x-label>--}}
{{--        <x-search-select--}}
{{--            :data="['uk' => 'United Kingdom', 'usa' => 'USofA', 'ru' => 'The Motherland']"--}}
{{--            name="country"--}}
{{--            placeholder="The Placeholder?"--}}
{{--            emptyOptionsMessage="WTF are you looking for?"--}}
{{--        />--}}
{{--    </x-field>--}}

{{--    <x-field>--}}
{{--        <x-label>Region</x-label>--}}
{{--        <x-search-select--}}
{{--        <x-select--}}
{{--            :data="$foobar"--}}
{{--            data="{{ empty($foobar) ? '' : str_replace('"', '\'', json_encode($foobar)) }}"--}}
{{--            :data="[--}}
{{--                'nyc1' => 'New York 1',--}}
{{--                'sgp1' => 'Singapore 1',--}}
{{--                'lon1' => 'London 1',--}}
{{--                'nyc3' => 'New York 3',--}}
{{--                'ams3' => 'Amsterdam 3',--}}
{{--                'fra1' => 'Frankfurt 1',--}}
{{--                'tor1' => 'Toronto 1',--}}
{{--                'blr1' => 'Bangalore 1',--}}
{{--                'sfo3' => 'San Francisco '--}}
{{--            ]"--}}
{{--            name="region"--}}
{{--            wireModel="state.region"--}}
{{--        />--}}
{{--    </x-field>--}}

{{--    <x-field>--}}
{{--        <x-label>Server Size</x-label>--}}
{{--        <x-search-select--}}
{{--            :data="['1' => 'Tiny', '2' => 'Normal', '3' => 'But Uncle Kun, it won\'t fit!']"--}}
{{--            name="size"--}}
{{--        />--}}
{{--    </x-field>--}}

{{--    <p>Provider ID: {{ $state['provider_id'] }}</p>--}}
{{--    <p>Available regions: {{ json_encode(Arr::pluck($availableRegions, 'name', 'slug')) }}</p>--}}

</div>
