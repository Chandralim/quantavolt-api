@extends('user.layout.flat')


@section("e-content")

<div id="login" class="w100 h100 ez-bg-black ez-fs-small bg_one" style="position:relative; overflow:hidden;">
  <div class="h100 w100" style="height:50px; display:table; position:relative;">
    {{-- <a class="middle go_back" style="color:#fff; text-decoration:none; padding:0px 5px;">
      <i class="fas fa-angle-left"></i>
      <span class="title">
          BACK
      </span>
    </a> --}}
  </div>

  <div class="w100" style="height:calc(100% - 50px);  display:table; position:relative;">
    <span class="w100 h100" style="display:table-cell; vertical-align:middle">
      <div class="text-center" style="max-width:300px; margin:auto;">
        {{ csrf_field() }}
        <h4 class="text-center bold">Registrasi Berhasil</h4>

        <a href="/" class="ez-a-white">
          <span class="fez-enerzylogo" style="font-size:5em; text-shadow:1px 1px #000; color:white;"></span>
          {{-- <img src="/img/main/logo.png" alt="" style="width:75%;"> --}}
        </a>

        <div class="w100">
          <p>
            Terima kasih, alamat email anda telah terverifikasi.
          </p>
          <a class="ez-btn-white" href="{{url('/login')}}">
            Silahkan login kembali
          </a>
        </div>

      </div>
    </span>
  </div>
</div>

@endsection
