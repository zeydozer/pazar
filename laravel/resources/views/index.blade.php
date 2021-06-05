<?php if (!Cookie::has('user')) : $user = new \App\Models\User; ?>

<script>location.href = '/login'</script>

<?php else : $user = json_decode(Cookie::get('user')); endif; ?>

<!DOCTYPE html>
<html lang="tr">
    <head>

        <meta charset="utf-8" />

        <title>Noone | Pazaryeri Entegrasyon @hasSection('title') - @yield('title') @endif</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Pazaryeri Entegrasyonu" name="description" />
        <meta content="Zeyd Özer" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="/assets/images/favicon.png">

        @hasSection('style') @yield('style') @endif

        <!-- App css -->
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />

        <style>

            ::-webkit-scrollbar
            {
                width: 5px;
                height: 5px;
            }

            ::-webkit-scrollbar-track
            {
                background: #eee;
            }

            ::-webkit-scrollbar-thumb
            {
                background: #222831;
            }

            ::-webkit-scrollbar-thumb:hover
            {
                background: #00adb5;
                cursor: pointer;
            }

            .left-side-menu-condensed .left-side-menu
            {
                z-index: 100;
            }

            .left-side-menu-condensed .navbar-custom
            {
                padding-right: 10px;
                z-index: 101;
            }

            .nav-user,
            .left-side-menu-condensed .user-profile
            {
                padding-right: 0 !important;
            }

            .profile-dropdown
            {
                display: list-item;
            }

            .left-side-menu-condensed .user-profile .pro-user-desc,
            .left-side-menu-condensed .user-profile .pro-user-name
            {
                display: block;
            }

            [type="file"].form-control
            {
                height: calc(1.5em + 1rem + 8px);
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* #content img
            {
                max-width: 100%;
                max-height: 200px;
                object-fit: contain;
                object-position: left;
            } */

            #content .spinner-grow
            {
                width: 21px;
                height: 21px;
            }

            #result-msg
            {
                overflow: hidden;
                text-overflow: ellipsis;
            }

        </style>

        <script src="//kit.fontawesome.com/579b8cfb67.js" crossorigin="anonymous"></script>

    </head>
    <body class="left-side-menu-condensed" data-left-keep-condensed="true">

        <!-- Pre-loader -->
        <div id="preloader">
            <div id="status">
                <div class="spinner">
                    <div class="circle1"></div>
                    <div class="circle2"></div>
                    <div class="circle3"></div>
                </div>
            </div>
        </div>
        <!-- End Preloader-->

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Topbar Start -->
            <div class="navbar navbar-expand flex-column flex-md-row navbar-custom">
                <div class="container-fluid">
                    <!-- LOGO -->
                    <a href="/" class="navbar-brand mr-0 mr-md-2 logo">
                        <span class="logo-lg">
                            <img src="/assets/images/logo.png" alt="" height="24" />
                            <!-- <span class="d-inline h5 ml-1 text-logo">Ruberu</span> -->
                        </span>
                        <span class="logo-sm">
                            <img src="/assets/images/logo.png" alt="" height="24">
                        </span>
                    </a>

                    <ul class="navbar-nav bd-navbar-nav flex-row list-unstyled menu-left mb-0">
                        <li class="">
                            <button class="button-menu-mobile open-left disable-btn">
                                <i data-feather="menu" class="menu-icon"></i>
                                <i data-feather="x" class="close-icon"></i>
                            </button>
                        </li>
                    </ul>

                    <ul class="navbar-nav flex-row ml-auto d-flex list-unstyled topnav-menu float-right mb-0">
                        <li class="dropdown notification-list align-self-center profile-dropdown">
                            <a class="nav-link dropdown-toggle nav-user mr-0" data-toggle="dropdown" href="#" role="button"
                                aria-haspopup="false" aria-expanded="false">
                                <div class="media user-profile ">
                                    <img src="/assets/images/users/avatar-7.jpg" alt="user-image" class="rounded-circle align-self-center" />
                                    <div class="media-body text-left">
                                        <h6 class="pro-user-name ml-2 my-0">
                                            <span>{{ $user->name }}</span>
                                            <span class="pro-user-desc text-muted d-block mt-1">{{ $user->degree }}</span>
                                        </h6>
                                    </div>
                                    <span data-feather="chevron-down" class="ml-2 align-self-center"></span>
                                </div>
                            </a>
                            <div class="dropdown-menu profile-dropdown-items dropdown-menu-right">
                                <a href="/account" class="dropdown-item notify-item">
                                    <i data-feather="user" class="icon-dual icon-xs mr-2"></i>
                                    <span>Hesabım</span>
                                </a>

                                <!-- <a href="/account/setting" class="dropdown-item notify-item">
                                    <i data-feather="settings" class="icon-dual icon-xs mr-2"></i>
                                    <span>Ayarlar</span>
                                </a> -->

                                <div class="dropdown-divider"></div>

                                <a href="/logout" class="dropdown-item notify-item">
                                    <i data-feather="log-out" class="icon-dual icon-xs mr-2"></i>
                                    <span>Çıkış</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
            <!-- end Topbar -->

            <!-- ========== Left Sidebar Start ========== -->
            <div class="left-side-menu">
                <div class="sidebar-content">
                    <!--- Sidemenu -->
                    <div id="sidebar-menu" class="slimscroll-menu">
                        <ul class="metismenu" id="menu-bar">
                            <!-- <li>
                                <a href="/siparis">
                                    <i data-feather="list"></i>
                                    <span class="badge badge-success float-right">1</span>
                                    <span> Sipariş </span>
                                </a>
                            </li> -->
                            <li>
                                <a href="javascript:void(0)">
                                    <i data-feather="box"></i>
                                    <span>Products</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="/urunler">System</a></li>
                                    <li><a href="/urunler/trendyol">Trendyol</a></li>
                                    <li><a href="/urunler/ciceksepeti">Çiçeksepeti</a></li>
                                    <li><a href="/urunler/hepsiburada">Hepsiburada</a></li>
                                    <li><a href="/urunler/gittigidiyor">Gittigidiyor</a></li>
                                    <li><a href="/urunler/n11">N11</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="/urun/fiyat">
                                    <i data-feather="credit-card"></i>
                                    <span>Prices</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i data-feather="shopping-cart"></i>
                                    <span>Orders</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="/siparisler">System</a></li>
                                    <li><a href="/siparisler/trendyol">Trendyol</a></li>
                                    <li><a href="/siparisler/hepsiburada">Hepsiburada</a></li>
                                    <li><a href="/siparisler/gittigidiyor">Gittigidiyor</a></li>
                                    <li><a href="/siparisler/n11">N11</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i data-feather="rotate-ccw"></i>
                                    <span>Return</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="/iadeler">System</a></li>
                                    <li><a href="/iadeler/trendyol">Trendyol</a></li>
                                    <li><a href="/iadeler/hepsiburada">Hepsiburada</a></li>
                                    <li><a href="/iadeler/gittigidiyor">Gittigidiyor</a></li>
                                    <li><a href="/iadeler/n11">N11</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="/urun/kategori">
                                    <i data-feather="tag"></i>
                                    <span>Category</span>
                                </a>
                            </li>
                            <li>
                                <a href="/urun/ozellik">
                                    <i data-feather="filter"></i>
                                    <span>Features</span>
                                </a>
                            </li>
                            <li>
                                <a href="/urun/marka">
                                    <i data-feather="globe"></i>
                                    <span>Brands</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i data-feather="book"></i>
                                    <span>Accounting</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="/hesaplar">System</a></li>
                                    <li><a href="/hesaplar/trendyol">Trendyol</a></li>
                                    <li><a href="/hesaplar/hepsiburada">Hepsiburada</a></li>
                                    <li><a href="/hesaplar/gittigidiyor">Gittigidiyor</a></li>
                                    <li><a href="/hesaplar/n11">N11</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="/ayar/1">
                                    <i data-feather="settings"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- End Sidebar -->

                    <div class="clearfix"></div>
                </div>
                <!-- Sidebar -left -->

            </div>
            <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                    @yield('content')
                </div> <!-- content -->

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                {{ date('Y') }} &copy; Ruberu | <a href="#">Zeyd Özer</a>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->
            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="/assets/js/vendor.min.js"></script>

        @hasSection('script') @yield('script') @endif

        <!-- App js -->
        <script src="/assets/js/app.min.js"></script>

        <script>

            $('body').css('min-height', $(window).height());

            $('.content-page').css('min-height', $(window).height() - $('.navbar-custom').height() - 60);

            function result(icon, msg)
            {
                var icons =
                {
                    true: ['check', 'success', '<span class="text-success">Başarılı</span>'],
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

            $('#content [type="submit"]').click(function()
            {
                $(this).attr('click', true);
            });

            $('#content').submit(function(e)
            {
                e.preventDefault();

                var form = $(this), disabled = [];

                $.each(form.find('[disabled]'), function(i)
                {
                    disabled[i] = $(this);
                });

                form.find('*').attr('disabled', false);

                var data = new FormData(this),

                    button = $(this).find('[click]');

                /* $.each(form.find('[required]'), function(i)
                {
                    var index = $(this).closest('.form-group').find('.form-control').index(this);

                        name = $(this).closest('.form-group').find('label').eq(index).html();

                    data.append('label['+ $(this).attr('name') +']', name);
                }); */

                data.append('_token', '{{ csrf_token() }}');

                data.append('task', $.trim(button.find('span:eq(1)').text()).toLowerCase());

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

                            setTimeout(function() { location.href = resp[2]; }, 2000);

                        form.find('*').removeAttr('disabled');

                        $.each(disabled, function()
                        {
                            $(this).attr('disabled', true);
                        });

                        button.removeAttr('click');

                        button.find('div').hide();

                        button.find('span').show();
                    },
                    error: function(a, b, error)
                    {
                        result('error', error);

                        form.find('*').removeAttr('disabled');

                        $.each(disabled, function()
                        {
                            $(this).attr('disabled', true);
                        });

                        button.find('div').hide();

                        button.find('span').show();
                    }
                });
            });

            $('#content').find('[required]').each(function()
            {
                var index = $(this).closest('.form-group').find('.form-control').index(this);

                    label = $(this).closest('.form-group').find('label').eq(index);

                label.html(label.html() +' *');
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
