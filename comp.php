<?php

$char_cnt = 5;

function sequence(string $file, double $total_t = NULL)
{

    $file_c = str_split($file,1);

    $u  = 0;
    $total_t = "";
    foreach ($file_c as $c)
    {
        $total_t = decbin($c) . $total_t;
        //$total_t += (ord($c) << (2**(8*$u)));
        $u++;
    }
    $pairs = $total_t;
    return $pairs;
}

function unsequence(int $number, int $max)
{
    global $char_cnt;
    $seq = [];

    while (count($seq) < $char_cnt)
    {
        unshift($seq,$number%256);
        $number >>= 8;
    }

    return implode($seq);
}

function dictionary(string $file, int $chars, $fout)
{

    $pairs = [];
    global $char_cnt;
    $xy = $char_cnt;

    echo "1. Creating Dictionary...";

    $filemark = str_split($file, $xy);

    foreach ($filemark as $file_i) {
        $pairs[] = sequence($file_i);
    }

    $rr = 0;
    $y = 0;
    $rh = 0;

    $file_un = array_unique($pairs);

    echo "\n2. Writing Dictionary...";

    $x = 0;

    $total_str = "";
    foreach ($file_un as $a)
    {
        $z = $a;
        if ($a == $filemark[$x])
            $num[] = -1;
        else {
            $num[] = $x;
            $x++;
        }
        for ( ; strlen($z) > 8 ; )
        {
            $d = substr($z,0,8);
            $total_str .= chr(bindec($d%256));
            $z = substr($z,8);
        }
        $d = $z;
        if (strlen($d) > 0)
            $total_str .= chr(bindec($d%256));
        $x++;
    }

    echo "\n3. Compressing Mathematically...";
    $y++;

    fwrite($fout,$total_str);

    output($fout, $pairs, $num);

    echo "\n4. Compression Complete\n";
//    fclose($fout);
    return $fout;
}

function output($fout, array $pairs, array $nums)
{
    $mns = 0;
    $order = 1;
    $x = 0;
    $total_str = "";
    foreach ($nums as $d)
    {
        if ($d == -1) {
            $order++;
            continue;
        }
        else {
            $z = $order;
            $total_str .= "|";
            for ( ; $z > 0 ; )
            {

                $total_str .= chr($z%256);
                $z >>= 8;
            }
            $z = $d;
            $total_str .= "|";
            for ( ; $z > 0 ; )
            {

                $total_str .= chr($z%256);
                $z >>= 8;
            }
            $order = 1;
        }
    }
    fwrite($fout,$total_str);
}

function Decompress(string $filename, string $output = "f.txt")
{
    $str = "";
    $rr = 0;
    $y = 0;
    $f = fopen($output,"w");
    $i = 0;
    $img = imagecreatefrompng("$filename");
    while (imagesy($img) > $y)
    {
        while (imagesx($img) > $rr + 1) {
            $b = 0;
            $x = 0;
            $total_max = imagecolorat($img,$rr,$y);
            $rr++;
            for ($x = 0 ; $x < 3 && $rr + $x + 1 < imagesx($img) ; $x++)
            {
                $rgb = imagecolorat($img,$rr+$x,$y);
                $b <<= 8;
                $b += ($rgb - 1);
            }
            $str .= unsequence($b, $total_max);
            $rr += $x;
        }
        $rr = 0;
        $y++;
    }

    while (substr($str,-1) == chr(0))
        $str = substr($str,0, strlen($str) - 1);

    fwrite ($f,$str);
    imagedestroy($img);

}

$filestr = file_get_contents($argv[1]);
$x = 65;
$d = 0;
$c = "";
$time = hrtime(true);
file_put_contents($argv[2].".xiv","");
$fout = fopen($argv[2].".xiv","w+");
while (strlen($filestr) > 0)
{
    $c = chr($x);
    if (strlen($filestr) > 90000000)
    {
        $fout = dictionary(substr($filestr,0,90000000), $char_cnt, $fout);
        $filestr = substr($filestr,90000000);
        //Decompress("$d$c.webp","f.txt");
    }
    else
    {
        $fout = dictionary($filestr, $char_cnt, $fout);
        $filestr = "";
        //Decompress("$d$c.png","f.txt");
    }
//    if (chr($x) == 'Z')
//    {
//        $x = 64;
//        $d++;
//    }
//    $x++;
}
fclose($fout);
echo "This file was compressed in ";
echo date("i:s",((hrtime(true) - $time) / 1000000000));
echo "\ndone\n";
?>
