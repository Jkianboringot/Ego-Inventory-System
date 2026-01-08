<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EgoEnt') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('admin-lte/css/adminlte.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary"> 
    <div class="app-wrapper">
        @livewire('navigation-menu')
        
        <aside class="app-sidebar bg-inv-primary shadow" data-bs-theme="dark"> 
            <div class="sidebar-brand text-inv-primary">
                <span class="brand-text fw-light">Ego Ent Inventory</span>
            </div>
            <x-sidebar-navigation />
        </aside>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    {{ $slot }}
                </div>
            </div> 
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script>
    <script src="{{ asset('admin-lte/js/adminlte.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
        integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
        integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
        integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ✅ FIXED: Use data-navigate-once to prevent re-execution -->
    <script data-navigate-once>
        document.addEventListener('DOMContentLoaded', function() {
            // Sortable init
            const connectedSortables = document.querySelectorAll(".connectedSortable");
            connectedSortables.forEach((connectedSortable) => {
                new Sortable(connectedSortable, {
                    group: "shared",
                    handle: ".card-header",
                });
            });

            const cardHeaders = document.querySelectorAll(".connectedSortable .card-header");
            cardHeaders.forEach((cardHeader) => {
                cardHeader.style.cursor = "move";
            });
        });

        // ✅ Declare Toast globally ONCE
        window.Toast = Swal.mixin({
            toast: true,
            position: 'top-right',
            timer: 7500,
            timerProgressBar: true,
        });

        // ✅ Set up Livewire event listener ONCE
        document.addEventListener('livewire:init', () => {
            Livewire.on('done', (event) => {
                if (event.success) window.Toast.fire({ icon: 'success', text: event.success });
                if (event.warning) window.Toast.fire({ icon: 'warning', text: event.warning });
                if (event.info) window.Toast.fire({ icon: 'info', text: event.info });
                if (event.error) window.Toast.fire({ icon: 'error', text: event.error });
                if (event.question) window.Toast.fire({ icon: 'question', text: event.question });
            });
        });
    </script>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
</body>
</html>