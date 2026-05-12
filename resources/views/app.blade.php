<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Prevent auto translate issues -->
    <meta name="google" content="notranslate">

    <title inertia>{{ config('app.name', 'ACAGMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- ================= GOOGLE TRANSLATE (ONLY HERE) ================= -->
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,am,om,ar',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
        }

        // Re-init after Inertia navigation
        document.addEventListener('inertia:finish', () => {
            if (!document.querySelector('.goog-te-gadget-simple')) {
                googleTranslateElementInit();
            }
        });

        // FIX layout shifting issue
        const observer = new MutationObserver(() => {
            if (document.body.style.top !== '0px') {
                document.body.style.setProperty('top', '0px', 'important');
            }
            if (document.documentElement.style.marginTop !== '0px') {
                document.documentElement.style.setProperty('margin-top', '0px', 'important');
            }
        });

        document.addEventListener("DOMContentLoaded", () => {
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        });
    </script>

    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>

    <!-- ================= GLOBAL STYLES ================= -->
    <style>
        html, body {
            top: 0 !important;
            position: static !important;
            margin-top: 0 !important;
        }

        /* Hide Google top banner */
        .goog-te-banner-frame,
        .goog-te-balloon-frame,
        .goog-te-banner,
        #goog-gt-tt,
        .skiptranslate {
            display: none !important;
            visibility: hidden !important;
        }

        /* Fix highlight */
        .goog-text-highlight {
            background: none !important;
            box-shadow: none !important;
        }

        /* Translate button style */
        .goog-te-gadget-simple {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 6px 10px !important;
            display: inline-flex !important;
            align-items: center !important;
            cursor: pointer !important;
        }

        .goog-te-gadget-icon,
        .goog-logo-link,
        .goog-te-gadget span {
            display: none !important;
        }

        .goog-te-menu-value span:first-child {
            color: #1e293b !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
        }

        /* Ensure dropdown is on top */
        .goog-te-menu-frame {
            z-index: 999999 !important;
        }
    </style>

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">

    @if(isset($systemStatus) && $systemStatus === 'active')
        @inertia
    @else
        <!-- SYSTEM OFFLINE UI -->
        <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f1f5f9;">
            <div style="background:white;padding:40px;border-radius:20px;text-align:center;max-width:500px;">
                <span class="material-icons-outlined" style="font-size:40px;color:red;">settings</span>
                <h1 style="font-size:24px;font-weight:bold;">System Offline</h1>
                <p>The system is currently under maintenance.</p>
            </div>
        </div>
    @endif

</body>
</html>