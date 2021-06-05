@if (Cookie::has('user'))
<script>location.href = '/'</script>
@endif

<!DOCTYPE html>
<html lang="tr">
    <head>
        
        <meta charset="utf-8" />
        
        <title>Ruberu | Pazaryeri Entegrasyon - {{ $title }}</title>
        
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
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="card mb-0">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-6 p-5">
                                        <div class="mx-auto mb-5">
                                            <a href="index.html">
                                                <img src="/assets/images/logo.png" alt="" height="24" />
                                                <!-- <h3 class="d-inline align-middle ml-1 text-logo">Shreyu</h3> -->
                                            </a>
                                        </div>

                                        <h6 class="h5 mb-0 mt-4">
                                            @if ($title == 'Giriş Yap')
                                            Tekrar hoşgeldiniz!
                                            @elseif ($title == 'Parola Sıfırla')
                                            Parola Sıfırla
                                            @endif
                                        </h6>
                                        <p class="text-muted mt-1 mb-4">
                                            @if ($title == 'Giriş Yap')
                                            Panele erişmek için e-posta adresinizi ve parolanızı girin.
                                            @elseif ($title == 'Parola Sıfırla')
                                            E-posta adresinizi girin ve size parolanızı yenilemek için bağlantı içeren bir e-posta gönderelim.
                                            @endif
                                        </p>

                                        <form class="authentication-form needs-validation" novalidate>
                                            <div class="form-group">
                                                <label class="form-control-label" for="email">E-Posta Adresi</label>
                                                <div class="input-group input-group-merge">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="icon-dual" data-feather="mail"></i>
                                                        </span>
                                                    </div>
                                                    <input type="email" class="form-control" id="email" 
                                                    placeholder="lorem@ipsum.com" required name="mail">
                                                    <div class="invalid-feedback">Lütfen bir e-posta adresi girin.</div>
                                                </div>
                                            </div>

                                            @if ($title != 'Parola Sıfırla')
                                            <div class="form-group mt-4">
                                                <label class="form-control-label" for="password">Parola</label>
                                                <a href="/token" class="float-right text-muted text-unline-dashed ml-1">Parolanızı mı unuttunuz?</a>
                                                <div class="input-group input-group-merge">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="icon-dual" data-feather="lock"></i>
                                                        </span>
                                                    </div>
                                                    <input type="password" class="form-control" id="password" 
                                                    placeholder="••••••••" required name="pass">
                                                    <div class="invalid-feedback">Lütfen bir parola girin.</div>
                                                </div>
                                            </div>
                                            @endif

                                            @if ($title == 'Parola Yenile')
                                            <div class="form-group mt-4">
                                                <label class="form-control-label" for="repeat">Parola Tekrar</label>
                                                <div class="input-group input-group-merge">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="icon-dual" data-feather="lock"></i>
                                                        </span>
                                                    </div>
                                                    <input type="password" class="form-control" id="repeat" 
                                                    placeholder="••••••••" required name="repeat">
                                                    <div class="invalid-feedback">Lütfen parolanızı tekrar girin.</div>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="form-group mt-4 mb-0 text-center">
                                                <button class="btn btn-primary btn-block d-flex align-items-center 
                                                    justify-content-center" type="submit">
                                                    <div class="spinner-grow text-white mr-2" role="status" style="display: none">
                                                        <span class="sr-only">Yükleniyor...</span>
                                                    </div>
                                                    <span>
                                                        @if ($title == 'Giriş Yap')
                                                        Oturum Aç
                                                        @elseif ($title == 'Parola Sıfırla')
                                                        Gönder
                                                        @elseif ($title == 'Parola Yenile')
                                                        Yenile
                                                        @endif
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-lg-6 d-none d-md-inline-block">
                                        <div class="auth-page-sidebar">
                                            <div class="overlay"></div>
                                            <div class="auth-user-testimonial">
                                                <p class="font-size-24 font-weight-bold text-white mb-1 px-3">
                                                    Doğru içgörü markanızı hedefinize ulaştırır.
                                                </p>
                                                <p class="lead">
                                                    "Müşteri beklentilerini karşılamak için öncelikle iyi bir analiz yapar, strateji üretiriz."
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->

                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->

        <!-- Vendor js -->
        <script src="/assets/js/vendor.min.js"></script>

        <!-- Plugin js-->
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Validation init js-->
        <script src="/assets/js/pages/form-validation.init.js"></script>

        <!-- App js -->
        <script src="/assets/js/app.min.js"></script>

        <style>

            .auth-page-sidebar
            {
                background-position: center;
                background-image: url('//ruberu.com/images/ruberu-reklam-ajansi-insight.jpg');
            }

            .spinner-grow
            {
                width: 21px;
                height: 21px;
            }

        </style>

        <script>
            
            function result(icon, msg)
            {
                var icons = 
                {
                    true: ['check', 'success', 'Başarılı'],
                    false: ['times', 'warning', 'Uyarı'],
                    error: ['exclamation', 'danger', 'Hata'],
                };

                    _class = 'uil-'+ icons[icon][0] +'-circle text-'+ icons[icon][1] +' display-3';
                    
                $('#result-icon').attr('class', _class);

                $('#result-title').html(icons[icon][2]);

                $('#result-msg').html(msg);

                if (icon == true)

                    $('#result-buttons').hide();

                $('#result').modal('show');
            }

            $('[type="submit"]').click(function()
            {
                $(this).attr('click', true);
            });

            $('form').submit(function(e)
            {
                e.preventDefault();

                var data = new FormData(this), 
                    form = $(this),
                    button = $(this).find('[click]');
                
                data.append('_token', '{{ csrf_token() }}');

                $.ajax(
                {
                    url: location.href,
                    type: 'post',
                    data: data,
                    processData: false,
                    contentType: false,
                    beforeSend: function() 
                    {
                        button.find('span').hide();

                        button.find('div').show();

                        form.find('*').attr('disabled', true);
                    },
                    success: function(resp)
                    {
                        result(resp[0], resp[1]);

                        if (resp[0])
                        {
                            <?php if ($title == 'Giriş Yap') : ?>

                            var route = document.referrer != '' ? document.referrer : '/';

                            setTimeout(function() { location.href = route; }, 2000);

                            <?php elseif ($title == 'Parola Yenile') : ?>

                            setTimeout(function() { location.href = '/login'; }, 2000);

                            <?php endif ?>
                        }

                        form.find('*').removeAttr('disabled');

                        button.removeAttr('click');

                        button.find('div').hide();

                        button.find('span').show();
                    },
                    error: function(a, b, error)
                    {
                        result('error', error);

                        form.find('*').removeAttr('disabled');

                        button.find('div').hide();

                        button.find('span').show();
                    }
                });
            });

        </script>

        <div class="modal fade" id="result" tabindex="-1" role="dialog" 
            aria-labelledby="myCenterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <i id="result-icon" class="uil-icon-circle text-color display-3"></i>
                        <h4 class="text-danger mt-4" id="result-title">...</h4>
                        <p class="w-75 mx-auto text-muted mb-0" id="result-msg">...</p>
                        <div class="mt-4" id="result-buttons">
                            <a href="#" class="btn btn-outline-primary btn-rounded width-md"  data-dismiss="modal">
                                <i class="uil uil-arrow-left mr-1"></i> Geri
                            </a>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </body>
</html>