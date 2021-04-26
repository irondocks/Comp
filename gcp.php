<?php

$char_cnt = 9;

function sequence(string $file)
{
//    global $char_cnt;
//    return
    $file_c = str_split($file,1);
    $pairs = "";
    foreach ($file_c as $c) {
        $dcbn = decbin($c);
        if (strlen($dcbn) <= 4) {
            $dcbn = "1" . $dcbn;
            $dcbn .= str_repeat("0",5-strlen($dcbn));
        }
        else {
            $dcbn = "0" . $dcbn;
            $dcbn .= str_repeat("0",9-strlen($dcbn));
        }
        $pairs = ($dcbn) . $pairs;
    }
    return $pairs;
}

// NEEDS FULL REWRITE
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

function dictionary(string $file, int $chars, &$fout)
{

    $pairs = [];
    global $char_cnt;
    $xy = $char_cnt;

    echo "1. Creating Dictionary...";

    $filemark = str_split($file, $xy);

    foreach ($filemark as $file_i)
    {
        $pairs[] = sequence($file_i);
    }

    $file_un = array_unique($pairs);

    echo "\n2. Writing Dictionary...";

    $x = 0;

    $total_str = "";
    $num = [];
    $y = 0;
    $imp = $file_un;
    fwrite($fout, implode($imp));
    while (count($num) < count($pairs))
    {
        $x = 0;
        while ($x >= $y && $x < count($file_un) && $file_un[$x] != $pairs[$y])
            $x++;
        while ($x >= $y && $y < count($pairs) && $x < count($file_un) && $file_un[$x] == $pairs[$y])
        {
            $num[] = -1;
            $x++;
            $y++;
        }
        if ($y < count($pairs))
            $num[] = array_search($pairs[$y], $file_un);
        $y++;
    }

    echo "\n3. Compressing Mathematically...";

    unset($pairs);

    output($fout, $num);

    echo "\n4. Compression Complete\n";

    return $fout;
}

function output(&$fout, array $nums)
{
    $order = 0;

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
                $total_str .= chr(($z)%256);
                $z >>= 8;
            }
            if ($order == 1) {
                continue;
            }
            $z = $d;
            $total_str .= "|";
            for ( ; $z > 0 ; )
            {
                $total_str .= chr(($z)%256);
                $z >>= 8;
            }
            $order = 0;
        }
    }
    fwrite($fout,$total_str);
}

function Decompress(string $filename, string $output = "f.txt")
{

}

$filestr = file_get_contents($argv[1]);
$time = hrtime(true);
file_put_contents($argv[2].".xiv","");
$fout = fopen($argv[2].".xiv","w+");
while (strlen($filestr) > 0)
{
    if (strlen($filestr) > 90000000)
    {
        $fout = dictionary(substr($filestr,0,90000000), $char_cnt, $fout);
        $filestr = substr($filestr,90000000);
    }
    else
    {
        $fout = dictionary($filestr, $char_cnt, $fout);
        $filestr = "";
    }
}
fclose($fout);
echo "This file was compressed in ";
echo date("i:s",((hrtime(true) - $time) / 1000000000));
echo "\ndone\n";
?>
