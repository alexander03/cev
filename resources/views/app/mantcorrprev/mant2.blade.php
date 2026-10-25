
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($repuesto, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<section class="form-row">
	<div class="form-group col-lg-12 col-md-12 col-sm-12">
		{!! Form::label('consecion', 'Conseción:', array('class' => 'col-12 col-sm-12 control-label')) !!}
		<div class="col-12 col-sm-12">
			{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'conseción')) !!}
		</div>
	</div>
	<div class="form-group col-lg-5 col-md-5 col-sm-5">
		{!! Form::label('concesionaria_id', 'Concesionaria:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('unidad_id', $oConcesionarias, null, array('class' => 'form-control input-xs', 'id' => 'unidad_id')) !!}
		</div>
	</div>
	<div class="form-group col-lg-7 col-md-7 col-sm-7 bmd-label-floating">
		{!! Form::label('cliente', 'Cliente:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('cliente', null, array('class' => 'form-control input-xs', 'id' => 'cliente', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="mt-4 mb-2 ml-3  col-lg-12 col-md-12 col-sm-12">
		<p class="text-warning ">Datos Generales</p> 
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		<div class="container">
			<label for="ua" class="col-lg-8 col-md-8 col-sm-8 control-label">UA:
				<label id="buscarporUA" style="color:black" onclick="buscarporUA()" onmouseout="this.style.color='black'" onmouseover="this.style.color='orange';">Comprobar
				</label>
			</label>
			{!! Form::text('ua1', null, array('class' => 'form-control input-xs', 'id' => 'uabuscar', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		{!! Form::label('kmman', 'Km de mantenimiento:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('kmman', null, array('class' => 'form-control input-xs', 'id' => 'kmman', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-12">
		{!! Form::label('kmin', 'Km Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('kmin', null, array('class' => 'form-control input-xs', 'id' => 'kmin', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		{!! Form::label('kmfin', 'Km Final:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('kmfin', null, array('class' => 'form-control input-xs', 'id' => 'kmfin', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		{!! Form::label('fin', 'Fecha Entrada:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fin', null, array('class' => 'form-control input-xs', 'id' => 'fin', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		{!! Form::label('fin', 'Fecha Salida:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fin', null, array('class' => 'form-control input-xs', 'id' => 'fin', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		{!! Form::label('tipo', 'Tipo de Mantenimiento:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipo',array('1'=>'Preventivo','2'=>'Correctivo'), null, array('class' => 'form-control input-xs', 'id' => 'tipo', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		{!! Form::label('telefono', 'Telefono:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('telefono', null, array('class' => 'form-control input-xs', 'id' => 'telefono', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="mt-4 mb-2 ml-3  col-lg-10 col-md-10 col-sm-10">
		<p class="text-warning ">Observaciones</p> {!! Form::button('<i class="fa fa-plus fa-lg"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregar', 'onclick' => 'agregarfila();')) !!}
	</div>
	<div class="container" id="lista">
		<div class='row container'>
			<div class='form-group col-lg-2 col-md-2 col-sm-2'>
				{!! Form::label('item', 'ITEM', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class='col-lg-12 col-md-12 col-sm-12'>
					<input class='form-control input-xs' id='item1' disabled value='1'>
				</div>
			</div>
			<div class='form-group col-lg-2 col-md-2 col-sm-2'>
				{!! Form::label('cant', 'CANT.', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class='col-lg-12 col-md-12 col-sm-12'>
					{!! Form::text('cant', null, array('class' => 'form-control input-xs', 'id' => 'cant1', 'maxlength' => '100')) !!}
				</div>
			</div>
			<div class='form-group col-lg-2 col-md-2 col-sm-2'>
				{!! Form::label('unidad', 'UND.', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class='col-lg-12 col-md-12 col-sm-12'>
					{!! Form::text('unidad', null, array('class' => 'form-control input-xs', 'id' => 'unidad1', 'maxlength' => '100')) !!}
				</div>
			</div>
			<div class='form-group col-lg-3 col-md-3 col-sm-3'>
				{!! Form::label('cod', 'CODIGO', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class='col-lg-12 col-md-12 col-sm-12'>
					{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo1', 'maxlength' => '100')) !!}
				</div>
			</div>
			<div class='form-group col-lg-3 col-md-3 col-sm-3'>
				{!! Form::label('monto', 'MONTO', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class='col-lg-12 col-md-12 col-sm-12'>
					{!! Form::text('monto', null, array('class' => 'form-control input-xs', 'id' => 'monto1', 'maxlength' => '100')) !!}
				</div>
			</div>
			<div class='form-group col-lg-12 col-md-12 col-sm-12'>
				{!! Form::label('descripcion', 'DESCRIPCIÓN DE REPUESTOS', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class='col-lg-12 col-md-12 col-sm-12'>
					{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'maxlength' => '100')) !!}
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'cerrarModal();')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	var a = 1;
	$(document).ready(function() {
		configurarAnchoModal('800');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

		const inputCode = document.getElementById('codigo');
		inputCode.addEventListener('change', e => {
			if(isNaN(e.target.value)) e.target.value = '';
		})

		inputCode.addEventListener('keydown', e => {
			//TODO: que acepte las teclas: flecha left y right
			if(e.target.value.length > 6 && e.keyCode != 8) e.preventDefault();
			if(e.keyCode < 8 || (e.keyCode >9 && e.keyCode< 48) || (e.keyCode >57 && e.keyCode< 67) || (e.keyCode >67 && e.keyCode< 86) || (e.keyCode >86 && e.keyCode< 96) || e.keyCode> 105) e.preventDefault();
		})
	}); 	

	function buscarporUA(){
		

// 		//alert("gg nomas");
// 		var uanumero= document.getElementById('uabuscar').value;
// 		var informe = "dd";
// 		$.ajax("mantcorrprev/buscarporua?ua="+uanumero, function(data) {
			
// 			informe+=data.value;
// 			/*if (data!=null) {
// 				alert(data);
// 			}else{
// 				alert("No existe Unidad con UA"+uanumero);
//        		}*/
// });
// 		alert(informe);

		var uanumero= document.getElementById('uabuscar').value;

		var serviceURL = "mantcorrprev/buscarporua?ua="+uanumero;

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
            var str =  JSON.stringify(data['gg']);    
                alert(str.substr(1, str.length-2));
            }
            function errorFunc() {
                alert('borraron la columna ua_id de tabla equipos, rehacer funcion -buscarporua- de controller mantprevcorr');
            }
        



	}


	function agregarfila(){
		a++;
		document.getElementById('lista').innerHTML+=`<div class='row container'><div class='form-group col-lg-2 col-md-2 col-sm-2 bmd-form-group'>
		<label for='item' class='col-lg-12 col-md-12 col-sm-12 control-label bmd-label-static'>ITEM</label>
		<div class='col-lg-12 col-md-12 col-sm-12'>
			<input class='form-control input-xs' id='item${a}' disabled value='${a}'>
		</div>
	</div><div class='form-group col-lg-2 col-md-2 col-sm-2 bmd-form-group'>
		<label for='cantidad' class='col-lg-12 col-md-12 col-sm-12 control-label bmd-label-static'>CANT.</label>
		<div class='col-lg-12 col-md-12 col-sm-12'>
			<input class='form-control input-xs' id='cantidad${a}' maxlength='100' type='text'>
		</div>
	</div>
	<div class='form-group col-lg-2 col-md-2 col-sm-2 bmd-form-group'>
		<label for='unidad' class='col-lg-12 col-md-12 col-sm-12 control-label bmd-label-static'>UND.</label>
		<div class='col-lg-12 col-md-12 col-sm-12'>
			<input class='form-control input-xs' id='unidad${a}' maxlength='100' type='text'>
		</div>
	</div>
	<div class='form-group col-lg-3 col-md-3 col-sm-3 bmd-form-group'>
		<label for='codigo' class='col-lg-12 col-md-12 col-sm-12 control-label bmd-label-static'>CODIGO</label>
		<div class='col-lg-12 col-md-12 col-sm-12'>
			<input class='form-control input-xs' id='codigo${a}' maxlength='100' type='text'>
		</div>
	</div>
	<div class='form-group col-lg-3 col-md-3 col-sm-3 bmd-form-group'>
		<label for='monto' class='col-lg-12 col-md-12 col-sm-12 control-label bmd-label-static'>MONTO</label>
		<div class='col-lg-12 col-md-12 col-sm-12'>
			<input class='form-control input-xs' id='monto${a}' maxlength='100'  type='text'>
		</div>
	</div>
	<div class='form-group col-lg-12 col-md-12 col-sm-12 bmd-form-group'>
		<label for='descripcion' class='col-lg-12 col-md-12 col-sm-12 control-label bmd-label-static'>DESCRIPCIÓN DE REPUESTOS</label>
		<div class='col-lg-12 col-md-12 col-sm-12'>
			<input class='form-control input-xs' id='descripcion${a}' maxlength='100'  type='text'>
		</div>
	</div>
	</div>`;


}
</script>