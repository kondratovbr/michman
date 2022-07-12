<script>
    window.APP_DEBUG = {{ config('app.debug') ? 'true' : 'false' }};

    window.PUSHER_APP_KEY = '{{ config('broadcasting.connections.' . config('broadcasting.default') . '.key') }}';
    window.PUSHER_APP_CLUSTER = '{{ config('broadcasting.connections.' . config('broadcasting.default') . '.options.cluster') }}';
    window.PUSHER_FORCE_TLS = {{ config('broadcasting.connections.' . config('broadcasting.default') . '.options.useTLS', false) ? 'true' : 'false' }}

</script>
