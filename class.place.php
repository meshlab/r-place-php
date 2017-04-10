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

		// $this->initiatePalette();
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
	
	
	function set_frame_details($frames){
		$this->frame_details=$frames;
		
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
	
	function average_color(){
		$sum_r=0;
		$sum_g=0;
		$sum_b=0;
		$count=0;
		for($k=$this->firstframe;$k<$this->lastframe;$k++){
			
			$sum_r+=$this->rgb[$this->full_pixel_history[$k]][0];
			$sum_g+=$this->rgb[$this->full_pixel_history[$k]][1];
			$sum_b+=$this->rgb[$this->full_pixel_history[$k]][2];
			$count++;
			
		}
		$this->ave_color[0]=round($sum_r/$count);
		$this->ave_color[1]=round($sum_g/$count);		
		$this->ave_color[2]=round($sum_b/$count);		
		return $this->ave_color;
		
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
	
	
	function initiate_palette($palette){
		$this->colors=$palette[0];
		$this->rgb=$palette[1];
		$this->colorNames=$palette[2];
		
		
		
	
	}
	
	
	function pixel($i,$j,$firstframe=false,$lastframe=false){
		
		if($i<0 or $i>999){
			if($j<0 or $j>999){
				echo "pixel co-ordinates out of range. \r\n";
				exit;
				
			}
		}
				 
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

		if($numframes>0){
			
			$pixelhistory=array();
			foreach($frames as $ID=>$frame){
				$pixelhistory[$frame]=$colors[$ID];	
			}	


			$this->sparse_pixel_history=$pixelhistory;
			$full_pixel_history=array();
			
			for($i=0;$i<=$frames[1];$i++){
				$this->full_pixel_history[$i]=15;
			}

			
			foreach($frames as $ID=>$frame){
				if($ID!=$numframes){

					for($i=$frames[$ID];$i<=$frames[$ID+1]-1;$i++){
						$this->full_pixel_history[$i]=$colors[$ID];
					}
				}
			}

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

			
			$this->pixel_first_color=$this->full_pixel_history[$this->firstframe];
			$this->pixel_last_color=$this->full_pixel_history[$this->lastframe];
			
		
			for($i=0;$i<=15;$i++)$this->colorcounts[$i]=0;
			
			for($k=$this->firstframe;$k<=$this->lastframe;$k++){
				$this->colorcounts[$this->full_pixel_history[$k]]++;
			}
			
	
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