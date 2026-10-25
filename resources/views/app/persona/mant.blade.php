<div id="divMensajeError{!! $entidad !!}"></div>
<div id="my-div-errors" class="hidden"><h5 class="text-center p-0 m-0"><span class="badge badge-danger"></span></h5></div>
{!! Form::model($persona, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-row">
	<div class="form-group col-6  col-sm-6">
		{!! Form::label('mostrardni', 'DNI:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'margin-bottom:0px;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('mostrardni',["SI"=>"SI","NO"=>"NO"], null, array('class' => 'form-control input-xs', 'id' => 'mostrardni','onchange'=>'mostrarDNI();')) !!}
		</div>
	</div>
</div>
<div class="form-row" id="divDoc">
	<div class="form-group col-5 col-sm-3">
		{!! Form::label('dni', 'Ingrese:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('dni', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'dni2')) !!}
		</div>
	</div>
	<div class="form-group col-3 col-sm-5 d-flex justify-content-around">
		{!! Form::button('<span class="material-icons">cached</span> Consultar', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btn-consultar')) !!}
		{!! Form::button('<span class="material-icons">delete</span>', array('class' => 'btn btn-danger p-2 pl-1 pr-1', 'id' => 'btn-clear')) !!}
	</div>
	<div class="form-group col-5 col-sm-3">
		{!! Form::label('otros', 'Extranjero:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('otros', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'otros')) !!}
		</div>
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
	<div class="form-group col-12  col-sm-12">
		{!! Form::label('contratista', 'Empresa:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('contratista', null, array('class' => 'form-control input-xs js-contratista', 'id' => 'contratista')) !!}
			<small id="autoComplete_list2" class="text-danger"></small>
			{!! Form::hidden('contratista_id', null, array('id' => 'contratista_id')) !!}
			{!! Form::hidden('empresa_id', null, array('id' => 'empresa_id')) !!}
		</div>
	</div>
</div>
<div class="form-row">
	<div class="form-group col-6  col-sm-6">
		{!! Form::label('correo', 'Email:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::email('correo', null, array('class' => 'form-control input-xs', 'id' => 'correo')) !!}
		</div>
	</div>
	<div class="form-group col-6  col-sm-6">
		{!! Form::label('ua_id', 'Ua :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="u-ua-style js-ua-desc">
				<?php if($persona && !is_null($persona->ua)) echo $persona ->ua->descripcion; ?>
			</div>
			<input type="text" name="ua_id" id="ua_id" class="form-control js-ua-id input-xs" 
			value="@if($persona && !is_null($persona->ua)){{$persona->ua->codigo}}@endif">
			<small id="autoComplete_list1" class="text-danger"></small>
		</div>
	</div>
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarPersona(\''.$entidad.'\', this)')) !!}
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
	.solo-lectura:read-only {
		background-color: transparent;
		cursor: not-allowed;
	}
	.hidden {
		display: none;
	}
	
	.dt-row-bordered-selectable{
		border: 2px solid blueviolet !important;
		cursor: pointer;
	}
	.dt-row-bordered{
		border: 2px solid blueviolet !important;
	}
	.showTable{
		display: block !important;
	}
	.data-table{
		float: left;
		position: absolute;
		display: none;
		z-index: 1000;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('700');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		//inputs
		//inputs
		const myDivErrors = document.getElementById('my-div-errors');
		const btnConsultar = document.getElementById('btn-consultar');
		const btnClear = document.getElementById('btn-clear');

		const inputDni = document.getElementById('dni2');
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
			const dni = document.getElementById('dni2').value.trim();
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

		const doSearchContratista = () => {

		    var autoCompletejsContratista = new autoComplete({
		        data: {
		            src: async () => {
		                // Loading placeholder text
		                document
		                    .querySelector(".js-contratista")
		                    .setAttribute("placeholder", "Loading...");
		       
		                // Fetch External Data Source
		                const headers = new Headers();
		                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
		                const config = {
		                    headers,
		                    method:'GET'
		                };
		                  //Query
		                const query = document.querySelector('.js-contratista').value;
		                const source = await fetch(
		                    `control/search/contratista/${query}` , config
		                );
		                let data = await source.json();
		                // Post loading placeholder text
		                // Returns Fetched data
		                return data;
		            },
		            key: ["descripcion"],
		            cache: false
		        },
		        sort: (a, b) => {
		            if (a.match < b.match) return -1;
		            if (a.match > b.match) return 1;
		            return 0;
		        },
		        selector: ".js-contratista",
		        threshold: 1,
		        debounce: 0,
		        searchEngine: "strict",
		        highlight: true,
		        maxResults: 5,
		        resultsList: {
		            render: true,
		            container: source => {
		                source.setAttribute("id", "autoComplete_list");
		                source.setAttribute("class", "u-search-ua__result");
		            },
		            destination: document.querySelector(".js-contratista"),
		            position: "afterend",
		            element: "ul"
		        },
		        resultItem: {
		            content: (data, source) => {
		                source.innerHTML = data.match;
		            },
		            element: "li"
		        },
		        noResults: () => {
		            document.querySelector("#autoComplete_list2").innerText = 'Sin resultados';
		            $('#contratista_id').val("0");
		            $('#empresa_id').val("0");
		        },
		        onSelection: feedback => { //VA NUESTRA LOGICA
		            document.querySelector("#autoComplete_list2").innerText = '';
		            const selection = feedback.selection.value;
		            document.querySelector(".js-contratista").value = selection.razonsocial;
		            document.querySelector("#contratista_id").value = selection.contratista_id;
		            document.querySelector("#empresa_id").value = selection.empresa_id;
		            // console.log(feedback);
		        }
		    });
		    
		    // Toggle event for search input
		    // showing & hidding results list onfocus / blur
		    ["focus", "blur"].forEach(function(eventType) {
		      const resultsList = document.querySelector("#autoComplete_list2");
		    
		      document.querySelector(".js-contratista").addEventListener(eventType, function() {
		        // Hide results list & show other elemennts
		        if (eventType === "blur") {
		          resultsList.style.display = "none";
		        } else if (eventType === "focus") {
		          // Show results list & hide other elemennts
		          resultsList.style.display = "block";
		        }
		      });
		    });
		};
		doSearchContratista();
		doSearchUA();

	});
	var contadorEnvio=0;
	function guardarPersona (entidad, idboton) {
		if(contadorEnvio>0){
			alert('Estamos guardando la información, espere por favor...');
			return false;
		}
		contadorEnvio=1;
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var data         = submitForm(idformulario);
		var respuesta    = '';
		var listar       = 'NO';
		if ($(idformulario + ' :input[id = "listar"]').length) {
			var listar = $(idformulario + ' :input[id = "listar"]').val()
		};
		var btn = $(idboton);
		btn.button('loading');
		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			btn.button('reset');
			if(respuesta === 'ERROR'){
			}else{
				if (respuesta === 'OK') {
					md.showNotification('top','right','Guardado correctamente','info');
					contadorEnvio=0;
					cerrarModal();
					if (listar === 'SI') {
						if(typeof entidad2 != 'undefined' && entidad2 !== ''){
							entidad = entidad2;
						}
						buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
					}        
				} else {
					contadorEnvio=0;
					mostrarErrores(respuesta, idformulario, entidad);
				}
			}
		});
	} 

	function mostrarDNI(){
		if($("#mostrardni").val()=="SI"){
			$("#divDoc").removeClass("hidden");
		}else{
			$("#divDoc").addClass("hidden");
		}
	}
</script>