<div class="space-y-6">

    <x-field>
        <x-label>API Credentials</x-label>
        <x-select/>
    </x-field>

    <x-field>
        <x-label>Type</x-label>
        <x-search-select/>
    </x-field>

    <x-field>
        <x-label>Server Size</x-label>
        <x-search-select/>
    </x-field>

    {{-- TODO: Don't forget to add an explanation here. Not everyone knows where the name will be used and even WTF is it. --}}
    <x-field>
        <x-label>Name</x-label>
        <x-inputs.text name="name"/>
    </x-field>

</div>
