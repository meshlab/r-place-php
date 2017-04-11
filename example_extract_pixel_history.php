<?PHP

ini_set('memory_limit','8000M');

include "class.place.php";


// include "config_spacescience.php";
// include "config_u_eriknstr.php";
// include "config_u_mncke.php";
include "config_spacescience_extrapolated_times.php";


date_default_timezone_set('Australia/Sydney');


$p=new place($archive_file);
$p->initiate_palette($palette);
$p->set_frame_details($frame_details);


$irand=mt_rand(0,999);
$jrand=mt_rand(0,999);
echo $p->pixelinfo($irand,$jrand);



echo "\r\n";
echo "Frame  Filename        Date                             Color   rgb   \r\n";
for($i=0;$i<$p->maxframes;$i++){
	if($p->frame_details["timestamp"][$i]){
		$datetext=date(DATE_RFC2822,$p->frame_details["timestamp"][$i]);
	}else{
		$datetext="";
		
	}
	echo str_pad($i,7).str_pad($p->frame_details["filename"][$i],16).str_pad($datetext,33)."#".str_pad($p->colors[$p->full_pixel_history[$i]],8)."(".$p->rgb[$p->full_pixel_history[$i]][0].",".$p->rgb[$p->full_pixel_history[$i]][1].",".$p->rgb[$p->full_pixel_history[$i]][2].")\r\n";


}




?>