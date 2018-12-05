$(document).ready(function() {
   $("#rafraichir").load("refresh.php");
   startTime();
   
   setInterval(function() { // Do this
               $('#rafraichir').load('refresh.php');
         }, 500); // Every one second   
   setInterval(function(){startTime()},500);
});

function startTime()
{
   var today=new Date();
   var h=today.getHours();
   var m=today.getMinutes();
   var s=today.getSeconds();
   // add a zero in front of numbers<10
   m=checkTime(m);
   s=checkTime(s);
   document.getElementById('heure').innerHTML = today.getDate() + "/" + (today.getMonth()+1) + "/" + today.getFullYear() + " " + h + ":" + m + ":" + s;
}

function checkTime(i)
{
   if (i<10)
   {
      i="0" + i;
   }
   return i;
}