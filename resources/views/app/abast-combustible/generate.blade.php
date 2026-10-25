<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open(array('url' => route('abastecimiento.exportExcelReport'), 'method' => 'GET', 'id' => 'form', 'target' => '_blank')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">

	{!! Form::label('fecha', 'Seleccione el rango del reporte', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}

	<div class="col-12 form-group input-group">
		<div class="col-lg-6 col-md-6 col-sm-12 p-0 m-0 input-group">
			{!! Form::label('fecha', 'Fecha inicial: ', array('class' => 'mt-3 mb-2 col-lg-3 col-md-3 col-sm-6 control-label text-right')) !!}
			<input type="date" name="start_date" id='start_date' value="{{ date("Y-m-")."01" }}" class="form-control input-xs">
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 p-0 m-0 input-group">
			{!! Form::label('fecha', 'Fecha final: ', array('class' => 'mt-3 mb-2 col-lg-3 col-md-3 col-sm-6 control-label text-right')) !!}
			<input type="date" name="end_date" id='end_date' class="form-control input-xs">
		</div>
	</div>
	<div class="col-12 form-group input-group">
		<div class="col-lg-6 col-md-6 col-sm-12 p-0 m-0 input-group" style="display: none;">
			{!! Form::label('preciocombustible', 'Precio Combustible(Lt): ', array('class' => 'mt-3 mb-2 col-lg-5 col-md-5 col-sm-6 control-label text-right')) !!}
			<input type="number" name="preciocombustible" id='preciocombustible' value="0" class="form-control input-xs">
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 p-0 m-0 input-group">
			{!! Form::label('grifo', 'Grifo: ', array('class' => 'mt-3 mb-2 col-lg-3 col-md-3 col-sm-6 control-label text-right')) !!}
			<select name='grifo[]' class="form-control js-select" multiple="" style="height:150px;">
              @foreach($cboGrifo as $value)
                <option value="{{$value->id}}" selected>{{ $value->descripcion }}</option>
              @endforeach
            </select>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 p-0 m-0 input-group">
			{!! Form::label('concesionaria', 'Concesionaria: ', array('class' => 'mt-3 mb-2 col-lg-3 col-md-3 col-sm-6 control-label text-right')) !!}
			<select name='concesionaria[]' class="form-control js-select" multiple="" style="height:150px;">
              @foreach($cboConcesionaria as $value)
                <option value="{{$value->id}}" selected>{{ $value->razonsocial }}</option>
              @endforeach
            </select>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 p-0 m-0 input-group">
			{!! Form::label('contratista', 'Subcontratista: ', array('class' => 'mt-3 mb-2 col-lg-3 col-md-3 col-sm-6 control-label text-right')) !!}
			<select name='contratista' class="form-control js-select">
				<option value="">TODOS</option>
              @foreach($cboContratista as $value)
                <option value="{{$value->id}}" >{{ $value->razonsocial }}</option>
              @endforeach
            </select>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 p-0 m-0 input-group">
			{!! Form::label('tipocombustible', 'Tipo Comb ', array('class' => 'mt-3 mb-2 col-lg-3 col-md-3 col-sm-6 control-label text-right')) !!}
			<select name='tipocombustible[]' class="form-control js-select" multiple="" style="height:110px;">
              @foreach($cboTipocombustible as $value)
                <option value="{{$value->id}}" selected>{{ $value->descripcion }}</option>
              @endforeach
            </select>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-12 p-0 m-0 input-group">
			<div class="form-check col-12 col-sm-12 pl-4 mt-2">
                <input id="ck-excel" type="radio" name="tipo" value="excel">
                <label class="form-check-label" for="ck-excel">EXCEL</label>
              </div>
        	<div class="form-check col-12 col-sm-12 pl-4">
            	<input id="ck-pdf" type="radio" name="tipo" value="pdf" checked>
            	<label class="form-check-label" for="ck-pdf">PDF</label>
          	</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 p-0 m-0 input-group">
			<label style="color:blue">(Sólo aplica Reporte Subcontratista/Descuento)</label>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{{-- <a href="{{ route('controldiario.exportExcelReport') }}" target="_blank" class="btn btn-sm btn-primary" title="Exportar">
			<i class="material-icons">cloud_download</i> Generar Reporte
		</a> --}}
		{!! Form::hidden('typeExport', NULL) !!}
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$btnPdf1, array('class' => 'btn btn-success btn-sm', 'id' => 'btnExportPdf1', 'type' => 'submit')) !!}
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$btnPdf2, array('class' => 'btn btn-success btn-sm', 'id' => 'btnExportPdf2', 'type' => 'submit')) !!}
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Rep Vehiculo', array('class' => 'btn btn-info btn-sm', 'id' => 'btnExportVehiculo', 'type' => 'submit')) !!}
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Rep Equipo', array('class' => 'btn btn-info btn-sm', 'id' => 'btnExportEquipo', 'type' => 'submit')) !!}
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Rep Descuento', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnExportDescuento', 'type' => 'submit')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div> 

{!! Form::close() !!}

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('1000');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

		// Administracion de las fechas
		const startDate = document.getElementById('start_date');
		const endDate = document.getElementById('end_date');

		const getCurrentDate = () => {
			const fecha = new Date();
			let mes = fecha.getMonth()+1;
			let dia = fecha.getDate();
			const ano = fecha.getFullYear();
			if(dia<10) dia = '0' + dia;
			if(mes<10) mes = '0' + mes;

			endDate.value = ano+"-"+mes+"-"+dia;
			startDate.setAttribute('max', ano+"-"+mes+"-"+dia);
		}
		getCurrentDate();

		// Tipo de reporte
		const form = Array.from([document.getElementById('form')])[0];
		//const btnExportExcel = Array.from([document.getElementById('btnExportExcel')])[0];
		const btnExportPdf1 = Array.from([document.getElementById('btnExportPdf1')])[0];
		const btnExportPdf2 = Array.from([document.getElementById('btnExportPdf2')])[0];
		const btnExportVehiculo = Array.from([document.getElementById('btnExportVehiculo')])[0];
		const btnExportEquipo = Array.from([document.getElementById('btnExportEquipo')])[0];
		const btnExportDescuento = Array.from([document.getElementById('btnExportDescuento')])[0];
		const inputTypeExport = Array.from(document.getElementsByName('typeExport'))[0];

		const saveTypeExport = (e, typeExport) => {
			e.preventDefault();
			inputTypeExport.value = typeExport;
			form.submit();
		}

		// Eventos del sistema
		endDate.addEventListener('change', (e) => {
			startDate.setAttribute('max', endDate.value);
			
			let start_date = new Date(startDate.value);
			let end_date = new Date(endDate.value);

			if ( start_date > end_date ) startDate.value = endDate.value;
		});
		//btnExportExcel.addEventListener('click', e => saveTypeExport(e, 'excel'));
		btnExportPdf1.addEventListener('click', e => saveTypeExport(e, 'subcontratista'));
		btnExportPdf2.addEventListener('click', e => saveTypeExport(e, 'combustible'));
		btnExportVehiculo.addEventListener('click', e => saveTypeExport(e, 'combustible-vehiculo'));
		btnExportEquipo.addEventListener('click', e => saveTypeExport(e, 'combustible-equipo'));
		btnExportDescuento.addEventListener('click', e => saveTypeExport(e, 'combustible-descuento'));

	});
</script>