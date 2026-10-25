<?php
require_once('class.phpmailer.php');
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP

try {
  $listaCorreo = explode("|",$_GET["correo"]);
  //$listaImagen = explode("|",$_GET["imagenes"]);
  $mail = new phpmailer();
  //$mail->SMTPDebug = 2;
  $mail->IsSMTP();
  $mail->SMTPAuth = true;
  $mail->SMTPSecure = 'tls';
  $mail->Host = 'smtp-mail.outlook.com';
  $mail->Port = 587; 
  $mail->Username = 'notih2cto@outlook.com';
  $mail->Password = 'H2cto2023###';
  $mail->SetFrom('notih2cto@outlook.com', 'SGISOFT - Notificaciones');
  
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
