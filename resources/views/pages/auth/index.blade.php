<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PT. Rajawali Sumber Rejeki - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-rsr.png') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .account-pages {
            padding-top: 1rem;
        }

        .card {
            margin-top: 2rem;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.5);
        }

        @media (max-width: 575.98px) {
            .card-body {
                padding: 1rem;
            }
        }

        @media (min-width: 576px) and (max-width: 991.98px) {
            .card-body {
                padding: 2rem;
            }

            .fw-bold.fs-4.ms-2 {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 992px) {
            .card-body {
                padding: 2.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="row">
                            <div class="d-flex align-items-center justify-content-center pt-5">
                                <img src="{{ asset('assets/images/logo-rsr.png') }}" style="width: 100px" alt="Logo">
                            </div>
                            <div class="d-flex align-items-center justify-content-center">
                                <p class="mb-0 text-dark fw-bold fs-4 ms-2">PT. Rajawali Sumber Rejeki</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="p-1">
                                <form class="form-horizontal" action="{{ route('auth.login') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            placeholder="Enter username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" placeholder="Enter password"
                                                aria-label="Password" aria-describedby="password-addon" name="password">
                                        </div>
                                    </div>
                                    <div class="mt-4 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light fw-bold"
                                            type="submit">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center fw-bold">
                        <div>
                            <p>Copyright©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> PT. Rajawali Sumber Rejeki.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if ($errors->has('login'))
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '{{ $errors->first('login') }}',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
</body>

</html>