<?PHP

ini_set('memory_limit','8000M');


include "config_spacescience.php";
// include "config_u_eriknstr.php";


include "class.place.php";

$p=new place($archive_file);
$p->initiate_palette($palette);
$p->set_frame_details($frame_details);

echo $p->pixelinfo(0,0);    // extract information for the top left pixel



/*
The code below breaks up the timeline for the pixel at (0,0) into groups of 100 frames each and calculates the entropy and mode of each chunk
*/

echo "\r\n";
echo "Start frame    End frame      num edits      Shannon Entropy   Mode\r\n";
for($i=0;$i<24000;$i+=100){
	$p->set_frame_bounds($i,$i+100);
	echo str_pad($i,15).str_pad($i+100,15).str_pad($p->num_edits(),15).str_pad(round($p->shannon_entropy(),3),18)."#".$p->colors[$p->mode()]."\r\n";


}




?>