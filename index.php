<?php
function check($ip,$puerto) {
        if ($puerto) {
                $fp = @fsockopen($ip, $puerto, $errno, $errstr, 1);
                if ($fp) {
                        print "<td bgcolor=green width=20></td>";
                        fclose($fp);
                }
                else {
                        print "<td bgcolor=red width=20></td>";
                }
        }
        else {
                print "<td bgcolor=red width=20>";
        }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$ficheroEditado=$_REQUEST['fichero'];
	$nombreEditado=$_REQUEST['nombre'];
	$fp = fopen($ficheroEditado, 'w');
	fwrite($fp, $nombreEditado);
	fclose($fp);
}

$fileList = glob('./_puertos/*');
natsort($fileList);

print("<html><head><title>Argos</title></head>");
print("<style> table, th, td { border: 1px solid black; border-collapse: collapse; } </style>");
print ("<table>");
print ("<tr> <td><b>Fecha</td> <td><b>Activo</td> <td><b>Nombre</td> <td><b>IP</td> <td><b>Puertos</td> <td><b>Mac</td> <td><b>Fabricante</td> </tr>");

foreach($fileList as $filename){
        if(is_file($filename)){
                $ip_array = explode ("./_puertos/", $filename);
                $ip = $ip_array[1];
                print ("<tr>");

                $ruta_last = "./_fecha/".$ip;
                $last = fopen($ruta_last,"r");
                if ($last) {
                        print ("<td valign='top'>".fgets($last)."</td>");
                        fclose($last);
                }
                $linesP = fopen($filename,'r');

                $puertos = (fgets($linesP));
                $puerto = explode (",",$puertos);
                $puerto = ($puerto[0]);
                fclose($linesP);
                check($ip,$puerto);

                $ruta_nombre = "./_nombre/".$ip;
                touch($ruta_nombre);
                $nombre = fopen($ruta_nombre,"r");
		print ("<td style='font-size:0px;'><form method='post'><input type='hidden' name='fichero' value='".$ruta_nombre."'><input style='font-size:14px;border:0;outline:0;' type='text' name='nombre' value='".fgets($nombre)."'></form></td>");
                fclose($nombre);

                print ("<td valign='top'>".$ip."</td>");

                $puertos = fopen($filename,'r');
		print("<td valign='top'>".fgets($puertos)."</td>");
                fclose($puertos);

                $ruta_mac = "./_mac/".$ip;
                $mac = fopen($ruta_mac,"r");
                print ("<td valign='top'>".fgets($mac)."</td>");
                fclose($mac);

                $ruta_macNombre = "./_macNombres/".$ip;
                $macNombre = fopen($ruta_macNombre,"r");
                print ("<td valign='top'>".fgets($macNombre)."</td>");
                fclose($macNombre);

                print ("</tr>");
        }
}
print ("</table>");
print("</html>");
?>
