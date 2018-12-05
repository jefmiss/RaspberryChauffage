<?php

$address = 4;
$wdata = array(2,4,8);
$filename = "/home/pi/projects/chauffage/something.txt";
$handle = fopen($filename, "w+r");
fseek($handle, $address);
$contents = fwrite($handle,$wdata,1);
fclose($handle);
echo "fin de l'écriture, nombre d'octet ecrit : ".$contents;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

