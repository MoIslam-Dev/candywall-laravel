<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Data deletion</title>
    <meta name="msapplication-TileColor" content="#206bc4" />
    <meta name="theme-color" content="#206bc4" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <link rel="shortcut icon" href="/public/img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" href="/public/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/public/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/public/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/public/img/favicon/site.webmanifest">
    <!-- CSS files -->
    <link href="/public/css/jqvmap/jqvmap.min.css" rel="stylesheet" />
    <link href="/public/css/tabler.min.css" rel="stylesheet" />
    <link href="/public/css/tablerd.min.css" rel="stylesheet" />
    <style>
        .decor h5 {
            font-weight: bold !important;
            font-size: 1em !important
        }

    </style>
</head>

<body class="antialiased">
    <div class="page">
        <div class="content">
            <div class="container-xl col-11 col-md-10 col-lg-8">
                <div class="row justify-content-center">
                    <div class="card card-body decor">
                        <div class="text-center h2 mb-4">Data Deletion Instructions</div>
                        <h3 class="text-red ml-3">All the data will be removed permanently and this action cannot be undone.</h3>
                        <ul>
                            <li>Open the app.</li>
                            <li>Click on sidebar (hamburger icon)</li>
                            <li>Go to profile screen.</li>
                            <li>Scroll down to the bottom.</li>
                            <li>Click on <span class="text-red font-weight-bold">Delete Account</span> red button.</li>
                            <li>Follow the procedure.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <footer class="footer footer-transparent">
                <div class="container">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ml-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item"><a href="{{route('privacy')}}" class="link-secondary">Privacy Policy</a></li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            Copyright © {{date("Y")}}
                            <a href="{{env('APP_URL')}}" class="link-secondary">{{env('APP_NAME')}}</a>.
                            All rights reserved.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
