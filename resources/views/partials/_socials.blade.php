{{-- Generic Social Tags --}}
<meta property="og:site_name" content="{{ config('app.name', 'App') }}">
<meta property="og:title" content="{{ __('general.title') }}">
<meta property="og:description" content="{{ __('general.description') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('images/twitter-og.png') }}">
<meta property="og:image:alt" content="{{ __('general.title') }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="en_US">

{{-- Twitter-specific Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="{{ asset('images/twitter-og.png') }}">
<meta name="twitter:image:alt" content="{{ __('general.title') }}">
<meta name="twitter:site" content="{{ config('app.twitter_account') }}">
<meta name="twitter:creator" content="{{ config('app.twitter_account') }}">
<meta name="twitter:title" content="{{ __('general.title') }}">
<meta name="twitter:description" content="{{ __('general.description') }}">
<meta name="twitter:label1" value="Documentation">
<meta name="twitter:data1" value="{{ config('app.docs_url') }}">
{{--<meta name="twitter:label2" value="API">--}}
{{--<meta name="twitter:data2" value="https://docs.michman.dev/api">--}}

{{-- Facebook Tags --}}
{{--<meta property="fb:app_id" content="1234567890">--}}

{{-- Generic Metas --}}
<meta itemprop="name" content="{{ config('app.name', 'App') }}">
<meta itemprop="description" content="{{ __('general.description') }}">
<meta itemprop="image" content="{{ asset('images/twitter-og.png') }}">
