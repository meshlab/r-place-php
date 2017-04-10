<?PHP

ini_set('memory_limit','8000M');
gc_enable();

include "class.place.php";


include "config_spacescience.php";
// include "config_u_eriknstr.php";

/*
$istart=320;
$jstart=370;

$width=130;
$height=180;
$file_stub="mona_lisa_";
*/

// The Void
$istart=260;
$jstart=440;

$width=320;
$height=360;
$file_stub="the_void_";
$output_directory="the_void_animation/";

// $width=30;
// $height=80;



// $istart=600;
// $jstart=550;

$width=1000;
$height=20;

// $width=100;
// $height=100;


$output_directory="full_animation_green/";

$start_frame=0;


$end_frame=24250;

$skip_frames=40;



for($loop=0;$loop<50;$loop++){
	$file_stub="full_animation_loop".$loop."_";
	$istart=0;
	$jstart=$loop*20;

	$p=new place($archive_file);
	$p->initiate_palette($palette);
	$p->set_frame_details($frame_details);
	
	
	
	for($frame=$start_frame;$frame<=$end_frame;$frame+=$skip_frames){
		$im[$frame] = @imagecreatetruecolor($width, $height)
		  or die('Cannot Initialize new GD image stream');
		//   for($c=0;$c<=15;$c++){
		// 	  $color[$frame][$c] = imagecolorallocate($im[$frame], $p->rgb[$c][0], $p->rgb[$c][1],$p->rgb[$c][2]);
		//   }
		  
		$activity_im[$frame] = @imagecreatetruecolor($width, $height)
		  or die('Cannot Initialize new GD image stream');
		 //  for($c=0;$c<=255;$c++){
		// 	  $greyscale[$frame][$c] = imagecolorallocate($activity_im[$frame], $c,0,0);
		 //  }
		  
		
	}
	for($i=$istart;$i<min(1000,$istart+$width);$i++){
		for($j=$jstart;$j<min(1000,$jstart+$height);$j++){
			
			$last_activity_r=0.0;
			$last_activity_g=0.0;
			$last_activity_b=0.0;
			echo "processing pixel $i,$j loop $loop\r\n";
			$p->pixel($i,$j);
			for($frame=$start_frame;$frame<=$end_frame;$frame+=$skip_frames){
				// imagesetpixel ( $im[$frame] , $i-$istart , $j-$jstart , $color[$frame][$p->full_pixel_history[$frame]]  );
				$p->set_frame_bounds($frame,$frame+$skip_frames);
				$rgb_temp=$p->average_color();
				$temp_color=imagecolorallocate($im[$frame], $rgb_temp[0], $rgb_temp[1],$rgb_temp[2]);
				imagesetpixel ( $im[$frame] , $i-$istart , $j-$jstart , $temp_color  );
			// }
			
			// for($frame=$start_frame;$frame<=$end_frame;$frame+=$skip_frames){
				// $p->set_frame_bounds($frame,$frame+$skip_frames);
				// echo "$i $j $frame ".$p->firstframe." ".$p->lastframe." ".$p->num_edits()."\r\n";
				$num_edits_temp=$p->num_edits();
				if($num_edits_temp>0){
					$last_activity_g=255.0;
				}else{
					$last_activity_r=$last_activity_g*0.9;
				}
				if($num_edits_temp>=5){
					$last_activity_r=150;
					$last_activity_b=150;		
				}elseif($num_edits_temp>1 AND $num_edits_temp<5){
					$last_activity_r=$num_edits_temp*30;
					$last_activity_b=$num_edits_temp*30;
				}else{
					$last_activity_r=$last_activity_g*0.8;
					$last_activity_b=$last_activity_b*0.8;
				}

				$temp_color=imagecolorallocate($im[$frame], floor($last_activity_r), floor($last_activity_g),floor($last_activity_b));
				// imagesetpixel ( $activity_im[$frame] , $i-$istart , $j-$jstart , $greyscale[$frame][floor($last_activity_r)]  );
				imagesetpixel ( $activity_im[$frame] , $i-$istart , $j-$jstart , $temp_color );
			}

		}
	}
	
	$counter=1;
	for($frame=$start_frame;$frame<=$end_frame;$frame+=$skip_frames){
		$temp_image = @imagecreatetruecolor($width*2, $height)
		  or die('Cannot Initialize new GD image stream');
		  
		imagecopy ( $temp_image , $im[$frame], 0 , 0 , 0, 0 , $width , $height );
		imagecopy ( $temp_image , $activity_im[$frame], $width , 0 , 0, 0 , $width , $height );		
		imagepng($temp_image,$output_directory.$file_stub."combined_".str_pad($counter,5,0,STR_PAD_LEFT).".png");
		imagedestroy($temp_image);
		
		imagepng($im[$frame],$output_directory.$file_stub.str_pad($counter,5,0,STR_PAD_LEFT).".png");
		imagedestroy($im[$frame]);
		
		imagepng($activity_im[$frame],$output_directory.$file_stub."activity_".str_pad($counter,5,0,STR_PAD_LEFT).".png");
		imagedestroy($activity_im[$frame]);
		
		$counter++;
	}
	
	unset($p);
	gc_collect_cycles();
	
	sleep(10);
	gc_collect_cycles();
}


?>