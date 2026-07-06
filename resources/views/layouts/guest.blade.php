<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
    <div class="fixed inset-0 min-h-screen flex flex-col items-center justify-center overflow-x-hidden overflow-y-auto bg-[linear-gradient(160deg,#0e1636_0%,#17356b_32%,#2f8a4a_68%,#a9d144_100%)] z-50">

            <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fff_1px,transparent_1px)] bg-[size:3px_3px]"></div>

            <svg class="absolute bottom-0 left-0 w-full h-[46%]" viewBox="0 0 1900 500" preserveAspectRatio="none">
                <path d="M0,320 L180,220 L340,270 L480,190 L620,260 L760,200 L900,280 L1050,180 L1220,260 L1400,190 L1560,250 L1750,170 L1900,240 L1900,500 L0,500 Z" fill="#0b1330" opacity="0.55"/>
                <path d="M0,380 L220,300 L400,350 L560,290 L740,360 L920,300 L1100,370 L1300,300 L1500,360 L1700,310 L1900,370 L1900,500 L0,500 Z" fill="#0b1330" opacity="0.85"/>
            </svg>

            <svg class="absolute right-[9%] top-0 h-full w-[60px] pointer-events-none" viewBox="0 0 60 900" preserveAspectRatio="none">
                <path d="M30,0 C10,150 50,250 20,400 C-5,520 45,650 25,820" stroke="#c9e070" stroke-width="2.5" stroke-dasharray="1 10" fill="none" stroke-linecap="round"/>
                <circle cx="20" cy="400" r="7" fill="none" stroke="#c9e070" stroke-width="2"/>
            </svg>

            <div class="text-white w-44 sm:w-52 mb-8 sm:mb-12">
                <img src="{{ Vite::asset('resources/views/Images/logo-white.svg') }}" alt="JobtrekLogo">
            </div>

            <div class="relative z-10 w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
