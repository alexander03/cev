@php
use App\Detallepreciocombustible;
@endphp
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($preciocombustible, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('fechainicio', 'Fecha Inicio:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::date('fechainicio', null, array('class' => 'form-control input-xs', 'id' => 'fechainicio')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('fechafin', 'Fecha Fin:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::date('fechafin', null, array('class' => 'form-control input-xs', 'id' => 'fechafin')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('grifo_id', 'Grifo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::select('grifo_id', $cboGrifo, null, array('class' => 'form-control input-xs', 'id' => 'grifo_id')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('cboTipocombustible', 'Tipo Combustible:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="row form-group">
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::select('cboTipocombustible', $cboTipocombustible, null, array('class' => 'form-control input-xs', 'id' => 'cboTipocombustible')) !!}
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1">
			<button class="btn btn-sm btn-info" type="button" onclick="agregarParametro();"><i class="fa fa-plus fa-fw"></i></button>
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12">
		<table class="table table-sm text-center table-striped  table-hover">
		    <thead>
		        <tr>
		            <th>Tipo Combustible</th>
		            <th>Precio</th>
		        </tr>
		    </thead>
		    <tbody id='tbParametro'>
		        <?php 
		        if(!is_null($preciocombustible)){
		            $jscript="";
		            $data = Detallepreciocombustible::where('preciocombustible_id','=',$preciocombustible->id)->get();
					foreach($data as $k => $v){
                        $vid = $v->tipocombustible_id;
		                echo "<tr id='trParametro".$vid."'>";
                        echo "<td><input type='hidden' readonly='' name='tipocombustible[]' value='".$vid."' />".$v->tipocombustible->descripcion."</td>";
                        echo "<td><input type='number' name='precio[]' value='".$v->precio."' class='form-control input-xs' style='width:70px;' /></td>";
                        echo "<td><a href='#' onclick=\"quitarParametro('".$vid."')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>";
                        $jscript.="carroParametro.push(".$vid.");";
		            }
		        }
		        ?>
		    </tbody>
		</table>
	</div>
</div>
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('450');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 
var carroParametro = new Array();
function agregarParametro(){
    var vid = $("#cboTipocombustible").val();
    var band = true;
    for(c=0; c < carroParametro.length; c++){
        if(carroParametro[c] == vid) {
        	band = false;
        }
    }
    if(band){
	    $("#tbParametro").append("<tr id='trParametro"+vid+"'>"+
	        "<td><input type='hidden' name='tipocombustible[]' value='"+vid+"'/>"+$("#cboTipocombustible option:selected").text()+"</td>"+
	        "<td><input type='number' name='precio[]' value='0' class='form-control input-xs' style='width:70px;' /></td>"+
	        "<td><a href='#' onclick=\"quitarParametro('"+vid+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
	    carroParametro.push(vid);
	}
}
function quitarParametro(vid){
    $("#trParametro"+vid).remove();
    for(c=0; c < carroParametro.length; c++){
        if(carroParametro[c] == vid) {
            carroParametro.splice(c,1);
        }
    }
}
@php
	if(!is_null($preciocombustible)){
		echo $jscript;
	}
@endphp
</script>