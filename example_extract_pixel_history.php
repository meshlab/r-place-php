<?PHP

ini_set('memory_limit','8000M');
include "functions.php";
include "image_list.php";
include "class.place.php";


date_default_timezone_set('Australia/Sydney');


$p=new place("place_v5.bin");


$irand=mt_rand(0,999);
$jrand=mt_rand(0,999);
echo $p->pixelinfo($irand,$jrand);



echo "\r\n";
echo "Frame  Filename        Date                             Color   rgb   \r\n";
for($i=0;$i<$p->maxframes;$i++){
	if($image_times[$i]){
		$datetext=date(DATE_RFC2822,$image_times[$i]);
	}else{
		$datetext="";
		
	}
	echo str_pad($i,7).str_pad($image_files[$i],16).str_pad($datetext,33)."#".str_pad($p->colors[$p->full_pixel_history[$i]],8)."(".$p->rgb[$p->full_pixel_history[$i]][0].",".$p->rgb[$p->full_pixel_history[$i]][1].",".$p->rgb[$p->full_pixel_history[$i]][2].")\r\n";


}




?>