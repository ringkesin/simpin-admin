<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{asset('/img/logokkba-icon.ico')}}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
                document.querySelector('html').classList.remove('dark');
                document.querySelector('html').style.colorScheme = 'light';
            } else {
                document.querySelector('html').classList.add('dark');
                document.querySelector('html').style.colorScheme = 'dark';
            }
        </script>
    </head>
    <body class="antialiased font-inter bg-slate-100 text-slate-600">

        <main class="bg-white">

            <div class="relative flex">

                <!-- Content -->
                <div class="w-full md:w-1/2">

                    <div class="flex flex-col h-full min-h-screen after:flex-1">

                        <!-- Header -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                                <!-- Logo -->
                                <a class="block" href="{{ route('dashboard') }}">
                                    <img class="absolute top-4 left-5" src="{{asset('/assets/media/logo/logokkba.svg')}}" width="160" alt="Authentication decoration" />
                                </a>
                            </div>
                        </div>

                        <div class="w-full max-w-sm px-4 py-8 mx-auto">
                            {{ $slot }}
                        </div>

                    </div>

                </div>

                <!-- Image -->
                <div class="absolute top-0 bottom-0 right-0 hidden md:block md:w-1/2" aria-hidden="true">
                    <img class="object-cover object-center w-full h-full" src="{{ asset('/assets/img/auth-bg-2.jpeg') }}" width="720" height="1024" alt="Authentication image" />
                    <img class="absolute left-0 hidden ml-8 -translate-x-1/2 top-1/4 lg:block" src="{{ asset('/assets/img/auth-decoration.png') }}" width="218" height="224" alt="Authentication decoration" />
                </div>

            </div>

        </main>
    </body>
</html>
