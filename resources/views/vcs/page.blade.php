{{--TODO: Do these "narrow" sections (with no title/description part) look a bit odd on narrower desktop screens? Maybe should do something about it.--}}

<x-vcs.connection-section
    :connected="! is_null(user()->github())"
    icon="fab fa-github"
    :title="__('auth.oauth.providers.github.label')"
/>

<x-narrow-section-separator/>

<x-vcs.connection-section
    :connected="! is_null(user()->gitlab())"
    icon="fab fa-gitlab"
    :title="__('auth.oauth.providers.gitlab.label')"
/>

<x-narrow-section-separator/>

<x-vcs.connection-section
    :connected="! is_null(user()->bitbucket())"
    icon="fab fa-bitbucket"
    :title="__('auth.oauth.providers.bitbucket.label')"
/>
