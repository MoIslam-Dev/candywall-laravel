<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{env('APP_NAME', 'Candy')}}</title>
		<link rel="shortcut icon" href="/public/img/favicon/favicon.ico" />
		<link rel="apple-touch-icon" href="/public/img/favicon/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/public/img/favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/public/img/favicon/favicon-16x16.png">
		<link rel="manifest" href="/public/img/favicon/site.webmanifest">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #2a0889;
                color: #fff;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
            <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">الرئيسية</a>
                    @else
                        <a href="{{ route('login') }}">دخول</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">تسجيل</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                {{env('APP_NAME', 'Candy')}}
                </div>
            </div>
        </div>
    </body>
</html>
