<?PHP

ini_set('memory_limit','8000M');
include "functions.php";
include "class.place.php";

$p=new place("place_v5.bin");


for($i=0;$i<=1000;$i++){
	$irand=mt_rand(0,999);
	$jrand=mt_rand(0,999);
	echo $p->pixelinfo($irand,$jrand);

}



?>