<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{env('APP_NAME')}} - Login</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
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
    <link href="./public/css/tabler.min.css" rel="stylesheet" />
    <link href="./public/css/demo.min.css" rel="stylesheet" />
</head>

<body class="antialiased border-top-wide border-primary d-flex flex-column">
    <div class="flex-fill d-flex flex-column justify-content-center">
        <div class="container-tight py-6">
            <div class="text-center mb-4">
                <img src="./static/logo.svg" height="36" alt="">
            </div>
            <form class="card card-md" action="{{ route('login') }}" method="post">
                @csrf
                <input type="hidden" name="_token" value="{{ app('request')->session()->get('_token') }}">
                <div class="card-body">
                    <h2 class="mb-5 text-center" style="margin-bottom:20px !important">تسجيل الدخول إلى لوحة الإدارة</h2>
                    <div class="mb-3">
                        @error('email')
                        <label class="form-label text-red font-weight-bold">{{ $message }}</label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                            
                            </span>
                            <input type="email" name="email" class="form-control is-invalid" value="{{ old('email') }}" required autocomplete="email" autofocus tabindex="100">
                        </div>
                        @else
                        <label class="form-label">عنوان البريد الإلكتروني</label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                             
                            </span>
                            <input type="email" name="email" class="form-control" placeholder="البريد الالكتروني" required autofocus tabindex="100">
                        </div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        @error('password')
                        <label class="form-label text-red font-weight-bold">{{ $message }}<span class="form-label-description text-blue font-weight-normal"><a href="./forgot-password.html" tabindex="104">لقد نسيت كلمة المرور</a></span></label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                          
                            </span>
                            <div class="input-group input-group-flat"><input type="password" name="password" class="form-control is-invalid" placeholder="الرقم السري" required autocomplete="current-password" tabindex="101"></div>
                        </div>
                        @else
                        <label class="form-label">@if (Route::has('forget')) <span class="form-label-description"><a href="{{ route('forget') }}" tabindex="104">لقد نسيت كلمة المرور</a></span> @endif </label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                           
                              
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="الرقم السري" required tabindex="101">
                        </div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} class="form-check-input" tabindex="102" />
                            <span class="form-check-label">تذكرني على هذا الجهاز</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-success btn-block" tabindex="103">تسجيل الدخول</button>
                    </div>
                </div>
 
            </form>
        </div>
    </div>
    <!-- JS -->
    <script src="./public/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
