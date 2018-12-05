#!/usr/bin/env python2
#encoding: UTF-8

# To change this license header, choose License Headers in Project Properties.
# To change this template file, choose Tools | Templates
# and open the template in the editor.

import smbus
import time

#mettre 0 si ancien raspberry ou 1 si raspberry 2 et +
bus = smbus.SMBus(1)
adress = 0x12

print "Envoi de la valeur 4 "
bus.write_byte(adress, 4)
#pause de 1 seconde pour laisser le temps au traitement de se faire
time.sleep(1)
reponse = bus.read_byte(adress)
print "la réponse de l'arduino : ", reponse
