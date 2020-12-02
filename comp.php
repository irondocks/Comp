<?php

function Compress(string $file, string $outputname)
{
    $m = 0;
    $i = 25;
    $s = 10000;
    while (strlen($file)%$s > 0) $s--;
    $img = imagecreatetruecolor((($s)),round(strlen($file)/$s)+5);
    $black = imagecolorallocate($img, 1, 1, 1 );
    imagefill($img, 0, 0, $black);
    $rr = 0;
    $y = 0;
    //imageantialias($img, false);
    
    while (strlen($file) > $m)
    {
        $r = ord($file[$m]);
        $black = ($r << 16) + ($r << 8) + $r;
        $m += 1;
        // $r = ord($file[$m]);
        // $black = ($r << 8) + $black;
        // $m += 1;
 
        imagesetpixel($img,$rr,$y,$black);
        $rr++;
        
        if ($rr+1 >= (($s)))
        {
            $y += 1;
            $rr = 0;
        }
    }
    $int = (($s));
    $img = imagecrop($img,["x" => 0, "y" => 0, "width" => $int, "height" => ($y+1)]);
    imageantialias($img, false);
    imagepng($img, "$outputname.png", 9);
    echo "Compressed....";
    $dim = $s . "x" . ($y+1) . "+256";
    exec("gm convert -colorspace gray -size $dim -depth 8 -colors 256 -define webp:lossless=true $outputname.png $outputname.webp");
    //unlink("$outputname.png");
    imagedestroy($img);
}

function Decompress(string $filename, string $output = "f.txt")
{
    $str = "";
    $rr = 0;
    $y = 0;
    $f = fopen($output,"w");
    $filestr = file_get_contents($filename);
    $img = imagecreatefromstring($filestr);
    $d = 0;
    while (imagesy($img) > $y)
    {

        while (imagesx($img) > $rr + 1) {
            $rgb = imagecolorat($img,$rr,$y);
            $b = chr($rgb & 0xFF);
            $str .= $b;
            $rr++;
        }
        $rr = 0;
        $y++;
    }
    //rtrim($str,"\x00");
    while (substr($str,-1) == chr(1))
        $str = substr($str,0, strlen($str) - 1);
    fwrite($f,$str);
    imagedestroy($img);
}

$filestr = file_get_contents($argv[1]);
$x = 65;
$d = 0;
$c = "";
while (strlen($filestr) > 0)
{
    $c = chr($x); 
    if (strlen($filestr) > 50000000)
    {
        Compress(substr($filestr,0,50000000), "$d$c");
        $filestr = substr($filestr,50000000);
        //Decompress("$d$c.webp","f.txt");
    }
    else
    {
        Compress($filestr, "$d$c");
        $filestr = "";
        Decompress("$d$c.webp","f.txt");
    }
    if (chr($x) == 'Z')
    {
        $x = 64;
        $d++;
    }
    $x++;
}

echo "done;";
?>
 
