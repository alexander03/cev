<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as phpmailerException;
include_once("cado.php");

$conexion = new clsAccesoDatos();
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP
$correos = $conexion->obtenerDataSQL("select * from configuracion where id=8")->fetchObject()->valor;

try {
  $rst = $conexion->obtenerDataSQL("select vehiculo.*,kilometraje.limite_inf,kilometraje.descripcion as regla,kilometraje.limite_sup,concesionaria.abreviatura as concesionaria,a.descripcion as area 
  	from vehiculo 
  	inner join concesionaria on concesionaria.id=vehiculo.concesionaria_id
    left join area a on a.id=vehiculo.area_id
  	inner join kilometraje on kilometraje.id = vehiculo.kilometraje_id where vehiculo.deleted_at is null and vehiculo.contratista_id in (12,27,38) and vehiculo.estado not in ('Desmovilizado')");
  if($rst->rowCount()>0){
    //$listaCorreo = explode(";","javiergonzales@h2cto.com;vcaceres@h2cto.com;mquezada@h2cto.com;clunam@h2cto.com");//$data->correolider.";
    $listaCorreo = explode(";",$correos);
    //$listaCorreo = explode(";","alex_3849@hotmail.com;javiergonzales@h2cto.com");
    $mail = new PHPMailer();
    //$mail->SMTPDebug = 2;
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465; 
    $mail->Username = 'noticevh2cto@gmail.com';
    $mail->Password = 'ftlomfkmxcbqjoiy'; 
    $mail->SetFrom('noticevh2cto@gmail.com', 'CEV - Notificaciones');
    
    for ($i=0; $i < count($listaCorreo) ; $i++) { 
      $mail->AddAddress($listaCorreo[$i]);
    }
    
    $mensaje="Para reportar el detalle del siguiente recorrido: <br /><br />";
    $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                  <tr>
                    <th style='border-style: solid; border-color: black;'>NRO</th>
                    <th style='border-style: solid; border-color: black;'>CONCESIONARIA</th>
                    <th style='border-style: solid; border-color: black;'>REGLA KM</th>
                    <th style='border-style: solid; border-color: black;'>MODELO</th>
                    <th style='border-style: solid; border-color: black;'>PLACA</th>
                    <th style='border-style: solid; border-color: black;'>AREA</th>
                    <th style='border-style: solid; border-color: black;'>FECHA</th>
                    <th style='border-style: solid; border-color: black;'>KM INICIAL DE CONTROL</th>
                    <th style='border-style: solid; border-color: black;'>KM RECORRIDO</th>
                    <th style='border-style: solid; border-color: black;'>KM ACTUAL(CHECK/COMB/MANT)</th>
                    <th style='border-style: solid; border-color: black;'>DIFERENCIA KM</th>
                    <th style='border-style: solid; border-color: black;'>ESTADO</th>
                    <th style='border-style: solid; border-color: black;'>COMENTARIO</th>
                    <th style='border-style: solid; border-color: black;'>ESTADO MANT.</th>
                  </tr>
            ";
      $c=0;
      while($data = $rst->fetchObject()){
        $id = $data->id;
        $data->kilometraje_rec = $data->kilometraje_act - $data->kilometraje_inicial;
        if( $data->kilometraje_rec > $data->limite_inf ){$c++;
          
          $rst2 = $conexion->obtenerDataSQL("select T.* from ((select fecha_registro as fecha from checklistvehicular where vehiculo_id=".$id." and deleted_at is null and especial = true order by fecha_registro desc limit 1)
            union all
            (select fecha_abastecimiento as fecha from abastecimiento_combustible where vehiculo_id=".$id." and deleted_at is null and especial=true order by fecha_abastecimiento desc limit 1)
            union all 
            (select fechasalida as fecha from regrepveh where ua_id=".$id." and deleted_at is null and especial=true order by fechasalida desc limit 1)
            union all
            (select fechasalida as fecha from regmanveh where ua_id=".$id." and deleted_at is null and especial=true order by fechasalida desc limit 1)) as T order by T.fecha desc");
          $dataUltimo = $rst2->fetchObject();
          if(!is_null($dataUltimo)){
            $FechaUltimoMantEspecial = date("d/m/Y",strtotime($dataUltimo->fecha));
          }else{
            $FechaUltimoMantEspecial = '';
          }
          if( $data->kilometraje_rec < $data->limite_inf ){
            $color="";
          }elseif( $data->kilometraje_rec < $data->limite_sup ){
            $color="";
          }else{
            $color="color:red";
          }
          
          $mensaje.="<tr style='".$color."'>";
          $mensaje.="<td style='text-align:center'>".$c."</td>";
          $mensaje.="<td style='text-align:center'>".$data->concesionaria."</td>";
          $mensaje.="<td style='text-align:center'>".utf8_decode($data->regla)."</td>";
          $mensaje.="<td style='text-align:center'>".utf8_decode($data->modelo)."</td>";
          $mensaje.="<td style='text-align:center'>".$data->placa."</td>";
          $mensaje.="<td style='text-align:center'>".$data->area."</td>";
          $mensaje.="<td style='text-align:center'>".$FechaUltimoMantEspecial."</td>";
          $mensaje.="<td style='text-align:center'>".$data->kilometraje_inicial."</td>";
          $mensaje.="<td style='text-align:center'>".$data->kilometraje_rec."</td>";
          $mensaje.="<td style='text-align:center'>".number_format($data->kilometraje_act,2,'.','')."</td>";
          $mensaje.="<td style='text-align:center'>".number_format($data->limite_sup - $data->kilometraje_rec,2,'.','')."</td>";
          $estadorecorrido = '';
          if( $data->kilometraje_rec < $data->limite_inf ){
            $mensaje.="<td > Normal </td>";
          }elseif( $data->kilometraje_rec < $data->limite_sup ){
            $mensaje.=utf8_decode("<td> Observación </td>");
          }else{
            $mensaje.="<td> Desgastado </td>";
            $estadorecorrido = 'NO EJECUTADO';
          }
          $mensaje.="<td style='text-align:center'>".utf8_decode($data->comentario)."</td>";
          if($data->estadorecorrido==""){
            $mensaje.="<td style='text-align:center'>".utf8_decode($estadorecorrido)."</td>";
          }else{
            $mensaje.="<td style='text-align:center'>".utf8_decode($data->estadorecorrido)."</td>";
          }
          $mensaje.="</tr>";
        }
      }
      $mensaje.="</table>";

    //$usuario=$data->lider;
      $usuario="";
    $mail->Subject = "ALERTA DE KILOMETRAJE VEHICULOS LIVIANOS";
    
    $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION KILOMETRAJE VEHICULAR</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimados(a): '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
    $mail->AltBody = $html; // optional - MsgHTML will create an alternate automatically
    $mail->MsgHTML($html);
    if(!$mail->Send()) {
      echo "Error while sending Email.";
      echo $mail->ErrorInfo;
      print_r($mail);
    } else {
      echo "Email sent successfully;<script>window.close();</script>";
    }
  }
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>

</body>
</html>
