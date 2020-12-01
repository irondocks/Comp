<?php

function Compress(string $file, string $outputname)
{
    $m = 0;
    $i = 25;
    $s = 6400;
    while (strlen($file)%$s > 0) $s--;
    $img = imagecreatetruecolor((($s)),round(strlen($file)/$s)+5);
    $black = imagecolorallocate($img, 1, 1, 1 );
    imagefill($img, 0, 0, $black);
    $rr = 0;
    $y = 0;
    imageantialias($img, false);
    
    while (strlen($file) > $m)
    {
        $r = ord($file[$m]);
        $black = ($r << 16) + ($r << 8) + $r;
        $m += 1;

        imagesetpixel($img,$rr,$y,$black);
        $rr++;
        
        if ($rr+1 >= (($s)))
        {
            $y += 1;
            $rr = 0;
        }
        if ($m < strlen($file))
        {
            $r = ord($file[$m]);
            $black = ($r << 16) + ($r << 8) + $r;
            $m += 1;

            imagesetpixel($img,$rr,$y,$black);
            $rr++;
        }
        if ($rr+1 >= (($s)))
        {
            $y += 1;
            $rr = 0;
        }
        if ($m < strlen($file))
        {
            $r = ord($file[$m]);
            $black = ($r << 16) + ($r << 8) + $r;
            $m += 1;

            imagesetpixel($img,$rr,$y,$black);
            $rr++;
        }
        if ($rr+1 >= (($s)))
        {
            $y += 1;
            $rr = 0;
        }
    }
    $int = (($s));
    $img = imagecrop($img,["x" => 0, "y" => 0, "width" => $int, "height" => ($y+1)]);
    //imagefilter($img,IMG_FILTER_GRAYSCALE);
    imagepng($img, "$outputname.png",9);

    echo "Compressed....";
    //exec("gm convert -define jpeg:optimize-coding=true -define webp:lossless=true -colors 256 -colorspace GRAY $outputname.png $outputname.webp");
    exec("cwebp -lossless -noalpha -q 100 $outputname.png -o $outputname.webp");
    unlink("$outputname.png");
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

        $rgb = imagecolorat($img,$rr,$y);

        $b = chr($rgb & 0xFF);

        $rr++;
        
        if ($rr+1 >= imagesx($img))  
        {
            $y += 1;
            $rr = 0;
        }
        $str .= $b;
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
    if (strlen($filestr) > 500000000)
    {
        Compress(substr($filestr,0,500000000), "$d$c");
        $filestr = substr($filestr,500000000);
    }
    else
    {
        Compress($filestr, "$d$c");
        $filestr = "";
    }
    if (chr($x) == 'Z')
    {
        $x = 64;
        $d++;
    }
    $x++;
}
Decompress("$d$c.webp","f.txt");

echo "done;";
?>
 
