# Argos
>Local Network Inventory Scanner

The purpose is to have the bash script running periodically to identify the devices within a network, as well as their open ports, representing the information in a web table.

Installation:

-Configure parameters in bash script

>rango="192.168.100"

>ruta_web="/var/www/html/argos/"

-Schedule a recurring task, this uses crontab and repeats every 15 minutes
>sudo crontab -e

>*/15 * * * * /COMPLETE_PATH_TO_SHELL_SCRIPT/argos.sh

-In the web folder, create folder for store php script and create folders: _fecha, _mac, _macNombres, _nombre and _puertos
>mkdir _fecha _mac _macNombres _nombre _puertos
