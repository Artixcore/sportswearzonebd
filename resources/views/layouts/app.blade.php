<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', \App\Models\Setting::get('seo_default_title', config('app.name')))</title>
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('seo_default_description', config('seo.default_description', '')))">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    @stack('meta')
    @stack('json-ld')

    @php
        $gtmId = \App\Models\Setting::get('gtm_id');
        $gaMeasurementId = \App\Models\Setting::get('ga_measurement_id');
        $headerScripts = \App\Models\Setting::get('header_scripts');
        $metaVerificationScripts = \App\Models\Setting::get('meta_verification_scripts');
    @endphp
    @if($gtmId)
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
    @endif
    @if($gaMeasurementId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $gaMeasurementId }}');</script>
    @endif
    @if($metaVerificationScripts){!! $metaVerificationScripts !!}@endif
    @if($headerScripts){!! $headerScripts !!}@endif

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            };
            window.showAlert = function(type, title, text, options) {
                var opts = Object.assign({ title: title || '', text: text || '' }, options || {});
                if (type === 'success') opts.icon = 'success';
                else if (type === 'error') opts.icon = 'error';
                else if (type === 'warning') opts.icon = 'warning';
                return Swal.fire(opts);
            };
            window.showConfirm = function(title, text, onConfirm, options) {
                return Swal.fire(Object.assign({
                    title: title || 'Are you sure?',
                    text: text || '',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#6b7280'
                }, options || {})).then(function(result) {
                    if (result.isConfirmed && typeof onConfirm === 'function') onConfirm();
                    return result;
                });
            };
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

    <div id="toast-container" class="fixed right-4 top-20 z-[100] flex flex-col gap-2" aria-live="polite"></div>

    @include('partials.app-footer')

    @php $footerScripts = \App\Models\Setting::get('footer_scripts'); @endphp
    @if($footerScripts){!! $footerScripts !!}@endif
    @php $gtmId = \App\Models\Setting::get('gtm_id'); @endphp
    @if($gtmId)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
    (function() {
        var token = document.querySelector('meta[name="csrf-token"]');
        if (token && typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token.getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
        }
        window.updateNavCartCount = function(count) {
            var n = parseInt(count, 10) || 0;
            var text = n > 99 ? '99+' : String(n);
            document.querySelectorAll('.nav-cart-count').forEach(function(el) {
                el.textContent = text;
                el.style.display = n > 0 ? '' : 'none';
            });
        };
        window.showToast = function(message, type) {
            type = type || 'success';
            var container = document.getElementById('toast-container');
            if (!container) return;
            var el = document.createElement('div');
            el.className = 'rounded-lg px-4 py-3 text-sm font-medium text-white shadow-lg ' + (type === 'error' ? 'bg-red-600' : 'bg-accent');
            el.textContent = message;
            container.appendChild(el);
            setTimeout(function() {
                if (el.parentNode) el.parentNode.removeChild(el);
            }, 4000);
        };
    })();
    </script>
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
