<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'مكتب المحاماة') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            min-height: 100%;
            background: #F8F5F0;
            overflow-x: clip;
        }

        body {
            width: 100%;
            max-width: 100%;
            min-height: 100vh;
            margin: 0;
            overflow-x: clip;
            font-family: 'Cairo', sans-serif;
            color: #302019;
            background: #F8F5F0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }

        img,
        svg,
        video,
        canvas {
            max-width: 100%;
        }

        ::selection {
            color: #302019;
            background: #E8D8B7;
        }
    </style>
</head>

<body class="antialiased">
    <div class="flex min-h-screen w-full max-w-full min-w-0 flex-col overflow-x-clip"
        style="background-color: #F8F5F0;">

        @include('layouts.navigation')

        @isset($header)
            <header class="w-full max-w-full border-b border-stone-200/70 bg-white">
                <div class="mx-auto w-full max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="w-full min-w-0 max-w-full flex-1">
            {{ $slot }}
        </main>

        <footer class="mt-auto w-full max-w-full border-t border-stone-200 bg-white">
            <div
                class="mx-auto flex w-full max-w-7xl min-w-0 flex-col gap-3 px-4 py-4 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">

                {{-- Copyright --}}
                <div class="text-center text-xs leading-6 text-stone-500 md:text-right">
                    <span>© {{ date('Y') }}</span>
                    <span class="mx-1" style="color: #A88443;">·</span>
                    <span class="font-semibold" style="color: #302019;">
                        مكتب المحاماة
                    </span>
                    <span class="mx-1" style="color: #A88443;">·</span>
                    جميع الحقوق محفوظة
                </div>

                {{-- Footer links --}}
                <div
                    class="flex min-w-0 flex-wrap items-center justify-center gap-x-4 gap-y-2 text-xs text-stone-500 md:justify-start">
                    <a href="{{ route('profile.edit') }}" class="transition hover:text-stone-900">
                        المساعدة والحساب
                    </a>

                    <span class="hidden h-3 w-px bg-stone-200 sm:block"></span>

                    <span class="cursor-default text-stone-400">
                        الدعم الفني
                    </span>

                    <span class="hidden h-3 w-px bg-stone-200 sm:block"></span>

                    <span class="cursor-default text-stone-400">
                        الشروط والأحكام
                    </span>
                </div>
            </div>
        </footer>

        <div class="h-1 w-full max-w-full shrink-0" style="background: #A88443;"></div>
    </div>
</body>

</html>
