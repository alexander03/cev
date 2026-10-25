<?php
use App\Grupomenu;
use App\Opcionmenu;
use App\Http\Controllers\UserConcesionariaController;
?>
<div class="sidebar" data-color="orange" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
    <div class="logo"  style="z-index: 20">
    <a  class="simple-text logo-normal">
      <a class="simple-text logo-normal" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php
        $a= new UserConcesionariaController();
        $data = $a->actual(auth()->user()->id)->get();
        foreach($data as $k=>$v){
            echo $v->abreviatura;    
        }
        ?>
        
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <?php
      $data = $a->devolverConce(auth()->user()->id)->get();
      foreach ($data as $key => $val) {
        echo '<a class="dropdown-item" onclick="cambiar('.$val->id.')" >'.$val->razonsocial.'</a>';
      }
        ?>
      </div>
    </a>
  </div>
  <div class="sidebar-wrapper">
    <ul class="nav">
      <!--li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
          <i class="material-icons">dashboard</i>
            <p>{{ __('Dashboard') }}</p>
        </a>
      </li-->
      <?php
        $opciones_coleccion = Auth::user()->tipouser->opcionesmenu;
        $OpcionesFiltro = array();
        foreach($opciones_coleccion as $opcion){
          $OpcionesFiltro[] = [$opcion->id];
        };
        $data = Grupomenu::whereHas('opcionesmenu', function($query) use($OpcionesFiltro){
          $query->whereIn('id',$OpcionesFiltro);
        })->orderBy('orden','asc')->get();
        foreach ($data as $key => $val) {
          echo '<li class="nav-item">
                  <a class="nav-link" data-toggle="collapse" href="#'.$val->id.'">
                    <i class="material-icons">'.$val->icono.'</i>
                    <p>'.$val->descripcion.'<b class="caret"></b>
                    </p>
                  </a>
                  <div class="collapse" id="'.$val->id.'">
                    <ul class="nav">';  
          $data2 = $opciones_coleccion->where('grupomenu_id','=',$val->id);
          foreach($data2 as $k => $v){
            echo '<li class="nav-item" id="'.$v->link.'">
                    <a class="nav-link" onclick="cargarRuta(\''.URL::to($v->link).'\', \'container\',\''.$v->link.'\');">
                    <i class="material-icons">'.$v->icono.'</i>
                      <span class="sidebar-normal">'.$v->descripcion.'</span>
                    </a>
                  </li>';
          }
          echo '</ul>
              </div>
            </li>';
        }
      ?>
      <?php /*                        MENU LATERAL ORIGINAL            
      $data = Grupomenu::orderBy('orden','asc')->get();
      foreach ($data as $key => $val) {
        echo '<li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#'.$val->id.'">
                  <i class="material-icons">'.$val->icono.'</i>
                  <p>'.$val->descripcion.'<b class="caret"></b>
                  </p>
                </a>
                <div class="collapse" id="'.$val->id.'">
                  <ul class="nav">';  
        $data2 = Opcionmenu::where('grupomenu_id','=',$val->id)->get();
        foreach($data2 as $k => $v){
          echo '<li class="nav-item" id="'.$v->link.'">
                  <a class="nav-link" onclick="cargarRuta(\''.URL::to($v->link).'\', \'container\',\''.$v->link.'\');">
                  <i class="material-icons">'.$v->icono.'</i>
                    <span class="sidebar-normal">'.$v->descripcion.'</span>
                  </a>
                </li>';
        }
        echo '</ul>
            </div>
          </li>';
      }*/

      ?>
      <?php /*
      <li class="nav-item {{ ($activePage == 'profile' || $activePage == 'user-management') ? ' active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#laravelExample" aria-expanded="true">
          <i><img style="width:25px" src="{{ asset('material') }}/img/laravel.svg"></i>
          <p>{{ __('Laravel Examples') }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse show" id="laravelExample">
          <ul class="nav">
            <li class="nav-item{{ $activePage == 'profile' ? ' active' : '' }}">
              <a class="nav-link" href="{{ route('profile.edit') }}">
                <span class="sidebar-mini"> UP </span>
                <span class="sidebar-normal">{{ __('User profile') }} </span>
              </a>
            </li>
            <li class="nav-item{{ $activePage == 'user-management' ? ' active' : '' }}">
              <a class="nav-link" href="{{ route('user.index') }}">
                <span class="sidebar-mini"> UM </span>
                <span class="sidebar-normal"> {{ __('User Management') }} </span>
              </a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item{{ $activePage == 'table' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('table') }}">
          <i class="material-icons">content_paste</i>
            <p>{{ __('Table List') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'typography' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('typography') }}">
          <i class="material-icons">library_books</i>
            <p>{{ __('Typography') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'icons' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('icons') }}">
          <i class="material-icons">bubble_chart</i>
          <p>{{ __('Icons') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'map' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('map') }}">
          <i class="material-icons">location_ons</i>
            <p>{{ __('Maps') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'notifications' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('notifications') }}">
          <i class="material-icons">notifications</i>
          <p>{{ __('Notifications') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'language' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('language') }}">
          <i class="material-icons">language</i>
          <p>{{ __('RTL Support') }}</p>
        </a>
      </li>
      */
       ?>
    </ul>
  </div>
</div>
<script type="text/javascript">
  function cambiar($id){
    var serviceURL = "./userconcesionaria/concesionaria/"+$id;
    $.ajax({
        type: "GET",
        url: serviceURL,
        data: param = "",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: successFunc,
        error: errorFunc
    });
    
    function successFunc(data, status) {
        location.reload();
    }
    function errorFunc(data, status) {
        location.reload();
    }
  }

</script>
