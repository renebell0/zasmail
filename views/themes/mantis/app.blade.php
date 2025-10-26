<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @if (isset($page))
        {!! $page->header !!}
        <title>{{ $page->title }} - {{ config('app.settings.name') }}</title>
    @else
        <title>{{ config('app.settings.name') }}</title>
    @endif
    {!! config('app.settings.global.header') !!}
    @if (Illuminate\Support\Facades\Storage::disk('local')->has('public/images/custom-favicon.png'))
        <link rel="icon" href="{{ url('storage/images/custom-favicon.png') }}" type="image/png">
    @else
        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    @endif
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/' . config('app.settings.theme') . '/styles.css') }}">
    <script src="{{ asset('vendor/Shortcode/Shortcode.js') }}"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    @livewireStyles
    {!! config('app.settings.global.css') !!}
    @include('frontend.common.header')
</head>

<body>
    <div class="mantis-theme">
        <div class="container mx-auto p-5 md:p-0">
            <div class="flex items-center">
                <a href="{{ route('home') }}">
                    @if(Illuminate\Support\Facades\Storage::disk('local')->has('public/images/custom-logo.png'))
                    <img class="max-w-logo" src="{{ url('storage/images/custom-logo.png') }}" alt="logo">
                    @else
                    <img class="max-w-logo" src="{{ asset('images/logo.png') }}" alt="logo">
                    @endif
                </a>
                @livewire('frontend.nav')
            </div>
        </div>
        @livewire('frontend.actions', ['in_app' => isset($page) ? true : false])
        <div class="container mx-auto">
            @if(config('app.settings.ads.two'))
            <div class="flex justify-center items-center max-w-full m-4 ads-two">{!! config('app.settings.ads.two') !!}</div>
            @endif
            @if(isset($page))
                @livewire('frontend.page', ['page' => $page])
            @else 
                <div class="spacer mt-5"></div>
                @livewire('frontend.app')
            @endif
            @if(config('app.settings.ads.three'))
            <div class="flex justify-center items-center max-w-full m-4 ads-three">{!! config('app.settings.ads.three') !!}</div>
            @endif
        </div>
    </div>
    @if (config('app.settings.cookie.enable'))
        <div id="cookie" class="hidden fixed w-full bottom-0 left-0 p-4 bg-gray-900 text-white justify-between">
            <div class="py-2">
                {!! __(config('app.settings.cookie.text')) !!}
            </div>
            <div id="cookie_close" class="px-3 py-2 bg-yellow-300 text-gray-900 rounded-md cursor-pointer">
                {{ __('Close') }}
            </div>
        </div>
    @endif

    <!--- Helper Text for Language Translation -->
    <div class="hidden language-helper">
        <div class="error">{{ __('Error') }}</div>
        <div class="success">{{ __('Success') }}</div>
        <div class="copy_text">{{ __('Email ID Copied to Clipboard') }}</div>
    </div>

    @livewireScripts
    @if (!isset($page))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const email = '{{ App\Models\TMail::getEmail(true) }}';
                const add_mail_in_title = "{{ config('app.settings.add_mail_in_title', true) ? 'yes' : 'no' }}"
                if(add_mail_in_title === 'yes') {
                    document.title += ` - ${email}`;
                }
                Livewire.emit('syncEmail', email);
                Livewire.emit('fetchMessages');
            });
        </script>
    @endif
    <script>
        document.addEventListener('stopLoader', () => {
            document.getElementById('refresh').classList.add('pause-spinner');
        });
        let counter = parseInt({{ config('app.settings.fetch_seconds') }});
        setInterval(() => {
            document.getElementById('counter').style.width = document.getElementById('counter').offsetHeight + 'px'
            document.querySelector('#counter div.filler').style.width = (((parseInt({{ config('app.settings.fetch_seconds') }}) - counter) * 100) / parseInt({{ config('app.settings.fetch_seconds') }})) + '%'
            document.querySelector('#counter span.text').innerText = counter
            if (counter === 0 && document.getElementById('imap-error') === null && !document.hidden) {
                document.getElementById('refresh').classList.remove('pause-spinner');
                let even = true
                let temp = setInterval(() => {
                    if(even) {
                        document.getElementById('refresh').parentElement.style.backgroundColor = '#000'
                    } else {
                        document.getElementById('refresh').parentElement.style = null
                    }
                    even = !even
                }, 100);
                setTimeout(() => {
                    clearInterval(temp)
                }, 1000);
                Livewire.emit('fetchMessages');
                counter = parseInt({{ config('app.settings.fetch_seconds') }}) + 1;
            }
            counter--;
            if(document.hidden) {
                counter = 1;
            }
        }, 1000);
    </script>
    {!! config('app.settings.global.js') !!}
    {!! config('app.settings.global.footer') !!}
    @if(config('app.settings.ad_block_detector_filename'))
    <script src="{{ asset('storage/js/' . config('app.settings.ad_block_detector_filename')) }}" defer></script>
    <script defer>
    setTimeout(() => {
        const enable_ad_block_detector = "{{ config('app.settings.enable_ad_block_detector', false) ? 1 : 0 }}"
        if(!document.getElementById('Q8CvrZzY9fphm6gG') && enable_ad_block_detector == "1") {
            document.querySelector('.mantis-theme').remove()
            document.querySelector('body > div').insertAdjacentHTML('beforebegin', `
                <div class="fixed w-screen h-screen bg-red-800 flex flex-col justify-center items-center gap-5 z-50 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-40 w-40" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" />
                    </svg>
                    <h1 class="text-4xl font-bold">{{ __('Ad Blocker Detected') }}</h1>
                    <h2>{{ __('Disable the Ad Blocker to use ') . config('app.settings.name') }}</h2>
                </div>
            `)
        }
    }, 500);
    </script>
    @endif
</body>

</html>
