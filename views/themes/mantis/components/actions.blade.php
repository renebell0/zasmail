<div x-data="{ in_app: {{ $in_app ? 'true' : 'false' }} }" class="bg-teal-400 bg-actions-pattern relative" style="background-color: {{ config('app.settings.colors.primary') }}">
    <div class="container mx-auto bg-gray-500 bg-opacity-30">
        <div class="px-5 md:px-0">
            <div x-show.transition.in="!in_app" class="in-app-actions flex flex-col md:flex-row items-center justify-center" style="display: none;">
                @if(config('app.settings.ads.one'))
                <div wire:ignore class="flex justify-center items-center max-w-full m-4 ads-one">{!! config('app.settings.ads.one') !!}</div>
                @endif
                <div class="flex-1 py-10 space-y-5">
                    <h2 class="text-xl text-white font-bold text-center">{{ __('Your temporary email address is ready') }}</h2>
                    <form class="lg:max-w-lg lg:mx-auto flex space-x-5" action="#" method="post">
                        <div class="relative flex-1">
                            <x-jet-dropdown align="top" width="w-full">
                                <x-slot name="trigger">
                                    <div class="block appearance-none w-full bg-white py-4 px-5 pr-8 cursor-pointer focus:outline-none select-none border-b-4 border-b-orange-500" style="border-color: {{ config('app.settings.colors.secondary') }}" id="email_id">{{ $email }}</div>
                                </x-slot>
                                <x-slot name="content">
                                    @foreach($emails as $email)
                                    <x-jet-dropdown-link href="{{ route('switch', $email) }}">
                                        {{ $email }}
                                    </x-jet-dropdown-link>
                                    @endforeach
                                </x-slot>
                            </x-jet-dropdown>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        <div id="counter" class="bg-gray-50 flex items-center justify-center relative">
                            <div class="filler h-full absolute top-0 left-0 transition-all ease-in-out" style="background-color: {{ config('app.settings.colors.primary') }}50"></div>
                            <span class="text"></span>
                        </div>
                    </form>
                </div>
                @if(config('app.settings.ads.five'))
                <div wire:ignore class="flex justify-center items-center max-w-full m-4 ads-five">{!! config('app.settings.ads.five') !!}</div>
                @endif
                <div class="w-full md:w-auto grid grid-cols-4 md:grid-cols-1 text-white space-x-0.5 md:space-x-0 md:space-y-0.5">
                    <div class="btn_copy bg-black bg-opacity-30 text-center cursor-pointer py-3 px-4 hover:bg-opacity-25">
                        <i class="far fa-copy"></i>
                        <div class="text-xs">{{ __('Copy') }}</div>
                    </div>
                    <div onclick="document.getElementById('refresh').classList.remove('pause-spinner')" wire:click="$emit('fetchMessages')" class="bg-black bg-opacity-30 text-center cursor-pointer py-3 px-4 hover:bg-opacity-25">
                        <i id="refresh" class="fas fa-sync-alt fa-spin"></i>
                        <div class="text-xs">{{ __('Refresh') }}</div>
                    </div>
                    <div x-on:click="in_app = true" class="bg-black bg-opacity-30 text-center cursor-pointer py-3 px-4 hover:bg-opacity-25">
                        <i class="far fa-plus-square"></i>
                        <div class="text-xs">{{ __('New') }}</div>
                    </div>
                    <div wire:click="deleteEmail" class="bg-black bg-opacity-30 text-center cursor-pointer py-3 px-4 hover:bg-opacity-25">
                        <i class="far fa-trash-alt"></i>
                        <div class="text-xs">{{ __('Delete') }}</div>
                    </div>
                </div>
            </div>
            <div x-show.transition.in="in_app" class="app-action flex flex-col md:flex-row" style="display: none;">
                @if(config('app.settings.ads.one'))
                <div wire:ignore class="flex justify-center items-center max-w-full m-4 ads-one">{!! config('app.settings.ads.one') !!}</div>
                @endif
                <div class="mx-auto py-10">
                    <div class="flex space-x-3">
                        <form wire:submit.prevent="create" method="post" class="flex-1 space-x-3 flex">
                            <div class="flex-1 space-y-3">
                                @if(config('app.settings.captcha') == 'hcaptcha' || config('app.settings.captcha') == 'recaptcha2')
                                <div>
                                    <x-captcha field="captcha" class="" />
                                </div>
                                @endif
                                <input class="block appearance-none w-full py-4 px-5 bg-white border-0 focus:outline-none placeholder-opacity-50" type="text" name="user" id="user" wire:model="user" placeholder="{{ __('Enter Username') }}">
                                <div class="relative">
                                    <x-jet-dropdown align="top" width="w-full">
                                        <x-slot name="trigger">
                                            <input x-ref="domain" type="text"
                                                class="block appearance-none w-full md:w-96 bg-white py-4 px-5 pr-8 cursor-pointer focus:outline-none select-none placeholder-opacity-50 border-0"
                                                placeholder="{{ __('Select Domain') }}" name="domain" id="domain"
                                                wire:model="domain" readonly>
                                        </x-slot>
                                        <x-slot name="content">
                                            @foreach ($domains as $domain)
                                                <a x-on:click="$refs.domain.value = '{{ $domain }}'; $wire.setDomain('{{ $domain }}')"
                                                    class='block px-4 py-2 text-sm leading-5 text-gray-700 cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out'>{{ $domain }}</a>
                                            @endforeach
                                        </x-slot>
                                    </x-jet-dropdown>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <button id="create"
                                class="flex items-center bg-indigo-600 text-white text-center py-4 px-5 hover:bg-opacity-75 cursor-pointer"
                                style="background-color: {{ config('app.settings.colors.secondary') }}">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                        <div class="flex items-center">
                            <div class="border border-dashed border-white h-6"></div>
                        </div>
                        <form wire:submit.prevent="random" method="post" class="flex">
                            <button id="random"
                                class="flex items-center bg-green-400 text-white text-center py-4 px-5 hover:bg-opacity-75 cursor-pointer"
                                style="background-color: {{ config('app.settings.colors.tertiary') }}">
                                <i class="fas fa-random"></i>
                            </button>
                        </form>
                    </div>
                    @if (count($emails) > 0 && $in_app)
                        <div class="mt-5">
                            <a href="{{ route('mailbox') }}"
                                class="flex items-center bg-white bg-opacity-25 text-white text-center py-3 px-5 space-x-3 hover:bg-opacity-10 cursor-pointer text-sm">
                                <i class="fas fa-chevron-left"></i>
                                <span>{{ __('Get back to MailBox') }}</span>
                            </a>
                        </div>
                    @endif
                    @if (!$in_app)
                        <div class="mt-5">
                            <div x-on:click="in_app = false"
                                class="flex justify-center items-center bg-white bg-opacity-25 text-white text-center py-3 px-5 hover:bg-opacity-10 cursor-pointer text-sm">
                                <span>{{ __('Cancel') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
                @if(config('app.settings.ads.five'))
                <div wire:ignore class="flex justify-center items-center max-w-full m-4 ads-five">{!! config('app.settings.ads.five') !!}</div>
                @endif
            </div>
        </div>
    </div>
    @if(config('app.settings.captcha') == 'recaptcha3')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('app.settings.recaptcha3.site_key') }}"></script>
    <script>
        const handle = (e) => {
            e.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('app.settings.recaptcha3.site_key') }}', { action: 'submit' }).then(function(token) {
                    Livewire.emit('checkReCaptcha3', token, e.target.id);
                });
            });
        }
        document.getElementById('create').addEventListener('click', handle);
        document.getElementById('random').addEventListener('click', handle);
    </script>
    @endif
</div>