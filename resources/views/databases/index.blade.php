{{--    TODO: CRITICAL! Don't forget to display the database connection URL or similar, like Forge does. Maybe I could even make a button for users to automatically connect to it.--}}

<livewire:databases.create-database-form :server="$server" />

<x-section-separator/>

<livewire:databases.databases-index-table :server="$server" />

<x-section-separator/>

<livewire:database-users.create-database-user-form :server="$server" />

<x-section-separator/>

<livewire:database-users.database-users-index-table :server="$server" />
