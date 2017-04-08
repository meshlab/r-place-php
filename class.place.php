<?PHP


class place{
	
	function __construct($filename){
		
		$zd = gzopen($filename, "r");
		$this->contents = gzread($zd, 600000000);
		gzclose($zd);
		
		$this->frame_chunk_sizes=unpack('N1000',substr($this->contents,0,4000));
		$this->color_chunk_sizes=unpack('N1000',substr($this->contents,4000,4000));

		$this->frame_start_bytes=unpack('N1000000',substr($this->contents,8000,4000000));
		$this->color_start_bytes=unpack('N1000000',substr($this->contents,4008000,4000000));

		
		$counter=1;
		$line_bytes_running_total=8008000;

		for($j=0;$j<=999;$j++){
				$color_bytes_running_total=$line_bytes_running_total+$this->frame_chunk_sizes[$j+1];
				for($i=0;$i<=999;$i++){
					$this->frame_start_bytes_2d[$i][$j]=$line_bytes_running_total+$this->frame_start_bytes[$counter];
					$this->color_start_bytes_2d[$i][$j]=$color_bytes_running_total+$this->color_start_bytes[$counter];
					$counter++;
				}
			$line_bytes_running_total+=$this->frame_chunk_sizes[$j+1]+$this->color_chunk_sizes[$j+1];	
		}

		$this->initiatePalette();
		$this->maxframes=24391;

	}
	
	function pixelInfo($i,$j,$firstframe=false,$lastframe=false){
		$text="\r\n";
		$text.="Information for pixel(".$i.",".$j.")\r\n";
		$text.="\r\n";
		$this->pixel($i,$j,$firstframe,$lastframe);

		arsort($this->colorcounts);
		$text.=str_pad("COLOR",15)." ".str_pad("COUNT",7)." ".str_pad("PERCENTAGE",7)."\r\n";
		foreach($this->colorcounts as $colorID=>$count) $text.=str_pad($this->colorNames[$colorID],15)." ".str_pad($count,7)." ".str_pad(round(($count/count($this->full_pixel_history))*100,2)."%",7)."\r\n";
		ksort($this->colorcounts);
		$text.="\r\n";
		$text.="edited ".$this->num_edits()." times from frame ".$this->firstframe." to ".$this->lastframe."\r\n";
		$text.="first color ".$this->colorNames[$this->pixel_first_color]."\r\n";
		$text.="last color ".$this->colorNames[$this->pixel_last_color]."\r\n";
		$text.="Shannon Entropy of this pixel is ".round($this->shannon_entropy(),4)."\r\n";
		$this->mode();
		$text.="Mode of this pixel is color ".$this->mode." (".$this->colorNames[$this->mode].")\r\n";
		
		return $text;
	}
	
	function set_frame_bounds($firstframe,$lastframe){
	
			$this->firstframe=$firstframe;

			$this->lastframe=$lastframe;

			
			$this->pixel_first_color=$this->full_pixel_history[$this->firstframe];
			$this->pixel_last_color=$this->full_pixel_history[$this->lastframe];
			
			
			for($i=0;$i<=15;$i++)$this->colorcounts[$i]=0;
			
			for($k=$this->firstframe;$k<=$this->lastframe;$k++){
				$this->colorcounts[$this->full_pixel_history[$k]]++;
			}
			
		
	}
	
	
	function shannon_entropy(){
		
		$bitsum=0;
		foreach($this->colorcounts as $color=>$count){
			if($count!=0){
				$prob=$count/($this->lastframe-$this->firstframe);
				$bitsum-=$prob*log($prob,2);	
			
			}
		}
		$this->shannon_entropy=$bitsum;
		return $bitsum;
		
		
	}
	
	function mode(){
		$modes = array_keys($this->colorcounts, max($this->colorcounts));
		$this->mode=$modes[0];
		return $modes[0];
	}
	
	function num_edits(){
		$num_edits=0;
		for($k=$this->firstframe;$k<$this->lastframe;$k++){
			if($this->full_pixel_history[$k]!=$this->full_pixel_history[$k+1]){
				$num_edits++;
			}
		}
		
		$this->num_edits=$num_edits;
		return $num_edits;
		
	}
	
	
	function initiatePalette(){
		$this->colors[0]="333333";
		$this->colors[1]="CC9900";
		$this->colors[2]="0099CC";
		$this->colors[3]="990099";
		$this->colors[4]="00CCCC";
		$this->colors[5]="00CC00";
		$this->colors[6]="996633";
		$this->colors[7]="999999";
		$this->colors[8]="CC66CC";
		$this->colors[9]="CCCCCC";
		$this->colors[10]="CCCC00";
		$this->colors[11]="0000FF";
		$this->colors[12]="CC0000";
		$this->colors[13]="FF99CC";
		$this->colors[14]="99CC33";
		$this->colors[15]="FFFFFF";
		
		$this->rgb[0]=array(51, 51, 51);
		$this->rgb[1]=array(204, 153, 0);
		$this->rgb[2]=array(0, 153, 204);
		$this->rgb[3]=array(153, 0, 153);
		$this->rgb[4]=array(0, 204, 204);
		$this->rgb[5]=array(0, 204, 0);
		$this->rgb[6]=array(153, 102, 51);
		$this->rgb[7]=array(153, 153, 153);
		$this->rgb[8]=array(204, 102, 204);
		$this->rgb[9]=array(204, 204, 204);
		$this->rgb[10]=array(204, 204, 0);
		$this->rgb[11]=array(0, 0, 255);
		$this->rgb[12]=array(204, 0, 0);
		$this->rgb[13]=array(255, 153, 204);
		$this->rgb[14]=array(153, 204, 51);
		$this->rgb[15]=array(255,255,255);
		
		$this->colorNames[0]="Dark Grey";
		$this->colorNames[1]="Tan/Orange";
		$this->colorNames[2]="Light Blue";
		$this->colorNames[3]="Purple";
		$this->colorNames[4]="Turquise";
		$this->colorNames[5]="Green";
		$this->colorNames[6]="Brown";
		$this->colorNames[7]="Grey";
		$this->colorNames[8]="Light Purple";
		$this->colorNames[9]="Light Grey";
		$this->colorNames[10]="Yellow/Green";
		$this->colorNames[11]="Blue";
		$this->colorNames[12]="Red";
		$this->colorNames[13]="Pink";
		$this->colorNames[14]="Light Green";
		$this->colorNames[15]="White";
	
	}
	
	
	function pixel($i,$j,$firstframe=false,$lastframe=false){
		
		if($i<0 or $i>999){
			if($j<0 or $j>999){
				echo "pixel co-ordinates out of range. \r\n";
				exit;
				
			}
		}
		
		
		 // echo $this->frame_start_bytes_2d[$i][$j]." ".$this->frame_start_bytes_2d[$i+1][$j]." ".($this->frame_start_bytes_2d[$i+1][$j]-$this->frame_start_bytes_2d[$i][$j])."\r\n";
		 // echo $this->color_start_bytes_2d[$i][$j]." ".$this->color_start_bytes_2d[$i+1][$j]." ".($this->color_start_bytes_2d[$i+1][$j]-$this->color_start_bytes_2d[$i][$j])."\r\n";	 
		 
		 if($i==999){
			 $end_byte=8008000;
			 
			 for($temp=1;$temp<=$j;$temp++){
				 $end_byte+=$this->frame_chunk_sizes[$temp]+$this->color_chunk_sizes[$temp];

			 }
			$frame_end_byte=$end_byte+$this->frame_chunk_sizes[$j+1];
			$color_end_byte=$end_byte+$this->frame_chunk_sizes[$j+1]+$this->color_chunk_sizes[$j+1];
			 
			 $last_frame_byte=$frame_end_byte-$this->frame_start_bytes_2d[$i][$j];
			 $last_color_byte=$color_end_byte-$this->color_start_bytes_2d[$i][$j]; 
			 

		 }else{
			 $last_frame_byte=$this->frame_start_bytes_2d[$i+1][$j]-$this->frame_start_bytes_2d[$i][$j];
			 $last_color_byte=$this->color_start_bytes_2d[$i+1][$j]-$this->color_start_bytes_2d[$i][$j]; 
		 }

		 
		 
		$frames=unpack('n*',substr($this->contents,$this->frame_start_bytes_2d[$i][$j],$last_frame_byte));
		$colors=unpack('n*',substr($this->contents,$this->color_start_bytes_2d[$i][$j],$last_color_byte));
	
		$numframes=count($frames);
		// echo $numframes." frames\r\n";
		if($numframes>0){
			
			$pixelhistory=array();
			foreach($frames as $ID=>$frame){
				$pixelhistory[$frame]=$colors[$ID];	
			}	


            // print_r($pixelhistory);			
			
			// print_r($pixelhistory);
			$this->sparse_pixel_history=$pixelhistory;
			$full_pixel_history=array();
			
			for($i=0;$i<=$frames[1];$i++){
				$this->full_pixel_history[$i]=15;
			}
			// echo "setting from frame 0 to frame ".$frames[1]." as white\r\n";
			
			foreach($frames as $ID=>$frame){
				if($ID!=$numframes){
					// echo "setting from frame ".$frames[$ID]." to frame ".($frames[$ID+1]-1)." as color ".$colors[$ID]."\r\n";
					for($i=$frames[$ID];$i<=$frames[$ID+1]-1;$i++){
						$this->full_pixel_history[$i]=$colors[$ID];
					}
				}
			}
			// echo "setting from frame ".$frames[$numframes]." to frame ".$this->maxframes." as color ".$colors[$numframes]."\r\n";
			for($i=$frames[$numframes];$i<=$this->maxframes;$i++){
				$this->full_pixel_history[$i]=$colors[$numframes];
			}
			
			
			if($firstframe){
				$this->firstframe=$firstframe;
			}else{
				$this->firstframe=0;
			}
			if($lastframe){
				$this->lastframe=$lastframe;
			}else{
				$this->lastframe=$this->maxframes;
			}
			
			


			
			// if($firstframe or $lastframe){
			// 	$this->full_pixel_history = array_slice($this->full_pixel_history, $this->firstframe, $this->lastframe-$this->firstframe+2);
			// }
			
			$this->pixel_first_color=$this->full_pixel_history[$this->firstframe];
			$this->pixel_last_color=$this->full_pixel_history[$this->lastframe];
			
			// echo "last frame ".$this->firstframe." ".$this->lastframe." ".($this->lastframe-$this->firstframe)."\r\n";
			
			for($i=0;$i<=15;$i++)$this->colorcounts[$i]=0;
			
			for($k=$this->firstframe;$k<=$this->lastframe;$k++){
				$this->colorcounts[$this->full_pixel_history[$k]]++;
			}
			
			// foreach($this->full_pixel_history as $frame=>$colorID)$this->colorcounts[$colorID]++;
			
			
		}else{
			$this->num_edits=0;
			for($i=0;$i<=$this->maxframes;$i++){
				$this->full_pixel_history[$i]=15;
			}
			$this->sparse_pixel_history=array();
			$this->pixel_first_edited=NULL;
			$this->pixel_last_edited=NULL;
			$this->pixel_first_color=15;
			$this->pixel_last_color=15;
			for($i=0;$i<15;$i++)$this->colorcounts[$i]=0;
			$this->colorcounts[15]=$this->maxframes;
		}
		
	}
	
	
}


?>