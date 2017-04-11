<?PHP

ini_set('memory_limit','8000M');

include "class.place.php";

// include "config_spacescience.php";
include "config_u_eriknstr.php";

$p=new place($archive_file);
$p->initiate_palette($palette);
$p->set_frame_details($frame_details);


for($i=0;$i<=1000;$i++){
	$irand=mt_rand(0,999);
	$jrand=mt_rand(0,999);
	echo $p->pixelinfo($irand,$jrand);

}



?>