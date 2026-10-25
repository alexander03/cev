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
  $rst = $conexion->obtenerDataSQL("select di.*,t.razonsocial as taller,case when di.vehiculo_id is null or di.vehiculo_id=0 then e.placa else v.placa end as placa,e.descripcion as equipo_descripcion,case when di.vehiculo_id is null or di.vehiculo_id=0 then e.modelo else v.modelo end as modelo,ch.observaciones,ch.incidentes,ch.fecha_registro as fecha_registro_check,ch.hora_fin,ch.hora_inicio
          from diagnostico di
          left join taller t on t.id=di.taller_id
          left join checklistvehicular ch on ch.id=di.checklistvehicular_id
          left join equipo e on e.id=di.equipo_id
          left join vehiculo v on v.id=di.vehiculo_id
          where di.deleted_at is null and di.id=".$_GET["id"]." order by di.created_at desc");
  if($rst->rowCount()>0){
    $data = $rst->fetchObject();
    
    $revisiones="";
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
    $mail->AddAttachment('/var/www/html/cev/storage/app/public/pdf/RegistroDeMantenimientoVehicular - '.$_GET["id"].'.pdf'); 
    
    for ($i=0; $i < count($listaCorreo) ; $i++) { 
      $mail->AddAddress($listaCorreo[$i]);
    }
    
    $mensaje="Para reportar el detalle del siguiente mantenimiento vehicular registrado: <br /><br />";
    $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                  <tr>
                    <th style='border-style: solid; border-color: black;'>NUMERO</th>
                    <th style='border-style: solid; border-color: black;'>FECHA REGISTRO</th>
                    <th style='border-style: solid; border-color: black;'>FECHA DIAG.</th>
                    <th style='border-style: solid; border-color: black;'>TALLER</th>
                    <th style='border-style: solid; border-color: black;'>MODELO</th>
                    <th style='border-style: solid; border-color: black;'>DESCRIP/PLACA</th>
                    <th style='border-style: solid; border-color: black;'>CHECKLIST</th>
                    <th style='border-style: solid; border-color: black;'>RECORRIDO KM/HOROMETRO</th>
                    <th style='border-style: solid; border-color: black;'>REVISION</th>
                    <th style='border-style: solid; border-color: black;'>DIAGNOSTICO</th>
                    <th style='border-style: solid; border-color: black;'>RECOMENDACION</th>
                    <th style='border-style: solid; border-color: black;'>CIERRE</th>
                    <th style='border-style: solid; border-color: black;'>TIPO</th>
                  </tr>
            ";
      $mensaje.="<tr>";
      $mensaje.="<td style='text-align:center'>".$data->numero."</td>";
      $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($data->fecharegistro))."</td>";
      if($data->fecha!=""){
        $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($data->fecha))."</td>";
      }else{
        $mensaje.="<td style='text-align:center'> - </td>";
      }
      $mensaje.="<td style='text-align:center'>".utf8_decode($data->taller)."</td>";
      $mensaje.="<td style='text-align:center'>".$data->modelo."</td>";
      $mensaje.="<td style='text-align:center'>".$data->placa." ".$data->equipo_descripcion."</td>";
      $checklist="";
      if(($data->checklistvehicular_id+0)>0){
        $checklist = date("d/m/Y",strtotime($data->fecha_registro_check))." ".substr($data->hora_inicio,0,5)." - ".substr($data->hora_fin,0,5)." | ".$data->observaciones." ".$data->incidentes;
      }
      $mensaje.="<td style='text-align:center'>".$checklist."</td>";
      $mensaje.="<td style='text-align:center'>".$data->recorrido."</td>";
      $mensaje.="<td style='text-align:center'>".$data->revision."</td>";
      $mensaje.="<td style='text-align:center'>".$data->diagnostico."</td>";
      $mensaje.="<td style='text-align:center'>".$data->recomendacion."</td>";
      $mensaje.="<td style='text-align:center'>".$data->cierre."</td>";
      $mensaje.="<td style='text-align:center'>".($data->tipomantenimiento=="1"?"Preventivo":"Correctivo")."</td>";
      $mensaje.="</tr>";
      $mensaje.="</table>";

    //$usuario=$data->lider;
      $usuario="";
    $mail->Subject = "ALERTA DIAGNOSTICO DE VEHICULO REGISTRADO";
    $url = 'https://sistemasolmos.pe/cev/';

    $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION DIAGNOSTICO DE VEHICULO</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimados(a): '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><p>
                <center><a href="' . $url . '" style="font-weight: bold;font-size:18px">***REVISAR***</a></center></p>
                <h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
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
