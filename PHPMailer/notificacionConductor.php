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
$correos = $conexion->obtenerDataSQL("select * from configuracion where id=8")->fetchObject()->valor;
try {
  //NO KM FINAL
  $rst = $conexion->obtenerDataSQL("select co.*,con.razonsocial as contratista
          from conductor co 
          inner join contratista con on con.id=co.contratista_id
          inner join conductorconcesionaria cc on cc.conductor_id=co.id
          where co.deleted_at is null and co.tipo like 'Conductor' and cc.estado =1 and co.contratista_id in (27,12,38) order by co.nombres,co.apellidos desc");
  
      //$listaCorreo = explode(";","javiergonzales@h2cto.com;vcaceres@h2cto.com;mquezada@h2cto.com;clunam@h2cto.com");
      $listaCorreo = explode(";",$correos);//$k.
      //$listaCorreo = explode(";","alex_3849@hotmail.com;javiergonzales@h2cto.com");
      $band = false;
      
      $mensaje="Para reportar que los siguientes conductores, presentan licencia de conducir vencida: <br /><br />";
      $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                <tr>
                  <th style='border-style: solid; border-color: black;'>NRO</th>
                  <th style='border-style: solid; border-color: black;'>NOMBRES</th>
                  <th style='border-style: solid; border-color: black;'>APELLIDOS</th>
                  <th style='border-style: solid; border-color: black;'>DNI</th>
                  <th style='border-style: solid; border-color: black;'>CATEGORIA</th>
                  <th style='border-style: solid; border-color: black;'>LICENCA</th>
                  <th style='border-style: solid; border-color: black;'>FEC. VENC. LIC.</th>
                  <th style='border-style: solid; border-color: black;'>CONTRATISTA</th>
                </tr>
              ";
      $dataDNI = array();
      $c=0;
      while ($dat = $rst->fetchObject()) {
        if(strtotime($dat->fechavencimiento)<strtotime('now') && !isset($dataDNI[$dat->dni])){$c++;
          $band=true;
          $mensaje.="<tr>";
          $mensaje.="<td style='text-align:center'>".$c."</td>";
          $mensaje.="<td style='text-align:center'>".$dat->nombres."</td>";
          $mensaje.="<td style='text-align:center'>".$dat->apellidos."</td>";
          $mensaje.="<td style='text-align:center'>".$dat->dni."</td>";
          $mensaje.="<td style='text-align:center'>".$dat->categoria."</td>";
          $mensaje.="<td style='text-align:center'>".$dat->licencia."</td>";
          $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($dat->fechavencimiento))."</td>";
          $mensaje.="<td style='text-align:center'>".$dat->contratista."</td>";
          $mensaje.="</tr>";
          $dataDNI[$dat->dni] = $dat->dni;
        }
      }
      $mensaje.="</table>";
      //$usuario=$lider;
      $usuario="";

      if($band){
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
        

        $mail->Subject = "ALERTA VENCIMIENTO DE LICENCIAS DE CONDUCIR";
        
        $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION VENC. LICENCIAS DE CONDUCIR</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimado(a), '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
        $mail->AltBody = $html; // optional - MsgHTML will create an alternate automatically
        $mail->MsgHTML($html);
        if(!$mail->Send()) {
          echo "Error while sending Email.";
          echo $mail->ErrorInfo;
          print_r($mail);
        } else {
          echo "Email sent successfully;<script>window.close();</script>";
        }
      }else{
        echo "Not data for email;<script>window.close();</script>";
      }
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>

</body>
</html>
