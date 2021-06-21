{{--TODO: Do these "narrow" sections (with no title/description part) look a bit odd on narrower desktop screens? Maybe should do something about it.--}}

<x-sub-page name="vcs">

    <x-vcs.connection-section oauthProvider="github" />

    <x-narrow-section-separator/>

    <x-vcs.connection-section oauthProvider="gitlab" />

    <x-narrow-section-separator/>

    <x-vcs.connection-section oauthProvider="bitbucket" />

</x-sub-page>
