<?PHP

function create_animation_frames($istart,$jstart,$width=100,$height=100,$start_frame=0,$end_frame=24391,$skip_frames=100,$file_stub="frame_",$output_directory="",$place_binary="place_v5.bin"){


	$p=new place($place_binary);
	for($frame=$start_frame;$frame<=$end_frame;$frame+=$skip_frames){
		$im[$frame] = @imagecreatetruecolor($width, $height)
		  or die('Cannot Initialize new GD image stream');
		  for($c=0;$c<=15;$c++){
			  $color[$frame][$c] = imagecolorallocate($im[$frame], $p->rgb[$c][0], $p->rgb[$c][1],$p->rgb[$c][2]);
		  }
		
	}
	for($i=$istart;$i<min(1000,$istart+$width);$i++){
		for($j=$jstart;$j<min(1000,$jstart+$height);$j++){
			echo "processing pixel $i,$j\r\n";
			$p->pixel($i,$j);
			for($frame=$start_frame;$frame<=$end_frame;$frame+=$skip_frames){
				imagesetpixel ( $im[$frame] , $i-$istart , $j-$jstart , $color[$frame][$p->full_pixel_history[$frame]]  );
			}
			// echo $p->full_pixel_history[$frame]."\r\n";
		}
	}
	for($frame=$start_frame;$frame<=$end_frame;$frame+=$skip_frames){
		imagepng($im[$frame],$output_directory.$file_stub.str_pad($frame,5).".png");
		imagedestroy($im[$frame]);
	}

}





?>