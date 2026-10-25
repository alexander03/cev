<?php 
use App\Vehiculo;
use App\Concesionaria;
?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
  <div class="container-fluid">
    <div class="navbar-wrapper">
      <a class="navbar-brand" href="#">{{ $titlePage }}</a>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
    <span class="sr-only">Toggle navigation</span>
    <span class="navbar-toggler-icon icon-bar"></span>
    <span class="navbar-toggler-icon icon-bar"></span>
    <span class="navbar-toggler-icon icon-bar"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end">
      <!--form class="navbar-form">
        <div class="input-group no-border">
        <input type="text" value="" class="form-control" placeholder="Search...">
        <button type="submit" class="btn btn-white btn-round btn-just-icon">
          <i class="material-icons">search</i>
          <div class="ripple-container"></div>
        </button>
        </div>
      </form-->
      <?php
      //{{ route('profile.edit') }}
      ?>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="">
            <i class="material-icons">person_pin</i>
            <label>{{ Auth::user()->name }}</label>
          </a>
        </li>

        @if(Auth::user()->tipouser_id==1 || Auth::user()->tipouser_id==12 || Auth::user()->tipouser_id==3 || Auth::user()->tipouser_id==9)
        <li class="nav-item">
            <a class="nav-link "  id="buttonNotify2"  href="#"   aria-haspopup="true" aria-expanded="false" onclick="cargarRuta('{{ URL::to('reportkilometraje') }}', 'container','reportkilometraje');">
            <i class="material-icons">directions_car</i>
            <?php
            $ConcesionariaActual = Concesionaria::join('users','users.concesionaria_id','=','concesionaria.id')
            ->where('users.id','=',auth()->user()->id)
            ->select('concesionaria.id','concesionaria.razonsocial')->get();
            $idConcAct=$ConcesionariaActual[0]->id;
            $resultado        = Vehiculo::where('concesionaria_id', $idConcAct)
                                ->whereIn('contratista_id',[12,27,38]);
            $lista1            = $resultado->get();
            $c=0;
            foreach ($lista1 as $key1 => $value1){
              $recorrido = $value1->kilometraje_act - $value1->kilometraje_inicial;
      		    if( $recorrido < $value1->kilometraje2->limite_inf ){
      		        $color="";
      		        $background="";
      		    }elseif( $recorrido < $value1->kilometraje2->limite_sup ){
      		        $color="bg-warning";
      		        $background="";
      		    }else{
      		        $background="background:#f44336a8";
      		        $color="";
      		        $c=$c+1;
      		    }
      		  }
            ?>
            <span class="notification <?=($c==0)?"d-none":"-"?>"><?=$c?></span>
            <p class="d-lg-none d-md-block">
              {{ __('Some Actions') }}
            </p>
          </a>
        </li>
        <li class="nav-item dropdown" style="display: none;">
          <a class="nav-link "  data-toggle="dropdown" id="buttonNotify"  href="#pablo"   aria-haspopup="true" aria-expanded="false" onclick="ActivarNotificaciones(this,event)">
            <i class="material-icons">notifications</i>
            <span class="notification d-none">5</span>
            <p class="d-lg-none d-md-block">
              {{ __('Some Actions') }}
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="buttonNotify">
            <a class="dropdown-item p-0" >
              <div class="alert alert-info alert-with-icon " data-notify="container">
              <i class="material-icons" data-notify="icon">add_alert</i>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="material-icons">close</i>
              </button>
              <span data-notify="message">
                Vehículo: Modelo  - MARCA, placa PLACA
                <br>
                El documento DOCT , vence : FECHAAAAAAAA
              </span>
            </div>
            </a>


          </div>
        </li>
        @endif
        <li class="nav-item dropdown">
          <a class="nav-link" href="#" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="material-icons">person</i>
            <p class="d-lg-none d-md-block">
              {{ __('Account') }}
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
            <!--a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a>
            <div class="dropdown-divider"></div-->
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Cerrar sesión</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
