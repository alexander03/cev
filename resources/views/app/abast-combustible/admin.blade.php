<div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-info">
              <h4 class="card-title ">{{ $title }}</h4>
            </div>
            <div class="card-body">
                  {!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'row align-items-center', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
                  {!! Form::hidden('page', 1, array('id' => 'page')) !!}
                  {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
                  <div class="col-12 col-sm-2">
                        <label for="id-date">Fecha Inicial</label>
                        <input type="date" name="date_abastecimiento" class="form-control" id="id-date" value="<?=date("Y-m-")."01"?>">
                  </div>
                  <div class="col-12 col-sm-2">
                        <label for="id-date">Fecha Final</label>
                        <input type="date" name="date_abastecimiento2" class="form-control" id="id-date2" value="<?=date("Y-m-d")?>">
                  </div>
                  <div class="col-12 col-sm-2">
                        <label for="id-ua">Codigo UA</label>
                        <input type="text" name="codigo_ua" class="form-control" id="id-ua">
                  </div>
                  <div class="col-12 col-sm-2">
                        <label for="id-grifo">Grifo</label>
                        {!! Form::select('grifo',$cboGrifo, null, array('class' => 'form-control input-xs', 'id' => 'id-grifo')) !!}
                  </div>
                  <div class="col-12 col-sm-2">
                        <label for="id-placa">Placa</label>
                        <input type="text" name="placa" class="form-control" id="id-placa">
                  </div>
                  <div class="col-12 col-sm-2">
                      {!! Form::label('filas', 'Filas a mostrar:')!!}
                      {!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                  </div>
                  <div class="col-12 col-sm-2">
                    {!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
                  </div>
                  <div class="col-12 col-sm-12 d-flex justify-content-center">
                      {!! Form::button('<i class="material-icons">add</i>Nuevo', array('class' => 'btn btn-info btn-sm', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
                      <a href="#" onclick="window.open('{{ route('abastecimiento.excel.export') }}?fechainicio='+$('#id-date').val()+'&fechafin='+$('#id-date2').val(),'_blank');" class="btn btn-sm btn-primary">
                          <i class="material-icons">cloud_download</i> Exportar
                      </a>

                      <a href="{{ route('abastecimiento.excel.export') }}" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#formExContrDia">
                          <i class="material-icons">cloud_download</i> Exportar Control Diario
                      </a>
                      
                      {!! Form::button('<i class="material-icons">cloud_download</i> Reporte Combustible', array('class' => 'btn btn-primary btn-sm', 'id' => 'btnGenerateReporte', 'onclick' => 'modal (\''.URL::route($ruta["generateReport"], array('listar'=>'NO')).'\', \''.$titulo_generar.'\', this);')) !!}
                  </div>
                  {!! Form::close() !!}
                  <div class="table-responsive" id="listado{{ $entidad }}">
                  </div>
              </div>
          </div>
      </div>
  </div>


  <div class="modal fade" id="formExContrDia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Datos para Control Diario</h5>
        </div>
        <div class="modal-body">
          {!! Form::open(['route' => 'abastecimiento.controldiario', 'method' => 'POST' , 'class' => 'row', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'GReportControlDiarioCombustible','target' => '_blank']) !!}
             <div class="col-12 col-sm-12">
                <label >Fecha de abast.</label>
                <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" >
              </div>
              <div class="col-12 col-sm-12">
                <label>Grifo</label>
                <select name='grifo' class="form-control js-select">
                  <option selected value="">Seleccione grifo</option>
                  @foreach($cboGrifos as $value)
                    <option value="{{$value->id}}">{{ $value->descripcion }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-sm-12">
                <label>Tipo Combustible</label>
                <select name='tipocombustible' class="form-control js-select">
                  <option selected value="">TODOS</option>
                  @foreach($cboTipocombustible as $value)
                    <option value="{{$value->id}}">{{ $value->descripcion }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-check col-12 col-sm-12 pl-4 mt-2">
                <input id="ck-excel" type="radio" name="tipo" value="1" checked>
                <label class="form-check-label" for="ck-excel">
                  EXCEL
                </label>
              </div>
              <div class="form-check col-12 col-sm-12 pl-4">
                <input id="ck-pdf" type="radio" name="tipo" value="2">
                <label class="form-check-label" for="ck-pdf">
                  PDF
                </label>
              </div>
                          
            	<div class="col-12 form-group input-group">
        			{!! Form::label('concesionaria', 'Concesionaria: ', array('class' => 'text-right')) !!}
        			<select name='concesionaria[]' class="form-control js-select" multiple="" style="height:100px;">
                      @foreach($cboConcesionaria as $value)
                        <option value="{{$value->id}}" selected>{{ $value->razonsocial }}</option>
                      @endforeach
                    </select>
            	</div>
              <div class="form-group">
                <div class="col-lg-12 col-md-12 col-sm-12 text-right">
                  <button type="submit" class="btn btn-sm btn-success js-generate" disabled>Generar</button>
                  <button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
              </div> 
          
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>

  
  <script>
      $(document).ready(function () {
          buscar('{{ $entidad }}');
          init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
          $(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="descripcion"]').keyup(function (e) {
              var key = window.event ? e.keyCode : e.which;
              if (key == '13') {
                  buscar('{{ $entidad }}');
              }
          });
          document.querySelector('.js-select').addEventListener('change', ({ target }) => {
            if(target.value === '') document.querySelector('.js-generate').setAttribute('disabled', 'disabled');
            else document.querySelector('.js-generate').removeAttribute('disabled');
          });
      });
  </script>
  