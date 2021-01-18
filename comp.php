<?php

function Compress(string $file, string $outputname)
{
    $m = 0;
    $s = 15000;
    while ($s > strlen($file))
    {
        $s /= 2;
        $s = floor($s);
    }
    $img = imagecreatetruecolor((($s)),15000);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $white);
    $rr = 0;
    $y = 0;
    //imageantialias($img, false);
    
    while (strlen($file) > $m)
    {
        $r = ord($file[$m]);
        $m += 1;
        for ($x = 3 ; $x > 0 ; $x--)
        {
            $z = ($r%8) + 1;
            imagesetpixel($img,$rr+$x-1,$y,$z);
            $r >>= 3;
        }
        $rr+=3;
        
        if ($rr+1 >= (($s)))
        {
            $y += 1;
            $rr = 0;
        }
    }
    $int = $s;
    $img = imagecrop($img,["x" => 0, "y" => 0, "width" => $int, "height" => ($y+1)]);
    imageantialias($img, false);
    //imagefilter($img, IMG_FILTER_CONTRAST, -255);
    imagepng($img, "$outputname.png", 9);
    echo "Compressed....";
    exec("gm convert $outputname.png -define webp:lossless=true $outputname.webp");
    // unlink("$outputname.png");
    imagedestroy($img);
}

function Decompress(string $filename, string $output = "f.txt")
{
    $str = "";
    $rr = 0;
    $y = 0;
    $f = fopen($output,"w");
    $i = 0;
    echo "&&";
    $img = imagecreatefrompng("$filename");
    while (imagesy($img) > $y)
    {
        while (imagesx($img) > $rr + 1) {
            $b = 0;
            for ($x = 0 ; $x < 3 && $rr + $x + 1 < imagesx($img) ; $x++)
            {
                $rgb = imagecolorat($img,$rr+$x,$y);
                $b <<= 3;
                if ($rgb > 8 || $rgb == 0)
                    continue;
                $b += ($rgb - 1);
            }
            echo " " . ($b);
            $b = chr($b%256);
            $str .= $b;
            $rr += 3;
        }
        $rr = 0;
        $y++;
    }
    // rtrim($str,"\x00");
    // while (substr($str,-1) == chr(1))
    //     $str = substr($str,0, strlen($str) - 1);
    while (substr($str,-1) == chr(0))
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
        Decompress("$d$c.png","f.txt");
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
 
