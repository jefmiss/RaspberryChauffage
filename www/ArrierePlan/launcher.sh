#!/bin/bash

NB_PROCESSUS=`ps aux | grep daemonize.php | grep -v grep | wc -l`
if [ "$NB_PROCESSUS"  -eq "0" ]
then
 
  `dirname $0`/daemonize.php
   date > /tmp/log_launcher
fi
#deuxieme test qui va voir si un bouton poussoir pour activer le shutdown a été poussé
#lorsque l'on appui sur le poussoir, on créé un répertoir /tmp/shutdown.
#le présent test vérifie si ce répertoire existe et si oui, il le supprime et lance 
#la commande shutdown
	#date >> /tmp/log_shutdown

if [ -f "/home/pi/projects/chauffage/shutdown" ]
then
	rm -f /home/pi/projects/chauffage/shutdown
        /sbin/shutdown now
fi
