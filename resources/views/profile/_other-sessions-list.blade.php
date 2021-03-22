<div class="space-y-6">
    @foreach($this->sessions as $session)
        <div class="flex items-center">
            <div>
                @if ($session->agent->isDesktop())
                    <x-icon size="10">
                        <i class="far fa-2x fa-desktop"></i>
                    </x-icon>
                @else
                    <x-icon size="10">
                        <i class="far fa-2x fa-mobile-alt"></i>
                    </x-icon>
                @endif
            </div>

            <div class="ml-3">
                <div>
                    {{ $session->agent->platform() }} - {{ $session->agent->browser() }}
                </div>
                <div>
                    {{ $session->ip_address }},
                    @if ($session->is_current_device)
                        <span class="text-green-400 font-semibold">
                            {{ __('account.profile.sessions.this_device') }}
                        </span>
                    @else
                        <span class="text-gray-400">
                            {{ __('account.profile.sessions.last_active') }}
                            {{ $session->last_active }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
