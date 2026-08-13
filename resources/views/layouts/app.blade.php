<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Retort</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SB Admin CSS -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- Tambahan jika ada -->
    @stack('styles')
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('partials.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('partials.topbar')

                    @yield('content')
            </div>

            @include('partials.footer')
        </div>
    </div>

    <!-- SB Admin Scripts -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/select2/js/select2.min.js') }}"></script>

    <script>
        var windowWidth = $(window).width();
        if(windowWidth <= 1024){
            $('#page-top').addClass('sidebar-toggled');
            $('.sidebar').addClass('toggled');
        } else {
            $('#page-top').removeClass('sidebar-toggled');
            $('.sidebar').removeClass('toggled');
        }
    </script>

    <!-- Tambahan JS -->
    @stack('scripts')
</body>
</html>
