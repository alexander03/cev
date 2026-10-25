@php
		$readOnly = false;
@endphp
@if ($conductor != null)
	@php
		$readOnly = true;
	@endphp
@endif
<div id="divMensajeError{!! $entidad !!}"></div>
<div id="my-div-errors" class="hidden"><h5 class="text-center p-0 m-0"><span class="badge badge-danger"></span></h5></div>
{!! Form::model($conductor, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-row">
	<div class="form-group col-5 col-sm-3">
		{!! Form::label('dni', 'DNI:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('dni', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'dni', 'readonly' => $readOnly)) !!}
		</div>
	</div>
	<div class="form-group col-3 col-sm-5 d-flex justify-content-around">
		{!! Form::button('<span class="material-icons">cached</span> Consultar', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btn-consult')) !!}
		{!! Form::button('<span class="material-icons">delete</span>', array('class' => 'btn btn-danger p-2 pl-1 pr-1', 'id' => 'btn-clear')) !!}
	</div>
</div>
<div class="form-row">
	<div class="form-group col-6  col-sm-7">
		{!! Form::label('apellidos', 'Apellidos:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('apellidos', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'apellidos')) !!}
		</div>
	</div>
	<div class="form-group col-6  col-sm-5">
		{!! Form::label('nombres', 'Nombres:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('nombres', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'nombres')) !!}
		</div>
	</div>
</div>
<div class="form-row">
	<div class="form-group col-12  col-sm-5">
		{!! Form::label('licencia_letra', 'Licencia:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="form-row col-sm-12">
			<div class="col-3 col-sm-3">
				{!! Form::text('licencia_letra', $licenciaLetra, array('class' => 'form-control input-xs', 'id' => 'licencia_letra')) !!}
			</div>
			<div class="col-9 col-sm-9">
				{!! Form::text('dni', null, array('class' => 'pt-1 pb-1', 'id' => 'licencia_num', 'disabled' => 'true', 'style' => 'background-color: transparent;border:none')) !!}
			</div>
		</div>
	</div>
	<div class="form-group col-6  col-sm-4">
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('categoria', $arrCategorias, null, array('class' => 'form-control input-xs', 'id' => 'categoria')) !!}
		</div>
	</div>
	<div class="form-group col-6  col-sm-3">
		{!! Form::label('fechavencimiento', 'F. Vencimiento:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fechavencimiento', null, array('class' => 'form-control input-xs', 'id' => 'fechavencimiento', 'min' => date('Y-m-d') )) !!}
		</div>
	</div>
</div>
<div class="form-row">
	<div class="form-group col-8  col-sm-8">
		{!! Form::label('contratista_id', 'Contratista:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('contratista_id', $cboContratista, null, array('class' => 'form-control input-xs', 'id' => 'contratista_id')) !!}
		</div>
	</div>
	<div class="form-group col-4  col-sm-4">
		{!! Form::label('lider_id', 'Lider:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('lider_id', $cboLider, null, array('class' => 'form-control input-xs', 'id' => 'lider_id')) !!}
		</div>
	</div>
</div>
<div class="form-row">
	<div class="form-group col-4  col-sm-4 ">
		{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-xs', 'id' => 'tipo')) !!}
		</div>
	</div>
</div>
@if ($readOnly == false)
	<div class="form-row">
		<div class="form-group col-6  col-sm-4">
			{!! Form::label('imagenfirma', 'Imágen de la firma:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		</div>
		<div class="col-sm-8 d-flex align-items-end">
			{!! Form::file('imagenfirma', ['class' => 'form-control mb-2', 'accept' => 'image/png, image/jpeg','id' => 'imagenfirma']) !!}
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-6  col-sm-4">
			{!! Form::label('conformidadfirma', 'Doc. de conformidad:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		</div>
		<div class="col-sm-8 d-flex align-items-end">
			{!! Form::file('conformidadfirma', ['class' => 'form-control mb-2', 'accept' => '.pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document','id' => 'conformidadfirma']) !!}
		</div>
	</div>
	
	<hr style="margin: 0">
	<div class="form-row">
		<div class="form-group col-6  col-sm-6">
			{!! Form::label('username', 'Usuario:', array('class' => 'col-lg-12 col-md-12 col-sm-12')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
				{!! Form::text('username', $username_conductor, array('class' => '', 'id' => 'username')) !!}
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
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<input id="conductor" type="text" class="hidden" value="{{$conductor}}">
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
		let conductor = {};
		if(document.getElementById('conductor').value) {
			conductor = JSON.parse(document.getElementById('conductor').value);
		}
		const inputDni = document.getElementById('dni');
		const inputApe = document.getElementById('apellidos');
		const inputNom = document.getElementById('nombres');
		const inputLicenciaLetra = document.getElementById('licencia_letra');
		const inputLicenciaNum = document.getElementById('licencia_num');

		btnConsultar.addEventListener('click', async (e) => {
			const dni = document.getElementById('dni').value.trim();
			const iconLoader = e.target.children[0];
			iconLoader.classList.add('fa-spin');
			try {
				if(dni.length == 8) {
					myDivErrors.classList.add('hidden');
					let person = await consultDB(dni);
					if(person) { //Persona existe en la DB
						if(conductor) { //EDITAR
							if(person.dni != conductor.dni) {
								//La personaDB es otro conductor. No es el inicial
								showErrors('El conductor ya está registrado');
							}
						} else {//CREAR
							showErrors('El conductor ya está registrado');
						}
					}else { 
						person = await consultReniec(dni);
					}
					if(person) {
						inputApe.value = person.apepat+" "+person.apemat;
						inputNom.value = person.nombres;
						inputDni.setAttribute('readonly', true);
						inputLicenciaNum.value = person.dni;
					}else {
						showErrors('No existe este DNI');
						inputDni.removeAttribute('readonly');
						inputDni.focus();
					}
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
			inputLicenciaNum.value = '';
			myDivErrors.classList.add('hidden');
		})

		inputDni.addEventListener('change', e => {
			if(isNaN(e.target.value)) e.target.value = '';
		})

		inputDni.addEventListener('keydown', e => {
			// console.log(e.keyCode)
			if(e.target.value.length > 8 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39) e.preventDefault();
			if(e.keyCode < 8 || (e.keyCode >9 && e.keyCode< 37) || (e.keyCode >37 && e.keyCode< 39) || 
					(e.keyCode >39 && e.keyCode< 48) || (e.keyCode >57 && e.keyCode< 67) || (e.keyCode >67 && e.keyCode< 86) || (e.keyCode >86 && e.keyCode< 96) || e.keyCode> 105) e.preventDefault();
		})

		inputLicenciaLetra.addEventListener('keydown', e => {
			// console.log(e.keyCode)
			if(e.target.value.length > 0 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39) e.preventDefault();
			if(!(e.keyCode < 9 || (e.keyCode >9 && e.keyCode< 32) || (e.keyCode >32 && e.keyCode< 48)|| (e.keyCode >57 && e.keyCode< 96) || e.keyCode> 105)) e.preventDefault();
		})

		inputLicenciaLetra.addEventListener('keyup', e => {
			const regEx = /^[A-Z]{1}$/ig;
			// console.log(regEx.test(e.target.value))
			e.target.value = e.target.value.toUpperCase();
		})

		//Funciones
		const consultDB = (dni) => {
			const uriConsult = './existeconductor?dni=' + dni;
			return fetch(uriConsult)
			.then(res => res.status === 200 ? res.json() : console.error(`Error al cosultar Conductor en la db: ${res.status}`))
			.then(res => res[0])
		}

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
</script>