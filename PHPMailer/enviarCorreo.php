<?php
require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as phpmailerException;

$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP

try {
  $listaCorreo = explode("|",$_GET["correo"]);
  //$listaImagen = explode("|",$_GET["imagenes"]);
  $mail = new PHPMailer();
  //$mail->SMTPDebug = 2;
  $mail->IsSMTP();
  $mail->SMTPAuth = true;
  $mail->SMTPSecure = env('OUTLOOK_MAIL_ENCRYPTION');
  $mail->Host = env('OUTLOOK_MAIL_HOST');
  $mail->Port = (int) env('OUTLOOK_MAIL_PORT');
  $mail->Username = env('OUTLOOK_MAIL_USERNAME');
  $mail->Password = env('OUTLOOK_MAIL_PASSWORD');
  $mail->SetFrom(env('OUTLOOK_MAIL_FROM_ADDRESS'), 'SGISOFT - Notificaciones');
  
  for ($i=0; $i < count($listaCorreo) ; $i++) { 
    $mail->AddAddress($listaCorreo[$i]);
  }
  
  $mensaje=$_GET["mensaje"];
  $usuario=$_GET["usuario"];
  $mail->Subject = "Notificacion SGI";
  
  $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="http://www.h2olmos.pe/sgisoft/dist/logo_images/logo-cto.jpeg" alt="" width="250" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION SGI</strong></h1><p>&nbsp;</p></td></tr><tr><th align="left">Estimado(a),'.$usuario.'<br/></th></tr><tr><td>'.$mensaje.'</td></tr></table></font>';
  $mail->AltBody = $html; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML($html);
  if(!$mail->Send()) {
    echo "Error while sending Email.";
    echo $mail->ErrorInfo;
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
