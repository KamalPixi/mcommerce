<!DOCTYPE html>
<html lang="en">
<head>
  <!-- CSRF Token -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- meta include -->
  @stack('meta')

  <title>{{ config('app.name', 'E-commerce') }}</title>

  <!-- v2 -->
  <link rel="stylesheet" href="{{ asset('frontend/v2/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/v2/css/owl/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/v2/css/owl/owl.theme.default.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
  <link rel="stylesheet" href="{{ asset('frontend/v2/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/v2/css/responsive.css') }}">



  <!--to include custom css -->
  @stack('css')

</head>

<body>

  @yield('content')


  <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script> -->

  <div id="snackbar">Some text some message..</div>
  <!-- v2 -->
  <script src="{{ asset('frontend/v2/js/jQuery-v3.5.1.js') }}"></script>
  <script src="{{ asset('frontend/v2/js/lazy_load.min.js') }}"></script>
  <script src="{{ asset('frontend/v2/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('frontend/v2/js/popper.js') }}"></script>
  <script src="https://kit.fontawesome.com/902406204f.js" crossorigin="anonymous"></script>
  <script src="{{ asset('frontend/v2/js/owl/owl.carousel.min.js') }}"></script>
  <script src="https://connect.facebook.net/en_US/sdk.js"></script>

  <!-- v1 -->
  <!-- Custom Function JS -->
  <script src="{{ asset('frontend/js/notify.min.js') }}"></script>
  <!--to include custom js -->
  @stack('js')
  <script src="{{ asset('frontend/v2/js/func.js') }}"></script>
  <script src="{{ asset('frontend/v2/js/main.js') }}"></script>
</body>
</html>
