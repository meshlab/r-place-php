# r-place-php

PHP code to explore the pixel histories in Reddit's /r/place. The code is designed to be run from the command line.

This code can be used to make beautiful animations like this http://imgur.com/gallery/Xj1YMlk or it can be used to explore the statistical properties of the /r/place image archives.

r-place-php is optimised to explore individual pixels through time, as opposed to exploring a single timeframe for the entire image. If you just want to explore the raw images then there are various dumps of the image snapshots available for download (see links to torrent files below).

r-place-php can load an entire /r/place history file into memory in under 2 seconds, and can extract a single pixel's complete history from the in-memory archive in around 5 ms.

## Hello Pixel

A simple "hello pixel" program might look somethingn like this:

```php
<?PHP
// increase PHP's memory limits, make this even bigger if you have more RAM
ini_set('memory_limit','1024M'); 
include "class.place.php";

// this contains all the palette and frame infrmation for the spacescience archive
include "config_spacescience.php"; 

// instantiate the r-place-php object, specifying the archive file to use
$p=new place($archive_file);   

// initiate the pallette and frame information
$p->initiate_palette($palette);  
$p->set_frame_details($frame_details);

 // select a random pixel
$irand=mt_rand(0,999);  
$jrand=mt_rand(0,999);

// output information about the selected pixel to the console
echo $p->pixelinfo($irand,$jrand);  
?>
```

Output from that code will look somethingn like this:


```
Information for pixel(840,76)

COLOR           COUNT   PERCENTAGE
Grey            19065   78.16%
Light Purple    2040    8.36%
Yellow/Green    1369    5.61%
Brown           825     3.38%
Light Grey      736     3.02%
White           150     0.61%
Light Green     103     0.42%
Turquise        102     0.42%
Dark Grey       2       0.01%
Tan/Orange      0       0%
Light Blue      0       0%
Purple          0       0%
Green           0       0%
Blue            0       0%
Red             0       0%
Pink            0       0%

edited 15 times from frame 0 to 24391
first color White
last color Grey
Shannon Entropy of this pixel is 1.2407
Mode of this pixel is color 7 (Grey)

```

Three image archives were pre-processed into compressed difference files. You'll need to download these along with the code. Each archive is slightly different. The characteristics of each of the three archives are described below.

A difference file can be loaded into memory quite quickly, this happens within about 1.5 seconds on my laptop. Once loaded, the PHP program has access to all the information it needs to reconstruct the entire series of images from that particular archive.


Three main image repositories are available:

## /u/mncke's Archive

This is the most complete and consistant archive. One frame roughly every 5  seconds of /r/place's existance.

* r-place-php binary archive:	https://drive.google.com/uc?export=download&id=0BwlPeQzg23lsMzM5cEh0QTB3OU0
* r-place-php binary archive Size:	34.3MB
* r-place-php binaryarchive filename:	u_mncke.bin
* r-place-php config file: config_u_mncke.php
* Number of images:	50749
* Torrent File:	http://abra.me/place/place-snaps.torrent
* Reddit Username of torrent seeder:	http://www.reddit.com/u/mncke
* Archive info page:	https://www.reddit.com/r/place/comments/6396u5/rplace_archive_update/
* First Frame Time:	Sat, 01 Apr 2017 06:01:00 +1100
* Last Frame Time:	Tue, 04 Apr 2017 02:56:23 +1000
* First UTC timestamp:	1490986860
* Last UTC timestamp:	1491244860
* Duration (hours):	71.7
* Frame every 5.1s

## /u/eriknstr's Archive

* r-place-php binary archive:	https://drive.google.com/uc?export=download&id=0BwlPeQzg23lsWEQ0d05DR244Zjg
* r-place-php binary archive Size:	26.6MB
* r-place-php binaryarchive filename:	u_eriknstr.bin
* r-place-php config file: config_u_eriknstr.php
* Number of images:	14823
* Torrent File:	https://www.nordstroem.no/blob/15/ae/be13635-668247.torrent
* Reddit Username of torrent seeder:	http://www.reddit.com/u/eriknstr
* Archive info page:	https://www.reddit.com/r/place/comments/63a47z/bittorrent_magnet_links_for_raw_reddit_format/
* First Frame Time:	Sat, 01 Apr 2017 11:03:35 +1100
* Last Frame Time:	Tue, 04 Apr 2017 02:56:17 +1000
* First UTC timestamp:	1491005015
* Last UTC timestamp:	1491245120
* Duration (hours):	66.7
* Frame every 16.2s



## /u/JetBalsa's Archive

This archive had the earliest start time, but the first 600 odd images did not contain a timestamp. Times for these images were estimated by matching with the other archives and projecting backwards.

* r-place-php binary archive:	https://drive.google.com/uc?export=download&id=0BwlPeQzg23lsbEtlTnpZNmFKcmM
* r-place-php binary archive Size:	31.2MB
* r-place-php binaryarchive filename:	spacescience.tech.bin
* r-place-php config file: config_spacescience.php
* r-place-php config file with projected times: config_spacescience_projected_times.php
* Number of images:	24391 (after removing 9 blank images)
* Torrent File:	http://spacescience.tech/place.torrent
* Reddit Username of torrent seeder:	http://www.reddit.com/u/JetBalsa
* Archive info page:	https://www.reddit.com/r/place/comments/639gsw/place_archival_data_megathread/
* First Frame Time:	first timestamped image at frame 621 is Sat, 01 Apr 2017 10:13:37 +1100
* Projected first time: Sat, 01 Apr 2017 04:38:09 +1100
* Last Frame Timestamp:	Tue, 04 Apr 2017 02:55:30 +1000
* First UTC timestamp:	1491002017
* Projected first UTC timestamp: 1490981889
* Last UTC timestamp:	1491239619
* Duration (hours):	71.6
* Frame every 10.6s



![alt text](http://i.imgur.com/juruTcB.png "Relative coverage of the /r/place image archives")

