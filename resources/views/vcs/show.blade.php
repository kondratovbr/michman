{{--TODO: Do these "narrow" sections (with no title/description part) look a bit odd on narrower desktop screens? Maybe should do something about it.--}}

<x-sub-page name="vcs">

    <x-vcs.connection-section provider="github" />

    <x-narrow-section-separator/>

    <x-vcs.connection-section provider="gitlab" />

    <x-narrow-section-separator/>

    <x-vcs.connection-section provider="bitbucket" />

</x-sub-page>
