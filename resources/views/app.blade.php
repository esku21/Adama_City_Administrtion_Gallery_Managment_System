<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

        <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    includedLanguages: 'en,am,om', 
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                    autoDisplay: false
                }, 'google_translate_element');
            }
        </script>
        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>

        <style>
            /* Reset Google's layout shifts */
            body { top: 0 !important; position: static !important; }
            .goog-te-banner-frame.skiptranslate, .goog-te-banner-frame, .goog-te-balloon-frame { display: none !important; }
            
            /* Professional Selector Styling */
            .goog-te-gadget-simple {
                background-color: #f8fafc !important; 
                border: 1px solid #e2e8f0 !important;
                border-radius: 8px !important;
                padding: 4px 10px !important;
                display: flex !important;
                align-items: center !important;
                cursor: pointer !important;
            }
            
            .goog-te-gadget-icon { display: none !important; }
            .goog-logo-link { display: none !important; }
            .goog-te-gadget { color: transparent !important; }

            .goog-te-menu-value span {
                color: #475569 !important;
                font-size: 13px !important;
                font-weight: 600 !important;
                font-family: 'Figtree', sans-serif !important;
                text-decoration: none !important;
            }

            /* Tooltip and Highlight Removal */
            #goog-gt-tt { display: none !important; }
            .goog-text-highlight { background: none !important; box-shadow: none !important; }

            /* Position the translator floating at the top right */
            .translate-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            }
        </style>

        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        <div class="translate-container">
            <div id="google_translate_element"></div>
        </div>

        @inertia
    </body>
</html>