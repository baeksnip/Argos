#!/bin/bash
function ctrl_c(){
 exit 1
}
trap ctrl_c INT

# Config parametros #
rango="192.168.100"
ruta_web="/var/www/html/argos/"
# Config parametros #

#Variables
ruta_ficheros_puertos=$ruta_web"_puertos/"
ruta_ficheros_mac=$ruta_web"_mac/"
ruta_ficheros_macNombres=$ruta_web"_macNombres/"
ruta_macVendors=$ruta_web"macVendors.txt"
ruta_ficheros_fecha=$ruta_web"_fecha/"
ruta_ficheros_nombre=$ruta_web"_nombre/"
fecha=`date +"%d/%m/%Y %H:%M"`

#Escaneo de IPs
for ip in $(seq 1 254); do
        timeout 1 bash -c "ping -c 1 $rango.$ip" &>/dev/null && touch $ruta_ficheros_puertos$rango"."$ip && echo -n $fecha > $ruta_ficheros_fecha$rango"."$ip &
done; wait

#Consulta MAC si el fichero de la MAC esta vacio
for fichero_ip in `ls $ruta_ficheros_puertos`; do
        if ! [ -s $ruta_ficheros_mac$fichero_ip ]; then
                bash -c "ip neigh | grep $fichero_ip' '" | awk '{print $5}' > $ruta_ficheros_mac$fichero_ip
        fi
done

#Consulta MACs para buscar el fabricante
for fichero_ip in `ls $ruta_ficheros_mac`; do
	if [ ! -f $ruta_ficheros_macNombres$fichero_ip ]; then
		touch $ruta_ficheros_macNombres$fichero_ip
	fi
	mac_sin_separadores=$(cat "$ruta_ficheros_mac$fichero_ip" | tr -d ':')
	mac_primeros_seis=$(echo $mac_sin_separadores | grep -Po "^.{6}")
	empresa=$(grep -i "^$mac_primeros_seis" "$ruta_macVendors" | cut -d'|' -f2)
	if [ ! -z "$empresa" ]; then
		echo "$empresa" >> "$ruta_ficheros_macNombres$fichero_ip"
	fi
done

#Escaneo de puertos si el fichero de la ip esta vacio 65535
for fichero_ip in `ls $ruta_ficheros_puertos`; do
	size=$(wc -c < "$ruta_ficheros_puertos$fichero_ip")
	if [ "$size" -le 2 ]; then
		rm $ruta_ficheros_puertos$fichero_ip
		touch $ruta_ficheros_puertos$fichero_ip
                for port in $(seq 1 10000); do
                        timeout 0.1 bash -c "echo '' > /dev/tcp/$fichero_ip/$port" 2>/dev/null && echo -n $port"," >> $ruta_ficheros_puertos$fichero_ip
                done; wait
        fi
done
