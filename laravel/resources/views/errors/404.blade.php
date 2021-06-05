@extends('errors.index')

@section('title', 404)

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-4 col-lg-5 col-8">
        <div class="text-center">
            
            <div>
                <img src="/assets/images/not-found.png" alt="" class="img-fluid" />
            </div>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-12 text-center">
        <h3 class="mt-3">Noktaları birleştiremedik.</h3>
        <p class="text-muted mb-5">Bu sayfa bulunamadı. <br/> Adresi yanlış yazmış olabilirsiniz veya sayfa taşınmış olabilir.</p>

        <a onclick="location.href = document.referrer != '' ? document.referrer : '/'" 
            class="btn btn-lg btn-primary mt-4" href="#">Geri Dön</a>
    </div>
</div>

@endsection