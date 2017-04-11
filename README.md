# r-place-php

PHP code to explore the pixel histories in Reddit's /r/place. The code is designed to be run from the command line.

The code is optimised to explore individual pixels through time, as opposed to exploring a single timeframe for the entire image. If you just want to explore the raw images then there are various dumps of the image snapshots available for download (see links to torrent files below).

Three image archives were pre-processed into compressed difference files. You'll need to download these along with the code. Each archive is slightly different. The characteristics of each of the three archives are described below.

A difference file can be loaded into memory quite quickly, this happens within about 1.5 seconds on my laptop. Once loaded, the PHP program has access to all the information it needs to reconstruct the entire series of images from that particular archive.

A simple "hello pixel" program might look somethingn like this:

```php
<?PHP
ini_set('memory_limit','1024M'); // increase PHP's memory limits, make this even bigger if you have more RAM
include "class.place.php";
include "config_spacescience.php"; // this contains all the palette and frame infrmation for the spacescience archive
$p=new place($archive_file);   // instantiate the object, specifying the archive file to use
$p->initiate_palette($palette);  // initiate the pallette and frame information
$p->set_frame_details($frame_details);
$irand=mt_rand(0,999);   // select a random pixel
$jrand=mt_rand(0,999);
echo $p->pixelinfo($irand,$jrand);  // outputs information about the selected pixel to the console
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

Three main image repositories are available:


*Archive filename:	u_eriknstr.bin
*PHP-Place Binary Archive:	https://drive.google.com/uc?export=download&id=0BwlPeQzg23lsWEQ0d05DR244Zjg
*PHP Place Archive Size:	26.6MB
*config file: config_u_eriknstr.php
*Number of Images:	14823
*Torrent File:	https://www.nordstroem.no/blob/15/ae/be13635-668247.torrent
*Reddit Username of torrent seeder:	/u/eriknstr
*Archive info page:	https://www.reddit.com/r/place/comments/63a47z/bittorrent_magnet_links_for_raw_reddit_format/
*First Frame Timestamp:	Sat, 01 Apr 2017 11:03:35 +1100
*Last Frame Timestamp:	Tue, 04 Apr 2017 02:56:17 +1000
*First UTC timestamp:	1491005015
*Last UTC timestaamp:	1491245120
*Duration (hours):	66.7
*Frame every n seconds:	16.2






