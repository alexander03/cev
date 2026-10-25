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
						<div class="col-8 col-sm-4 col-md-4 col-lg-4">
							{!! Form::label('periodoinicial', 'Periodo Inicial:') !!}
							{!! Form::select('periodoinicial',$cboPeriodo, '', array('class' => 'form-control input-xs', 'id' => 'periodoinicial')) !!}
						</div>
						<div class="col-8 col-sm-4 col-md-4 col-lg-4">
							{!! Form::label('periodofinal', 'Periodo Final:') !!}
							{!! Form::select('periodofinal',$cboPeriodo, '', array('class' => 'form-control input-xs', 'id' => 'periodofinal')) !!}
						</div>
						<div class="form-group col-12">
							{!! Form::button('<i class="material-icons">download</i>Mant. Preventivo', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelMantPreventivo();')) !!}
							{!! Form::button('<i class="material-icons">download</i>Mant. Correctivo', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelMantCorrectivo();')) !!}
							{!! Form::button('<i class="material-icons">download</i>Comb. Liviano', array('class' => 'btn btn-danger p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelCombustibleLiviano();')) !!}
							{!! Form::button('<i class="material-icons">download</i>Comb. Pesado', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelCombustiblePesado();')) !!}
							{!! Form::button('<i class="material-icons">download</i>Otros(SOAT/GPS/POL/RT)', array('class' => 'btn btn-primary p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelOtros();')) !!}
							{!! Form::button('<i class="material-icons">download</i>Gasto Total Liviano', array('class' => 'btn btn-danger p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelGastoLiviano();')) !!}
							{!! Form::button('<i class="material-icons">download</i>Gasto Total Pesado', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelGastoPesado();')) !!}
							{!! Form::button('<i class="material-icons">download</i>Rend. Liviano', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelRendimientoLiviano();')) !!}
							{!! Form::button('<i class="material-icons">download</i>Rend. Pesado', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'excelRendimientoPesado();')) !!}
							<a href="#" class="ml-1 btn btn-sm btn-primary js-import-excel" style="display: none;">
								<i class="material-icons">cloud_upload</i> Importar
							</a>
							<input class="js-import-excel-file" type="file" accept=".xls, .xlsx" style="display: none;">
							<?php /*{!! Form::button('<i class="material-icons">add</i>Nuevo', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}*/ ?>
						</div>
						<div class="form-group" style="padding-left: 15px;">
							{!! Form::button('<i class="material-icons">search</i>GR. Mant. Prev', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoMantPreventivo();')) !!}
							{!! Form::button('<i class="material-icons">search</i>GR. Mant. Corr', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoMantCorrectivo();')) !!}
							{!! Form::button('<i class="material-icons">search</i>GR. Comb. Liviano', array('class' => 'btn btn-danger p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoCombustibleLiviano();')) !!}
							{!! Form::button('<i class="material-icons">search</i>GR. Comb. Pesado', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoCombustiblePesado();')) !!}
							{!! Form::button('<i class="material-icons">search</i>GR. Otros(SOAT/GPS/POL/RT)', array('class' => 'btn btn-primary p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoOtros();')) !!}
							{!! Form::button('<i class="material-icons">search</i>GR. Gasto Total Liviano', array('class' => 'btn btn-danger p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoGastoLiviano();')) !!}
							{!! Form::button('<i class="material-icons">search</i>GR. Gasto Total Pesado', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoGastoPesado();')) !!}
							{!! Form::button('<i class="material-icons">search</i>GR. Rend. Liviano', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoRendimientoLiviano();')) !!}
							{!! Form::button('<i class="material-icons">search</i>GR. Rend. Pesado', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'graficoRendimientoPesado();')) !!}
						</div>
						{!! Form::close() !!}
						<div class="table-responsive" id="listado{{ $entidad }}">
							<div id="chart1">
							</div>
							<br />
							<br />
							<br />
							<div id="chart2">
							</div>
							<br />
							<br />
							<br />
							<div id="chart3">
							</div>
						</div>
					</div>
				</div>
			</div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
	$(document).ready(function () {
		//buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="descripcion"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});

	doImportDataantiguaExcel();

	function excelMantPreventivo(){
		window.open('reportekpi/exportarMantPreventivo?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}
	function excelMantCorrectivo(){
		window.open('reportekpi/exportarMantCorrectivo?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}
	function excelCombustibleLiviano(){
		window.open('reportekpi/exportarCombustibleLiviano?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}
	function excelCombustiblePesado(){
		window.open('reportekpi/exportarCombustiblePesado?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}
	function excelOtros(){
		window.open('reportekpi/exportarOtros?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}
	function excelGastoLiviano(){
		window.open('reportekpi/exportarGastoLiviano?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}
	function excelGastoPesado(){
		window.open('reportekpi/exportarGastoPesado?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}
	function excelRendimientoLiviano(){
		window.open('reportekpi/exportarRendimientoLiviano?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}
	function excelRendimientoPesado(){
		window.open('reportekpi/exportarRendimientoPesado?periodoinicial='+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val(),'_blank');
	}

	var chart1 = "";
	var chart2 = "";
	var chart3 = "";

	function graficoMantPreventivo(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoMantPreventivo",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Kilometraje'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'KM Mant. Preventivo Vehiculos Livianos',
					    align: 'center',
					}
			    };

			    var options2 = {
			        series: vData.serie2,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/.'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        title: {
					    text: 'Costos Mant. Preventivo Vehiculos Livianos',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

				chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
				chart2.render();
		    }
	    });
	}

	function graficoMantCorrectivo(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoMantCorrectivo",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Kilometraje'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'KM Mant. Correctivo Vehiculos Livianos',
					    align: 'center',
					}
			    };

			    var options2 = {
			        series: vData.serie2,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/.'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        title: {
					    text: 'Costos Mant. Correctivo Vehiculos Livianos',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

				chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
				chart2.render();
		    }
	    });
	}

	function graficoCombustibleLiviano(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoCombustibleLiviano",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Gal'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'Gal. Combustible Vehiculos Livianos',
					    align: 'center',
					}
			    };

			    var options2 = {
			        series: vData.serie2,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/.'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        title: {
					    text: 'Costos Combustible Vehiculos Livianos',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

				chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
				chart2.render();
		    }
	    });
	}

	function graficoCombustiblePesado(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoCombustiblePesado",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			        	labels: {
					      rotate: -90,
					      style: {
			                  fontSize: '9px',
			                },
					    },
			          	categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Gal'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'Gal. Combustible Vehiculos Pesados',
					    align: 'center',
					}
			    };

			    var options2 = {
			        series: vData.serie2,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			        	labels: {
					      rotate: -90,
					      style: {
			                  fontSize: '9px',
			                },
					    },
			          	categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/.'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        title: {
					    text: 'Costos Combustible Vehiculos Pesados',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

				chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
				chart2.render();
		    }
	    });
	}

	function graficoOtros(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoOtros",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          	categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/.'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'Otros Vehiculos Livianos',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

		    }
	    });
	}

	function graficoGastoLiviano(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoGastoLiviano",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          	categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/.'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'Gasto Total Vehiculos Livianos',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

		    }
	    });
	}

	function graficoGastoPesado(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoGastoPesado",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			        	labels: {
					      rotate: -90,
					      style: {
			                  fontSize: '9px',
			                },
					    },
			          	categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/.'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'Gasto Total Vehiculos Pesados',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

		    }
	    });
	}

	function graficoRendimientoLiviano(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoRendimientoLiviano",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'KM'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'KM Recorrido Vehiculos Livianos',
					    align: 'center',
					}
			    };

			    var options2 = {
			        series: vData.serie2,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Gal'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        title: {
					    text: 'Gal/Mes Vehiculos Livianos',
					    align: 'center',
					}
			    };

			    var options3 = {
			        series: vData.serie3,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			          categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette7' // upto palette10
					},
			        title: {
					    text: 'Rend. x Galon Vehiculos Livianos',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

				chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
				chart2.render();

				chart3 = new ApexCharts(document.querySelector("#chart3"), options3);
				chart3.render();
		    }
	    });
	}

	function graficoRendimientoPesado(){
	    $.ajax({
	        type: "POST",
	        url: "reportekpi/graficoRendimientoPesado",
	        data: "periodoinicial="+$("#periodoinicial").val()+"&periodofinal="+$("#periodofinal").val()+"&_token="+$(':input[name="_token"]').val(),
	        beforeSend:function(){
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	$("#chart1").html("Cargando información, espere...");
	        	$("#chart2").html("");
	        	$("#chart3").html("");
	        },
	        success: function(a) {
	        	$("#chart1").html("");
	        	var vData = JSON.parse(a);
	        	if(chart1!==undefined && chart1!=""){
	        		chart1.destroy();
	        	}
	        	if(chart2!==undefined && chart2!=""){
	        		chart2.destroy();
	        	}
	        	if(chart3!==undefined && chart3!=""){
	        		chart3.destroy();
	        	}
	        	var options = {
			        series: vData.serie,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			        	labels: {
					      rotate: -90,
					      style: {
			                  fontSize: '9px',
			                },
					    },
			          	categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'KM'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette8' // upto palette10
					},
					title: {
					    text: 'KM Recorrido Vehiculos Pesados',
					    align: 'center',
					}
			    };

			    var options2 = {
			        series: vData.serie2,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			        	labels: {
					      rotate: -90,
					      style: {
			                  fontSize: '9px',
			                },
					    },
			          	categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Gal'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        title: {
					    text: 'Gal/Mes Vehiculos Pesados',
					    align: 'center',
					}
			    };

			    var options3 = {
			        series: vData.serie3,
			        chart: {
			          type: 'bar',
			          height: 350
			        },
			        plotOptions: {
			          bar: {
			            horizontal: false,
			            columnWidth: '55%',
			            endingShape: 'rounded'
			          },
			        },
			        dataLabels: {
			          enabled: false
			        },
			        stroke: {
			          show: true,
			          width: 2,
			          colors: ['transparent']
			        },
			        xaxis: {
			        	labels: {
					      rotate: -90,
					      style: {
			                  fontSize: '9px',
			                },
					    },
			          	categories: vData.placa,
			        },
			        yaxis: {
			          title: {
			            text: 'Costo S/'
			          }
			        },
			        fill: {
			          opacity: 1
			        },
			        theme: {
					  palette: 'palette7' // upto palette10
					},
			        title: {
					    text: 'Rend. x Galon Vehiculos Pesados',
					    align: 'center',
					}
			    };

				chart1 = new ApexCharts(document.querySelector("#chart1"), options);
				chart1.render();

				chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
				chart2.render();

				chart3 = new ApexCharts(document.querySelector("#chart3"), options3);
				chart3.render();

				//window.open('https://quickchart.io/apex-charts/render?'+str,'_blank');
		    }
	    });
	}
</script>