<?php
require_once('class.phpmailer.php');
include_once("cado.php");
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$conexion = new clsAccesoDatos();
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP
date_default_timezone_set("America/Lima");
$fecha2 = date("Y-m-d");
$data = $conexion->obtenerDataSQL("select u.*
    from users u
    where u.id=".$_GET["id"])->fetchObject();
$correos = $data->correo;
if(trim($correos)==""){
  $correos = $_GET["correo"];
}
try {
      $listaCorreo = explode(";",$correos);
      //$listaCorreo = explode(";","alex_3849@hotmail.com;javiergonzales@h2cto.com");
      $mail = new phpmailer();
      //$mail->SMTPDebug = 2;
      $mail->IsSMTP();
      //$mail->SMTPDebug = true;
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
      
      $mensaje="Siguen tus credenciales: <br /><br />";
      $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                <tr>
                  <th style='border-style: solid; border-color: white;color:white;background:#305496'>CUENTA</th>
                  <th style='border-style: solid; border-color: white;color:white;background:#305496'>CONTRASE&Ntilde;A</th>
                  <th style='border-style: solid; border-color: white;color:white;background:#305496'>LINK</th>
                </tr>
              ";
        $mensaje.="<tr>";
        $mensaje.="<td style='text-align:center;background:#D6DCE4'>".utf8_decode($data->username)."</td>";
        $mensaje.="<td style='text-align:center;background:#D6DCE4'>".utf8_decode($_GET["pass"])."</td>";
        $mensaje.="<td style='text-align:center;background:#D6DCE4'>https://sistemasolmos.pe/cev/</td>";
        $mensaje.="</tr>";
        
      $mensaje.="</table><br />";
      $usuario= $data->name;
      $mail->Subject = "ACCESOS CEV";
      
      $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="140" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION ACCESOS AL CEV</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimado(a), '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
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
