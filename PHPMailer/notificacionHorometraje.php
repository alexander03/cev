<?php
require_once('class.phpmailer.php');
include_once("cado.php");
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$conexion = new clsAccesoDatos();
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP
$correos = $conexion->obtenerDataSQL("select * from configuracion where id=8")->fetchObject()->valor;

try {
  $rst = $conexion->obtenerDataSQL("select equipo.*,horometraje.limite_inf,horometraje.descripcion as regla,horometraje.limite_sup,concesionaria.abreviatura as concesionaria 
  	from equipo 
  	inner join concesionaria on concesionaria.id=equipo.concesionaria_id
  	inner join horometraje on horometraje.id = equipo.horometraje_id where equipo.deleted_at is null and equipo.contratista_id in (12,27,38)");
  if($rst->rowCount()>0){
    //$listaCorreo = explode(";","javiergonzales@h2cto.com;vcaceres@h2cto.com;mquezada@h2cto.com;clunam@h2cto.com");//$data->correolider.";
    $listaCorreo = explode(";",$correos);
    //$listaCorreo = explode(";","alex_3849@hotmail.com;javiergonzales@h2cto.com");
    
    $mensaje="Para reportar el detalle del siguiente recorrido: <br /><br />";
    $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                  <tr>
                    <th style='border-style: solid; border-color: black;'>NRO</th>
                    <th style='border-style: solid; border-color: black;'>CONCESIONARIA</th>
                    <th style='border-style: solid; border-color: black;'>REGLA KM</th>
                    <th style='border-style: solid; border-color: black;'>MODELO</th>
                    <th style='border-style: solid; border-color: black;'>PLACA</th>
                    <th style='border-style: solid; border-color: black;'>HOROMETRO INICIAL DE CONTROL</th>
                    <th style='border-style: solid; border-color: black;'>HOROMETRO RECORRIDO</th>
                    <th style='border-style: solid; border-color: black;'>HOROMETRO ACTUAL(CONTR/COMB)</th>
                    <th style='border-style: solid; border-color: black;'>DIFERENCIA HOROMETRO</th>
                    <th style='border-style: solid; border-color: black;'>ESTADO</th>
                    <th style='border-style: solid; border-color: black;'>COMENTARIO</th>
                    <th style='border-style: solid; border-color: black;'>ESTADO MANT.</th>
                  </tr>
            ";
      $c=0;
      while($data = $rst->fetchObject()){
        $id = $data->id;
        $horometro_rec = $data->horometroactual - $data->horometroinicial;
        if( $horometro_rec > $data->limite_inf ){$c++;
          
          if( $horometro_rec < $data->limite_inf ){
            $color="";
          }elseif( $horometro_rec < $data->limite_sup ){
            $color="";
          }else{
            $color="color:red";
          }
          
          $mensaje.="<tr style='".$color."'>";
          $mensaje.="<td style='text-align:center'>".$c."</td>";
          $mensaje.="<td style='text-align:center'>".$data->concesionaria."</td>";
          $mensaje.="<td style='text-align:center'>".utf8_decode($data->regla)."</td>";
          $mensaje.="<td style='text-align:center'>".$data->modelo."</td>";
          $mensaje.="<td style='text-align:center'>".$data->placa."</td>";
          $mensaje.="<td style='text-align:center'>".$data->horometroinicial."</td>";
          $mensaje.="<td style='text-align:center'>".$horometro_rec."</td>";
          $mensaje.="<td style='text-align:center'>".number_format($data->horometroactual,2,'.','')."</td>";
          $mensaje.="<td style='text-align:center'>".number_format($data->limite_sup - $horometro_rec,2,'.','')."</td>";
          if( $horometro_rec < $data->limite_inf ){
            $mensaje.="<td > Normal </td>";
          }elseif( $horometro_rec < $data->limite_sup ){
            $mensaje.=utf8_decode("<td> Observación </td>");
          }else{
            $mensaje.="<td> Desgastado </td>";
          }
          $mensaje.="<td style='text-align:center'>".utf8_decode($data->comentario)."</td>";
          $mensaje.="<td style='text-align:center'>".$data->estadorecorrido."</td>";
          $mensaje.="</tr>";
        }
      }
      $mensaje.="</table>";
    if($c>0){
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
    

      //$usuario=$data->lider;
        $usuario="";
      $mail->Subject = "ALERTA DE RECOORIDO HOROMETRO VEHICULOS PESADOS";
      
      $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION RECORRIDO HOROMETRO VEHICULAR</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimados(a): '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
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
      echo "Not data for send email";
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
