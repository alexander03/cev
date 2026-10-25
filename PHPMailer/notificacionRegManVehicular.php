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
  $rst = $conexion->obtenerDataSQL("select rmv.*,case when rmv.ua_id is null or rmv.ua_id=0 then e.placa else v.placa end as placa,e.descripcion as equipo_descripcion,v.modelo,t.razonsocial as taller,av.correo as correovehiculo,ae.correo as correoequipo,us.name as usuario
          from regmanveh rmv 
          left join vehiculo v on v.id=rmv.ua_id
          left join area av on av.id=v.area_id
          left join equipo e on e.id=rmv.equipo_id
          left join area ae on ae.id=e.area_id
          left join taller t on t.id=rmv.taller_id
          left join users us on us.id=rmv.user_id
          where rmv.deleted_at is null and rmv.id=".$_GET["id"]." order by rmv.created_at desc");
  if($rst->rowCount()>0){
    $data = $rst->fetchObject();
    if($data->situacion=='F' && ($data->correovehiculo!="" || $data->correoequipo!="")){
      $correos.=";".$data->correoequipo.$data->correovehiculo;
    }
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
                    <th style='border-style: solid; border-color: black;'>NRO</th>
                    <th style='border-style: solid; border-color: black;'>FECHA REGISTRO</th>
                    <th style='border-style: solid; border-color: black;'>FECHA MANT.</th>
                    <th style='border-style: solid; border-color: black;'>DESCRIPCION</th>
                    <th style='border-style: solid; border-color: black;'>TALLER</th>
                    <th style='border-style: solid; border-color: black;'>DOCMAT</th>
                    <th style='border-style: solid; border-color: black;'>PLACA</th>
                    <th style='border-style: solid; border-color: black;'>KM INICIAL</th>
                    <th style='border-style: solid; border-color: black;'>KM MANT.</th>
                    <th style='border-style: solid; border-color: black;'>KM FINAL</th>
                    <th style='border-style: solid; border-color: black;'>FECHA DOCMAT</th>
                    <th style='border-style: solid; border-color: black;'>FECHA COTIZACION</th>
                    <th style='border-style: solid; border-color: black;'>TIPO</th>
                    <th style='border-style: solid; border-color: black;'>SITUACION</th>
                    <th style='border-style: solid; border-color: black;'>USUARIO</th>
                  </tr>
            ";
      if ($data->tipomantenimiento==1)
        $estado = "Preventivo";
      elseif ($data->tipomantenimiento==2)
        $estado = "Correctivo";
      else
        $estado = "Indefinido";
      
      $mensaje.="<tr>";
      $mensaje.="<td style='text-align:center'>1</td>";
      $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($data->fecharegistro))."</td>";
      if($data->fechamantenimiento!=""){
        $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($data->fechamantenimiento))."</td>";
      }else{
        $mensaje.="<td style='text-align:center'> - </td>";
      }
      $mensaje.="<td style='text-align:center'>".utf8_decode($data->titulo)."</td>";
      $mensaje.="<td style='text-align:center'>".utf8_decode($data->taller)."</td>";
      $mensaje.="<td style='text-align:center'>".$data->ordencompra."</td>";
      $mensaje.="<td style='text-align:center'>".$data->placa."</td>";
      $mensaje.="<td style='text-align:center'>".$data->kmman."</td>";
      $mensaje.="<td style='text-align:center'>".$data->kminicial."</td>";
      $mensaje.="<td style='text-align:center'>".$data->kmfinal."</td>";
      $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($data->fechaentrada))."</td>";
      $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($data->fechasalida))."</td>";
      $mensaje.="<td style='text-align:center'>".utf8_decode($estado)."</td>";
      $mensaje.="<td style='text-align:center'>".($data->situacion=='F'?'Finalizado':'Pendiente')."</td>";
      $mensaje.="<td style='text-align:center'>".$data->usuario."</td>";
      $mensaje.="</tr>";
      $mensaje.="</table>";

    //$usuario=$data->lider;
      $usuario="";
    $mail->Subject = "ALERTA MANTENIMIENTO VEHICULAR REGISTRADO";
    $url = 'https://sistemasolmos.pe/cev/';

    $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION MANT. VEHICULAR</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimados(a): '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><p>
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
