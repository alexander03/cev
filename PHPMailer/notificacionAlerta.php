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
  $fecha_actual = date("Y-m-d");
  $fecha_limite = date("Y-m-d",strtotime($fecha_actual."+ 1 month"));
  $rst = $conexion->obtenerDataSQL("select * from (
  (select vehiculo_id , tipo , MAX(fecha) as fecha,co.abreviatura as concesionaria ,'vehiculo' as tipo2
  from `vehiculodocument` 
  inner join vehiculo on `vehiculodocument`.`vehiculo_id` = `vehiculo`.`id`
  inner join concesionaria co on co.id=vehiculo.concesionaria_id 
  where `vehiculo`.`deleted_at` is null and `vehiculodocument`.`deleted_at` is null and vehiculo.contratista_id in (12,27,38) and vehiculo.estado not in ('Desmovilizado') 
  group by vehiculo_id , tipo,co.razonsocial
  having (fecha >= '$fecha_actual' and fecha <= '$fecha_limite') or fecha<'$fecha_actual'
  order by co.razonsocial asc,vehiculo_id asc)
  union all 
  (select equipo_id as vehiculo_id , tipo , MAX(fecha) as fecha,co.abreviatura as concesionaria ,'equipo' as tipo2
  from `equipodocument` 
  inner join equipo on `equipodocument`.`equipo_id` = `equipo`.`id`
  inner join concesionaria co on co.id=equipo.concesionaria_id 
  where `equipo`.`deleted_at` is null and `equipodocument`.`deleted_at` is null and equipo.contratista_id in (12,27,38) and equipo.estado not in ('Desmovilizado') 
  group by equipo_id , tipo,co.razonsocial
  having (fecha >= '$fecha_actual' and fecha <= '$fecha_limite') or fecha<'$fecha_actual'
  order by co.razonsocial asc,equipo_id asc)
  ) as T");
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
    
    $mensaje="Para reportar el vencimiento de: <br /><br />";
    $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                  <tr>
                    <th style='border-style: solid; border-color: black;'>NRO</th>
                    <th style='border-style: solid; border-color: black;'>PLACA</th>
                    <th style='border-style: solid; border-color: black;'>MODELO</th>
                    <th style='border-style: solid; border-color: black;'>CARROCERIA</th>
                    <th style='border-style: solid; border-color: black;'>FECHA VENC.</th>
                    <th style='border-style: solid; border-color: black;'>TIPO</th>
                    <th style='border-style: solid; border-color: black;'>CONCESIONARIA</th>
                    <th style='border-style: solid; border-color: black;'>COMENTARIO</th>
                  </tr>
            ";
      $c=0;
      while($data = $rst->fetchObject()){$c++;
        if($data->tipo2=="vehiculo"){
          $vehiculo = $conexion->obtenerDataSQL("select v.*,ca.descripcion as carroceria 
                  from vehiculo v 
                  inner join carroceria ca on ca.id=v.carroceria_id
                  where v.id=".$data->vehiculo_id)->fetchObject();
          $data2 = $conexion->obtenerDataSQL("select v.comentario,users.name as usercomentario
                  from vehiculodocument v 
                  left join users on users.id=v.usercomentario_id
                  where v.vehiculo_id=".$data->vehiculo_id." and v.tipo like '".$data->tipo."' and v.fecha='".$data->fecha."' and v.deleted_at is null")->fetchObject();
        }else{
          $vehiculo = $conexion->obtenerDataSQL("select v.*,v.descripcion as carroceria
                  from equipo v 
                  where v.id=".$data->vehiculo_id)->fetchObject();
          $data2 = $conexion->obtenerDataSQL("select v.comentario,users.name as usercomentario
                  from equipodocument v 
                  left join users on users.id=v.usercomentario_id
                  where v.equipo_id=".$data->vehiculo_id." and v.tipo like '".$data->tipo."' and v.fecha='".$data->fecha."' and v.deleted_at is null")->fetchObject();
        }
        $mensaje.="<tr>";
        $mensaje.="<td style='text-align:center'>".$c."</td>";
        $mensaje.="<td style='text-align:center'>".$vehiculo->placa."</td>";
        $mensaje.="<td style='text-align:center'>".utf8_decode($vehiculo->modelo)."</td>";
        $mensaje.="<td style='text-align:center'>".utf8_decode($vehiculo->carroceria)."</td>";
        $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($data->fecha))."</td>";
        $mensaje.="<td style='text-align:center'>".$data->tipo."</td>";
        $mensaje.="<td style='text-align:center'>".$data->concesionaria."</td>";    
        $mensaje.="<td style='text-align:center'>".utf8_decode($data2->comentario.(!is_null($data2->usercomentario)?(" (".$data2->usercomentario.")"):''))."</td>";    
        $mensaje.="</tr>";
      }
      $mensaje.="</table>";

    //$usuario=$data->lider;
      $usuario="";
    $mail->Subject = "ALERTA SOAT / RTV / POLIZA";
    
    $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION SOAT / RTV / POLIZA VEHICULAR</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimados(a): '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
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
