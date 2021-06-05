<!DOCTYPE html>
<html lang="tr">
    <head>
        
        <meta charset="utf-8" />
        
        <title>Ruberu | Pazaryeri Entegrasyon - @yield('title')</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Pazaryeri Entegrasyonu" name="description" />
        <meta content="Zeyd Özer" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        
        <!-- App favicon -->
        <link rel="shortcut icon" href="/assets/images/favicon.png">

        <!-- App css -->
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />

    </head>
    <body class="authentication-bg">

        <div class="account-pages my-5">
            <div class="container">
                @yield('content')
            </div>
            <!-- end container -->
        </div>
        <!-- end account-pages -->

        <!-- Vendor js -->
        <script src="/assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="/assets/js/app.min.js"></script>
        
    </body>
</html>