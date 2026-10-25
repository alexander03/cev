<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-info">
            <h4 class="card-title ">{{ $title }}</h4>
          </div>
          <div class="card-body">
			{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'row', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
			{!! Form::hidden('page', 1, array('id' => 'page')) !!}
			{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
			<div class="col-8 col-sm-9 col-md-2 col-lg-2">
				{!! Form::label('filter', 'Buscar por...') !!}
				{!! Form::text('filter', '', array('class' => 'form-control input-xs', 'id' => 'filter')) !!}
			</div>
			<div class="col-8 col-sm-9 col-md-2 col-lg-2">
				{!! Form::label('tipofecha', 'Tipo Fecha') !!}
				{!! Form::select('tipofecha',['fecharegistro'=>'Fecha Registro','fechamantenimiento'=>'Fecha Mant.'], '', array('class' => 'form-control input-xs', 'id' => 'tipofecha')) !!}
			</div>
			<div colspan='2' class="col-8 col-sm-9 col-md-2 col-lg-2">
				<tr>{!! Form::label('filter', 'Fecha Inicio') !!}
					<button style="float:right;" class="btn btn-dark p-1" onclick="document.getElementById('fechainicio').value=''" id="btn_remove_fecha_registro_inicial">
						<span class="material-icons outlined">close</span>
					</button>
				</tr>
				<input class="form-control input-xs" id="fechainicio" name="fechainicio" type="date" value="<?php  echo date('Y');echo'-';echo date('m');echo'-';echo'01'?>">
			</div>
			<div colspan='2' class="col-8 col-sm-9 col-md-2 col-lg-2">
				<tr>{!! Form::label('filter', 'Fecha Fin') !!}
					<button style="float:right;" class="btn btn-dark p-1" onclick="document.getElementById('fechafin').value=''" id="btn_remove_fecha_registro_inicial">
						<span class="material-icons outlined">close</span>
					</button>
				</tr>
				<input class="form-control input-xs" id="fechafin" name="fechafin" type="date" value="{{ date('Y-m-d') }}">
			</div>
			<div class="col-3 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('tipomantenimiento', 'Tipo Mant.')!!}
				{!! Form::select('tipomantenimiento',array(''=>'Todos','1'=>'Preventivo','2'=>'Correctivo'), null, array('class' => 'form-control input-xs', 'id' => 'tipomantenimiento', 'maxlength' => '100')) !!}
			</div>
			<div class="col-3 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('situacion', 'Situacion')!!}
				{!! Form::select('situacion',array(''=>'Todos','P'=>'Pendiente','F'=>'Finalizado'), null, array('class' => 'form-control input-xs', 'id' => 'situacion')) !!}
			</div>
			<div class="col-3 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('documentos', 'Documentos')!!}
				{!! Form::select('documentos',array(''=>'Todos','SI'=>'SI','NO'=>'NO'), null, array('class' => 'form-control input-xs', 'id' => 'documentos')) !!}
			</div>
			<div class="col-3 col-sm-2 col-md-2 col-lg-1">
				{!! Form::label('filas', 'Filas')!!}
				{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
			</div>
			<div class="col-8 col-sm-9 col-md-2 col-lg-2">
				{!! Form::label('precio', 'P. Comb.') !!}
				{!! Form::text('precio', '', array('class' => 'form-control input-xs', 'id' => 'precio')) !!}
			</div>
			<div class="col-8 col-sm-9 col-md-6 col-lg-6">
				{!! Form::label('repuesto', 'Repuesto x Mant.') !!}
				{!! Form::text('repuesto', '', array('class' => 'form-control input-xs js-repuesto', 'id' => 'repuesto')) !!}
				<small id="autoComplete_listRepuesto" class="text-danger"></small>
			</div>
			<div class="form-group">
				{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				<button class = "btn p-2 pl-1 pr-1 btn-primary"><a onclick="window.open('{{ route('regrepveh.excel.export')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val()+'&precio='+$('#precio').val(),'_blank');" style="color:#fff" target="_blank"><i class="material-icons">cloud_download</i> Rep. Detallado Veh.</a></button>
				<button class = "btn p-2 pl-1 pr-1 btn-primary"><a onclick="window.open('{{ route('regrepveh.excel2.export')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val()+'&precio='+$('#precio').val(),'_blank');" style="color:#fff" target="_blank"><i class="material-icons">cloud_download</i> Rep. Consolidado Veh.</a></button>
				{!! Form::button('<i class="material-icons">cloud_download</i> Export Mant/Checklist ', array('class' => 'btn btn-primary p-2 pl-1 pr-1', 'id' => 'btnReporte2', 'onclick' => 'excel2()')) !!}
				{!! Form::button('<i class="material-icons">cloud_download</i> Exportar ', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnReporte2', 'onclick' => 'excel3()')) !!}
				{!! Form::button('<i class="material-icons">cloud_download</i> Exportar Detalle Repuestos', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnReporte3', 'onclick' => 'excelRepuesto()')) !!}
				{!! Form::button('<i class="material-icons">cloud_download</i> Registro x Usuario Mes', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnReporte4', 'onclick' => 'excelResumenUsuarioMes()')) !!}
				{!! Form::button('<i class="material-icons">cloud_download</i> Registro x Usuario Dia', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnReporte4', 'onclick' => 'excelResumenUsuarioDia()')) !!}
				{!! Form::button('<i class="material-icons">add</i>Registrar', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["createrepuesto"], array('listar'=>'SI')).'\', \''.$tituloRegistrar.'\', this);')) !!}

			</div>
			{!! Form::close() !!}
			<div class="table-responsive" id="listado{{ $entidad }}">
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="filter"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		const doSearchRepuesto = () => {

	    var autoCompletejsRepuesto = new autoComplete({
	        data: {
	            src: async () => {
	                // Loading placeholder text
	                document
	                    .querySelector(".js-repuesto")
	                    .setAttribute("placeholder", "Loading...");
	       
	                // Fetch External Data Source
	                const headers = new Headers();
	                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
	                const config = {
	                    headers,
	                    method:'GET'
	                };
	                  //Query
	                const query = document.querySelector('.js-repuesto').value;
	                const source = await fetch(
	                    `regrepveh/search/repuesto/${query}` , config
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
	        selector: ".js-repuesto",
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
	            destination: document.querySelector(".js-repuesto"),
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
	            document.querySelector("#autoComplete_listRepuesto").innerText = 'Sin resultados';
	        },
	        onSelection: feedback => { //VA NUESTRA LOGICA
	            document.querySelector("#autoComplete_listRepuesto").innerText = '';
	            const selection = feedback.selection.value;
	            document.querySelector(".js-repuesto").value = selection.descripcion;
	            // console.log(feedback);
	        }
	    });
	    
	    // Toggle event for search input
	    // showing & hidding results list onfocus / blur
	    ["focus", "blur"].forEach(function(eventType) {
	      const resultsList = document.querySelector("#autoComplete_listRepuesto");
	    
	      document.querySelector(".js-repuesto").addEventListener(eventType, function() {
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
	doSearchRepuesto();
	});
	function excel2(){
		window.open('{{ route('regrepveh.excel3.export')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val(),'_blank');
	}
	function excel3(){
		window.open('{{ route('regrepveh.excel4.export')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val()+"&tipofecha="+$("#tipofecha").val()+'&filter='+$('#filter').val(),'_blank');
	}
	function excelRepuesto(){
		window.open('{{ route('regrepveh.excelRepuesto.export')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val()+'&repuesto='+$('#repuesto').val()+'&filter='+$('#filter').val(),'_blank');
	}
	function excelResumenUsuarioDia(){
		window.open('{{ route('regrepveh.excelResumenUsuarioDia')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val()+"&tipofecha="+$("#tipofecha").val(),'_blank');
	}
	function excelResumenUsuarioMes(){
		window.open('{{ route('regrepveh.excelResumenUsuarioMes')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val()+"&tipofecha="+$("#tipofecha").val(),'_blank');
	}
</script>