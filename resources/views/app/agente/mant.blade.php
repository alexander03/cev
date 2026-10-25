@php
		$readOnly = false;
@endphp
@if ($agente != null)
	@php
		$readOnly = true;
	@endphp
@endif
<div id="divMensajeError{!! $entidad !!}"></div>
<div id="my-div-errors" class="hidden"><h5 class="text-center p-0 m-0"><span class="badge badge-danger"></span></h5></div>
{!! Form::model($agente, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-row">
	<div class="form-group col-5 col-sm-3">
		{!! Form::label('dni', 'DNI:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('dni', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'dni')) !!}
		</div>
	</div>
	<div class="form-group col-3 col-sm-5 d-flex justify-content-around">
		{!! Form::button('<span class="material-icons">cached</span> Consultar', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btn-consult')) !!}
		{!! Form::button('<span class="material-icons">delete</span>', array('class' => 'btn btn-danger p-2 pl-1 pr-1', 'id' => 'btn-clear')) !!}
	</div>
</div>
<div class="form-row">
	<div class="form-group col-6  col-sm-6">
		{!! Form::label('apellidos', 'Apellidos:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('apellidos', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'apellidos')) !!}
		</div>
	</div>
	<div class="form-group col-6  col-sm-6">
		{!! Form::label('nombres', 'Nombres:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('nombres', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'nombres')) !!}
		</div>
	</div>
</div>
<div class="form-row">
	<div class="form-group col-6  col-sm-6">
		{!! Form::label('telefono', 'Telefono:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('telefono', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'telefono')) !!}
		</div>
	</div>
	@if ($agente != null)
	<div class="form-group col-6  col-sm-6">
		{!! Form::label('concesionaria_id', 'Concesionaria:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('concesionaria_id',$cboConcesionaria, null, array('class' => 'form-control input-xs', 'id' => 'concesionaria_id')) !!}
		</div>
	</div>
	@endif
</div>

@if ($readOnly == false)
	<hr style="margin: 0">
	<div class="form-row">
		<div class="form-group col-6  col-sm-6">
			{!! Form::label('username', 'Usuario:', array('class' => 'col-lg-12 col-md-12 col-sm-12')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
				{!! Form::text('username', null, array('class' => '', 'id' => 'username')) !!}
			</div>
		</div>
		<div class="form-group col-6  col-sm-6">
			{!! Form::label('password', 'Contraseña:', array('class' => 'col-lg-12 col-md-12 col-sm-12')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
				{!! Form::password('password', null, array('id' => 'password')) !!}
			</div>
		</div>
	</div>
@endif
<div class="form-group">
	{!! Form::label('garita_id', 'Garita:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::select('garita_id[]',$cboGarita, null, array('class' => 'form-control input-xs', 'id' => 'garita_id','multiple','style'=>'height:200px')) !!}
	</div>
</div>


<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<style>
	.solo-lectura:read-only {
		background-color: transparent;
		cursor: not-allowed;
	}
	.hidden {
		display: none;
	}
	.document__img-firma {
		font-size: 17px;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('700');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		//inputs
		const myDivErrors = document.getElementById('my-div-errors');
		const btnConsultar = document.getElementById('btn-consult');
		const btnClear = document.getElementById('btn-clear');

		const inputDni = document.getElementById('formMantenimientoAgente').elements['dni'];
		const inputApe = document.getElementById('apellidos');
		const inputNom = document.getElementById('nombres');
		

		inputDni.addEventListener('change', e => {
			if(isNaN(e.target.value)) e.target.value = '';
		})

		inputDni.addEventListener('keydown', e => {
			// console.log(e.keyCode)
			if(e.target.value.length > 8 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39) e.preventDefault();
			if(e.keyCode < 8 || (e.keyCode >9 && e.keyCode< 37) || (e.keyCode >37 && e.keyCode< 39) || 
					(e.keyCode >39 && e.keyCode< 48) || (e.keyCode >57 && e.keyCode< 67) || (e.keyCode >67 && e.keyCode< 86) || (e.keyCode >86 && e.keyCode< 96) || e.keyCode> 105) e.preventDefault();
		})


		btnConsultar.addEventListener('click', async (e) => {
			const dni = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val().trim();
			const iconLoader = e.target.children[0];
			iconLoader.classList.add('fa-spin');
			try {
				if(dni.length == 8) {
					myDivErrors.classList.add('hidden');
					let person = await consultReniec(dni);
					
					if(person) {
						inputApe.value = person.apepat+" "+person.apemat;
						inputNom.value = person.nombres;
						inputDni.setAttribute('readonly', true);
					}else {
						showErrors('No existe este DNI');
						inputDni.removeAttribute('readonly');
						inputDni.focus();
					}
				}else{
					console.log("No cumple,"+dni.length);
				}
			} catch (error) {
				console.log(error)
			}
			iconLoader.classList.remove('fa-spin');
		});

		btnClear.addEventListener('click', () => {
			inputDni.value = '';
			inputApe.value = '';
			inputNom.value = '';
			inputDni.removeAttribute('readonly');
			inputDni.focus();
			myDivErrors.classList.add('hidden');
		})

		const consultReniec = (dni) => {
			// const uriConsult = 'http://127.0.0.1:80/Reniec/consulta_reniec.php';
			//const uriConsult = './Reniec/consulta_reniec.php';
			const uriConsult = './Reniec/consultaReniec.php';
			return fetch(uriConsult,{
				method: 'POST',
				body: 'dni=' + dni,
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8;'
				}
			})
			.then(res => res.status === 200 ? res.json() : console.error(`Error al cosultar DNI en la db: ${res.status}`));
		}

		const showErrors = (msg) => {
			myDivErrors.children[0].children[0].textContent = msg;
			myDivErrors.classList.remove('hidden');
		}

	}); 

	<?php
	if(!is_null($agente) && $agente->garita_id!=""){
		echo '$("#garita_id").val('.$agente->garita_id.')';
	}
	?>
</script>