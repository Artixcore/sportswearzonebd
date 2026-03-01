<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', config('seo.default_description', ''))">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    @stack('meta')
    @stack('json-ld')

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            base: '#0f172a',
                            'base-light': '#1e293b',
                            accent: '#059669',
                            'accent-hover': '#047857',
                            surface: '#ffffff',
                            muted: '#f1f5f9',
                            'muted-border': '#e2e8f0'
                        }
                    }
                }
            }
        </script>
    @endif
</head>
<body class="flex flex-col min-h-screen bg-muted antialiased">
    @include('partials.app-nav')

    <main class="flex-grow">
        @if(session('success'))
            <div class="bg-accent text-surface px-4 py-3 text-center text-sm font-medium" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-600 text-white px-4 py-3 text-center text-sm font-medium" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-600 text-white px-4 py-3 text-center text-sm font-medium" role="alert">
                {{ session('info') }}
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.app-footer')

    @stack('scripts')
    @php $metaPixelId = \App\Models\Setting::get('meta_pixel_id', config('meta.pixel_id')); @endphp
    @if($metaPixelId)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $metaPixelId }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1" alt="" /></noscript>
    @endif
</body>
</html>
