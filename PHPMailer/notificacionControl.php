<?php
require_once('class.phpmailer.php');
include_once("cado.php");
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$conexion = new clsAccesoDatos();
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP
date_default_timezone_set("America/Lima");
$fecha2 = date("Y-m-d");
$tipo = $conexion->obtenerDataSQL("select * from control where id=".$_GET["id"])->fetchObject()->concesionaria_id;
if($tipo=="1"){
  $correos = $conexion->obtenerDataSQL("select * from configuracion where id=9")->fetchObject()->valor;
}else{
  $correos = $conexion->obtenerDataSQL("select * from configuracion where id=10")->fetchObject()->valor;
}
try {
  //NO KM FINAL
  $rst = $conexion->obtenerDataSQL("select co.*,ga.nombre as garita,tu.nombre as turno,concat(ag.apellidos,' ',ag.nombres) as agente,con.abreviatura as concesionaria
          from control co
          inner join garita ga on ga.id=co.garita_id
          inner join turno tu on tu.id=co.turno_id
          inner join concesionaria con on con.id=co.concesionaria_id
          inner join agente ag on ag.id=co.agente_id
          where co.deleted_at is null and co.id=".$_GET["id"]." order by co.created_at desc");
  $dat = $rst->fetchObject();
  //print_r($dat);
  //exit();
      //$listaCorreo = explode(";","javiergonzales@h2cto.com;vcaceres@h2cto.com;mquezada@h2cto.com;clunam@h2cto.com");
      $listaCorreo = explode(";",$correos);//$k.
      //$listaCorreo = explode(";","alex_3849@hotmail.com;javiergonzales@h2cto.com");
      $mail = new phpmailer();
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
      
      $mensaje="Para reportar: <br /><br />";
      $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                <tr>
                  <th style='border-style: solid; border-color: black;'>NRO</th>
                  <th style='border-style: solid; border-color: black;'>GARITA</th>
                  <th style='border-style: solid; border-color: black;'>FECHA</th>
                  <th style='border-style: solid; border-color: black;'>HORA</th>
                  <th style='border-style: solid; border-color: black;'>TIPO</th>
                  <th style='border-style: solid; border-color: black;'>PLACA</th>
                  <th style='border-style: solid; border-color: black;'>KM</th>
                  <th style='border-style: solid; border-color: black;'>ORIGEN</th>
                  <th style='border-style: solid; border-color: black;'>DESTINO</th>
                  <th style='border-style: solid; border-color: black;'>AGENTE</th>
                  <th style='border-style: solid; border-color: black;'>CONDUCTOR</th>
                  <th style='border-style: solid; border-color: black;'>PASAJEROS</th>
                  <th style='border-style: solid; border-color: black;'>EMPRESA</th>
                  <th style='border-style: solid; border-color: black;'>OBSERVACION</th>
                  <th style='border-style: solid; border-color: black;'>CONCESIONARIA</th>
                </tr>
              ";
      $rst2 = $conexion->obtenerDataSQL("select * from detallecontrol where control_id=".$_GET["id"]);
      $personal = "";$placa="";$km="";$empresa="";$conductor="";
      while ($value = $rst2->fetchObject()) {
        if($value->conductor=="N"){
          $personal .=" * ".$value->persona." <br />";
          $placa = $value->vehiculo;
          $km = $value->km;
          $empresa .=" * ".$value->empresa." <br />";
        }else{
          $conductor =$value->persona;
          $placa = $value->vehiculo;
          $km = $value->km;
        }
      }
      $mensaje.="<tr>";
      $mensaje.="<td style='text-align:center'>1</td>";
      $mensaje.="<td style='text-align:center'>".$dat->garita."</td>";
      $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($dat->fecha))."</td>";
      $mensaje.="<td style='text-align:center'>".date("h:i A",strtotime($dat->fecha))."</td>";
      $mensaje.="<td style='text-align:center'>".$dat->tipo."</td>";
      $mensaje.="<td style='text-align:center'>".utf8_decode($placa)."</td>";
      $mensaje.="<td style='text-align:center'>".utf8_decode($km)."</td>";
      $mensaje.="<td style='text-align:center'>".utf8_decode($dat->origen)."</td>";
      $mensaje.="<td style='text-align:center'>".utf8_decode($dat->destino)."</td>";
      $mensaje.="<td style='text-align:center'>".$dat->agente."</td>";
      $mensaje.="<td style='text-align:center'>".$conductor."</td>";
      $mensaje.="<td style='text-align:center'>".utf8_decode($personal)."</td>";
      $mensaje.="<td style='text-align:center'>".$empresa."</td>";
      $mensaje.="<td style='text-align:center'>".utf8_decode($dat->observacion)."</td>";
      $mensaje.="<td style='text-align:center'>".$dat->concesionaria."</td>";
      $mensaje.="</tr>";
        
      $mensaje.="</table>";
      $usuario="";
      $mail->Subject = "CONTROL GARITA DE CAMPAMENTO";
      
      $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="140" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION CONTROL GARITA DE CAMPAMENTO</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimado(a), '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
      $mail->AltBody = $html; // optional - MsgHTML will create an alternate automatically
      $mail->MsgHTML($html);
      if(!$mail->Send()) {
        echo "Error while sending Email.";
        echo $mail->ErrorInfo;
        print_r($mail);
      } else {
        echo "Email sent successfully;<script>window.close();</script>";
      }
  
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>

</body>
</html>
