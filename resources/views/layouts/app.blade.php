<!DOCTYPE html>
<html lang="en">

<head>
    @include('includes.meta')
    <title>RSR - @yield('title')</title>

    @stack('before-app-style')
    @include('includes.style')
    @stack('after-app-style')

</head>

<body data-sidebar="dark" data-layout-mode="light">

    <div id="layout-wrapper">

        @include('includes.header')

        @include('includes.sidebar')

        <div class="main-content">

            <div class="page-content">

                <div class="container-fluid">
                    <div id="table-preloader">
                        <div id="status">
                            <div class="spinner-chase">
                                <div class="chase-dot"></div>
                                <div class="chase-dot"></div>
                                <div class="chase-dot"></div>
                                <div class="chase-dot"></div>
                                <div class="chase-dot"></div>
                                <div class="chase-dot"></div>
                            </div>
                        </div>
                    </div>

                    <div id="table-content" style="display: none;">
                        @yield('content')
                    </div>
                </div>

            </div>

            @include('includes.footer')

        </div>

    </div>

    @stack('before-app-script')
    @include('includes.script')
    @stack('after-app-script')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                document.getElementById('table-preloader').style.display = 'none';
                document.getElementById('table-content').style.display = 'block';
            });
        });
    </script>

</body>

</html>