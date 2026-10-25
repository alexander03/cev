<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as phpmailerException;
include_once("cado.php");

$conexion = new clsAccesoDatos();
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP
$correos = $conexion->obtenerDataSQL("select * from configuracion where id=7")->fetchObject()->valor;

try {
  $rst = $conexion->obtenerDataSQL("select ch.*,c.nombres,c.apellidos,ua.codigo as ua,case when ch.vehiculo_id is null then e.placa else v.placa end as placa,l.correo as correolider,l.nombres as lider,e.descripcion as equipo_descripcion,v.modelo,co.abreviatura as concesionaria
          from checklistvehicular ch 
          left join ua on ua.id=ch.ua_id
          left join conductor c on c.id=ch.conductor_id
          left join vehiculo v on v.id=ch.vehiculo_id
          left join equipo e on e.id=ch.equipo_id
          left join lider l on l.id = c.lider_id
          inner join concesionaria co on co.id=ch.concesionaria_id
          where ch.deleted_at is null and ch.fecha_registro = '".date("Y-m-d",strtotime('-1 day'))."' and 
          (ch.sistema_electrico like '%\"estado\":false%' or ch.sistema_mecanico like '%\"estado\":false%' or ch.accesorios like '%\"estado\":false%' or ch.documentos like '%\"estado\":false%' or ch.observaciones!='' or ch.incidentes!='')
          order by ch.created_at desc");
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
    
    $mensaje="Para reportar el detalle del siguiente checklist vehicular: <br /><br />";
    $mensaje.="<table border='1' width=100% cellpadding=0 cellspacing=0>
                  <tr>
                    <th style='border-style: solid; border-color: black;'>NRO</th>
                    <th style='border-style: solid; border-color: black;'>FECHA REGISTRO</th>
                    <th style='border-style: solid; border-color: black;'>MODELO/PLACA</th>
                    <th style='border-style: solid; border-color: black;'>HORA INI</th>
                    <th style='border-style: solid; border-color: black;'>HORA FIN</th>
                    <th style='border-style: solid; border-color: black;'>KM INI</th>
                    <th style='border-style: solid; border-color: black;'>KM FIN</th>
                    <th style='border-style: solid; border-color: black;'>CONDUCTOR</th>
                    <th style='border-style: solid; border-color: black;'>REVISION</th>
                    <th style='border-style: solid; border-color: black;'>OBSERVACIONES</th>
                    <th style='border-style: solid; border-color: black;'>INCIDENTES</th>
                    <th style='border-style: solid; border-color: black;'>CONCESIONARIA</th>
                  </tr>
            ";
      $xx=0;
      while($data = $rst->fetchObject()){$xx++;
        $revisiones="";
        $sistema_electrico = json_decode($data->sistema_electrico);
        $sistema_mecanico = json_decode($data->sistema_mecanico);
        $accesorios = json_decode($data->accesorios);
        $documentos = json_decode($data->documentos);
        foreach ($sistema_electrico as $item){
          if($item->estado=='0'){
            $revisiones .= '* '.$item->titulo."<br />";
          }
        }
        foreach ($sistema_mecanico as $item){
          if($item->estado=='0'){
            $revisiones .= '* '.$item->titulo."<br />";
          }
        }
        foreach ($accesorios as $item){
          if($item->estado=='0'){
            $revisiones .= '* '.$item->titulo."<br />";
          }
        }
        foreach ($documentos as $item){
          if($item->estado=='0'){
            $revisiones .= '* '.$item->titulo."<br />";
          }
        }
        $mensaje.="<tr>";
        $mensaje.="<td style='text-align:center'>$xx</td>";
        $mensaje.="<td style='text-align:center'>".date("d/m/Y",strtotime($data->fecha_registro))."</td>";
        $mensaje.="<td style='text-align:center'>".utf8_decode($data->equipo_descripcion."".$data->modelo)." / ".$data->placa."</td>";
        $mensaje.="<td style='text-align:center'>".$data->hora_inicio."</td>";
        $mensaje.="<td style='text-align:center'>".$data->hora_fin."</td>";
        $mensaje.="<td style='text-align:center'>".$data->k_inicial."</td>";
        $mensaje.="<td style='text-align:center'>".$data->k_final."</td>";
        $mensaje.="<td>".$data->apellidos." ".$data->nombres."</td>";
        $mensaje.="<td>".utf8_decode($revisiones)."</td>";
        $mensaje.="<td>".utf8_decode($data->observaciones)."</td>";
        $mensaje.="<td>".utf8_decode($data->incidentes)."</td>";
        $mensaje.="<td>".utf8_decode($data->concesionaria)."</td>";
        $mensaje.="</tr>";
      }
      $mensaje.="</table>";

    //$usuario=$data->lider;
      $usuario="";
    $mail->Subject = "ALERTA CHECKLIST VEHICULAR REVISION / INCIDENTE / OBSERVACION";
    
    $html = '<font face="Segoe UI"><table style="margin: 0 auto; width: 100%;"><tbody><tr><td align="center" valign="middle"><img style="display: block; margin-left: auto; margin-right: auto;" src="https://sistemasolmos.pe/cev/public/assets/img/logo_correo.png" alt="" width="160" height="100"></td></tr><tr><td style="text-align: center; color: #6482AF;"><h1><strong>NOTIFICACION CHECKLIST VEHICULAR</strong></h1><p>&nbsp;</p></td></tr><tr><th style="border-top: #3d528b 3px solid;" align="left"><p>&nbsp;</p>Estimados(a): '.$usuario.'<br /><br /></th></tr><tr><td>'.$mensaje.'<p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td style="text-align: center; border-top: #3d528b 3px solid; color: #737373;"><h5>                        &#169 Tecnolog&iacute;as de la Informaci&oacute;n - Concesiones Irrigaci&oacute;n                  </h5>                    <p>&nbsp;</p>                </td>            </tr>        </tbody>    </table></font>';
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
