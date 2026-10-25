<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as phpmailerException;
include_once("cado.php");

$conexion = new clsAccesoDatos();
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP
date_default_timezone_set("America/Lima");
$fecha2 = date("Y-m-d");
$correos = $conexion->obtenerDataSQL("select * from configuracion where id=7")->fetchObject()->valor;
try {
  //NO KM FINAL
  $rst = $conexion->obtenerDataSQL("select ch.*,c.nombres,c.apellidos,ua.codigo as ua,case when ch.vehiculo_id is null then e.placa else v.placa end as placa,l.correo as correolider,l.nombres as lider,e.descripcion as equipo_descripcion,v.modelo
          from checklistvehicular ch 
          inner join ua on ua.id=ch.ua_id
          inner join conductor c on c.id=ch.conductor_id
          left join vehiculo v on v.id=ch.vehiculo_id
          left join equipo e on e.id=ch.equipo_id
          left join lider l on l.id = c.lider_id
          where ch.deleted_at is null and ch.k_final is null order by ch.created_at desc");
  $data["javier"] = array();$band=false;
  while ($dat = $rst->fetchObject()) {
    /*if(is_null($data[$dat->correolider])){
      $data[$dat->correolider] = array();
    }*/
    $band=true;
    $data["javier"][] = array("correo" => $dat->correolider,
              "fecha_registro" => $dat->fecha_registro,
              "placa" => ($dat->equipo_descripcion."".$dat->modelo)." / ".$dat->placa,
              "conductor" => $dat->nombres." ".$dat->apellidos,
              "horaini" => $dat->hora_inicio,
              "horafin" => $dat->hora_fin,
              "km_inicio" => $dat->k_inicial,
              "km_final" => $dat->k_final,
              "lider" => $dat->lider,
            );
  }
  //print_r($data);
  //exit();
  if(count($data)>0 && $band){
    foreach($data as $k=>$v){
      //$listaCorreo = explode(";","javiergonzales@h2cto.com;vcaceres@h2cto.com;mquezada@h2cto.com;clunam@h2cto.com");
      $listaCorreo = explode(";",$correos);//$k.
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
      $mail->SetFrom('noticevh2cto', 'CEV - Notificaciones');
      
      for ($i=0; $i < count($listaCorreo) ; $i++) { 
        $mail->AddAddress($listaCorreo[$i]);
      }
      
      $mensaje="Para reportar que el kilometraje final del siguiente checklist vehicular no ha sido registrado: <br /><br />";
      $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                <tr>
                  <th style='border-style: solid; border-color: black;'>NRO</th>
                  <th style='border-style: solid; border-color: black;'>FECHA REGISTRO</th>
                  <th style='border-style: solid; border-color: black;'>HORA INI</th>
                  <th style='border-style: solid; border-color: black;'>HORA FIN</th>
                  <th style='border-style: solid; border-color: black;'>KM INI</th>
                  <th style='border-style: solid; border-color: black;'>KM FIN</th>
                  <th style='border-style: solid; border-color: black;'>MODELO/PLACA</th>
                  <th style='border-style: solid; border-color: black;'>CONDUCTOR</th>
                </tr>
              ";
      $c=0;
      foreach ($v as $xx=>$yy) {$c++;
        $mensaje.="<tr>";
        $mensaje.="<td style='text-align:center'>".$c."</td>";
        $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($yy["fecha_registro"]))."</td>";
        $mensaje.="<td style='text-align:center'>".$yy["horaini"]."</td>";
        $mensaje.="<td style='text-align:center'>".$yy["horafin"]."</td>";
        $mensaje.="<td style='text-align:center'>".$yy["km_inicio"]."</td>";
        $mensaje.="<td style='text-align:center;background:#ff00004f;'>".$yy["km_final"]."</td>";
        $mensaje.="<td style='text-align:center'>".$yy["placa"]."</td>";
        $mensaje.="<td>".$yy["conductor"]."</td>";
        $mensaje.="</tr>";
        $lider = $yy["lider"];
      }
      $mensaje.="</table>";
      //$usuario=$lider;
      $usuario="";
      $mail->Subject = "ALERTA CHECKLIST VEHICULAR KM FINAL";
      
      $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION CHECKLIST VEHICULAR</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimado(a), '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
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
  }
  //NO REGISTRADO CHECKLIST
  $rst2 = $conexion->obtenerDataSQL("select v.*,ca.descripcion as carroceria,co.razonsocial as concesionaria
        from vehiculo v
        inner join carroceria ca on ca.id=v.carroceria_id
        inner join concesionaria co on co.id=v.concesionaria_id
        where v.id not in (select vehiculo_id from checklistvehicular where checklistvehicular.deleted_at is null and checklistvehicular.fecha_registro='".$fecha2."' and vehiculo_id is not null) and v.deleted_at is null and v.contratista_id in (12,27,38) and v.estado not in ('Desmovilizado')");
  
  $data = array();
  $data["javier"] = array();$band=false;
  while ($dat = $rst2->fetchObject()) {
    $data2 = $conexion->obtenerDataSQL("select c.nombres,c.apellidos, l.correo as correolider,l.nombres as lider,(select fecha_registro from checklistvehicular where vehiculo_id=".$dat->id." order by fecha_registro desc limit 1) as fecha_anterior 
        from conductor c
        left join lider l on l.id = c.lider_id
        where c.id=(select checklistvehicular.conductor_id from checklistvehicular where checklistvehicular.vehiculo_id=".$dat->id." and checklistvehicular.deleted_at is null order by checklistvehicular.fecha_registro desc,checklistvehicular.hora_fin desc limit 1) limit 1");
    if($data2->rowCount()>0){
      $data2 = $data2->fetchObject();
      //if(is_null($data[$data2->correolider])){
        //$data[$data2->correolider] = array();
      //}$data2->correolider
      $band=true;
      $data["javier"][] = array("correo" => $data2->correolider,
                        "placa" => ($dat->modelo)." / ".$dat->placa,
                        "carroceria" => $dat->carroceria,
                        "concesionaria" => $dat->concesionaria,
                        "conductor" => $data2->nombres." ".$data2->apellidos,
                        "lider" => $data2->lider,
                        "fecha_anterior" => $data2->fecha_anterior,
                    );
    }
  }
  $rst3 = $conexion->obtenerDataSQL("select e.*,e.descripcion as carroceria,co.razonsocial as concesionaria
        from equipo e
        inner join concesionaria co on co.id=e.concesionaria_id
        where e.id not in (select equipo_id from checklistvehicular where checklistvehicular.deleted_at is null and checklistvehicular.fecha_registro='".$fecha2."' and equipo_id is not null) and e.deleted_at is null and e.contratista_id in (12,27,38)  and e.estado not in ('Desmovilizado')");
  
  while ($dat = $rst3->fetchObject()) {
    $data2 = $conexion->obtenerDataSQL("select c.nombres,c.apellidos, l.correo as correolider,l.nombres as lider,(select fecha_registro from checklistvehicular where equipo_id=".$dat->id." order by fecha_registro desc limit 1) as fecha_anterior 
        from conductor c
        left join lider l on l.id = c.lider_id
        where c.id=(select checklistvehicular.conductor_id from checklistvehicular where checklistvehicular.equipo_id=".$dat->id." and checklistvehicular.deleted_at is null order by checklistvehicular.fecha_registro desc,checklistvehicular.hora_fin desc limit 1) limit 1");
    if($data2->rowCount()>0){
      $data2 = $data2->fetchObject();
      $band=true;
      $data["javier"][] = array("correo" => $data2->correolider,
                        "placa" => ($dat->modelo)." / ".$dat->placa,
                        "carroceria" => $dat->carroceria,
                        "concesionaria" => $dat->concesionaria,
                        "conductor" => $data2->nombres." ".$data2->apellidos,
                        "lider" => $data2->lider,
                        "fecha_anterior" => $data2->fecha_anterior,
                    );
    }
  }
  //print_r($data);
  //exit();
  if(count($data)>0 && $band){
    foreach($data as $k=>$v){
      //$listaCorreo = explode(";","javiergonzales@h2cto.com;vcaceres@h2cto.com;mquezada@h2cto.com;clunam@h2cto.com");
      $listaCorreo = explode(";",$correos);//$k.
      //$listaCorreo = explode(";","alex_3849@hotmail.com;javiergonzales@h2cto.com;");
      $mail = new PHPMailer();
      //$mail->SMTPDebug = 2;
      $mail->IsSMTP();
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = 'ssl';
      $mail->Host = 'smtp.gmail.com';
      $mail->Port = 465; 
      $mail->Username = 'noticevh2cto@gmail.com';
      $mail->Password = 'ftlomfkmxcbqjoiy'; 
      $mail->SetFrom('noticevh2cto', 'CEV - Notificaciones');
      
      for ($i=0; $i < count($listaCorreo) ; $i++) { 
        $mail->AddAddress($listaCorreo[$i]);
      }
      
      $mensaje=utf8_decode("Para reportar que no existe Checklist de: <br /><br />");
      $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                <tr>
                  <th style='border-style: solid; border-color: black;'>NRO</th>
                  <th style='border-style: solid; border-color: black;'>FECHA</th>
                  <th style='border-style: solid; border-color: black;'>MODELO/PLACA</th>
                  <th style='border-style: solid; border-color: black;'>CARROCERIA</th>
                  <th style='border-style: solid; border-color: black;'>ULTIMO CONDUCTOR</th>
                  <th style='border-style: solid; border-color: black;'>CONCESIONARIA</th>
                  <th style='border-style: solid; border-color: black;'>FECHA ULTIMO CHECKLIST</th>
                </tr>
              ";
      $c=0;
      foreach ($v as $xx=>$yy) {$c++;
        $mensaje.="<tr>";
        $mensaje.="<td style='text-align:center'>".$c."</td>";
        $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($fecha2))."</td>";
        $mensaje.="<td style='text-align:center'>".utf8_decode($yy["placa"])."</td>";
        $mensaje.="<td style='text-align:center'>".$yy["carroceria"]."</td>";
        $mensaje.="<td style='text-align:center'>".$yy["conductor"]."</td>";
        $mensaje.="<td style='text-align:center'>".$yy["concesionaria"]."</td>";
        $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($yy["fecha_anterior"]))."</td>";
        $mensaje.="</tr>";
        $lider = $yy["lider"];
      }
      $mensaje.="</table>";
      //$usuario=$lider;
      $usuario="";
      $mail->Subject = "ALERTA CHECKLIST VEHICULAR NO REGISTRADO";
      
      $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION CHECKLIST VEHICULAR NO REGISTRADO</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimados, '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
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
  }
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>

</body>
</html>
