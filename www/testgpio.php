<?php
// echo "bonjour, programme pour tester les bornes gpio<BR>";

system ( "gpio mode 25 in" );
system ( "gpio mode 25 up" );
system ( "gpio write 26 1" );
// $i = 1; // initialisation
// exec("rm /tmp/shutdown");
// $test=exec("touch /home/pi/projects/chauffage/shutdown");
// echo "la variable test est égale à ".$test; 
echo "<BR>";
$output = shell_exec("ls -l /home/pi/projects/chauffage/");
echo "<pre>$output</pre>";
?>


