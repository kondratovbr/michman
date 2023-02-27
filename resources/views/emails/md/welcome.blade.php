{{--NOTE: There's no indentations here because the content will be rendered as Markdown which relies on indentations for formatting.--}}
@component('mail::message')

# Ahoy!

Welcome to Michman - we are excited to have you on board!

Michman takes the difficult and annoying work out of your hands so you can focus on what you do best -
developing your applications.

Rapidly deploy your Python apps on virtual servers provided by DigitalOcean, manage databases, backups,
automate deployments and so much more!

You are currently on a free plan, which limits you to one server and one project,
but you still get access to all features of Michman!

The first step to getting started is connecting your server provider.
This will allow us to create virtual servers for your apps.
If you're not sure how to do this, don't worry - we've got documentation to guide you through the process.
You can find our docs here: [Michman Docs](https://docs.michman.dev/servers/providers/)

So what are you waiting for? Let's get your apps deployed!

@component('mail::button', ['url' => route('account.show', 'providers'), 'color' => 'primary'])
Connect server provider
@endcomponent

Best,<br>
Michman

@slot('subcopy')
{{ __('notifications.cant-click') }}
<span class="break-all">
[{{ route('account.show', 'providers') }}]({{ route('account.show', 'providers') }})
</span>
@endslot

@endcomponent
