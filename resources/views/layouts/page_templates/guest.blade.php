@include('layouts.navbars.navs.guest')
<div class="wrapper wrapper-full-page">
  <div class="login-page header-filter" style="background-image: url('{{ asset('material') }}/img/fondo-login.png'); background-size: cover; background-position: top center;align-items: center;min-height: 100vh;
  max-height: 1000px;
  display: flex !important;
  height: 100%;
  padding: 0;
  position: relative;" data-color="purple">
  <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
    @yield('content')
    @include('layouts.footers.guest')
  </div>
</div>
