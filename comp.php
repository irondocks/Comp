<?php

$char_cnt = 64;

function sequence(string $file)
{

    $file_c = str_split($file,1);
    $pairs = "";
    foreach ($file_c as $c) {
        $dcbn = decbin($c);
        if (strlen(decbin($c)) <= 4) {
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

    $rr = 0;
    $y = 0;
    $rh = 0;

    $file_un = array_unique($pairs);

    echo "\n2. Writing Dictionary...";

    $x = 0;

    $total_str = "";

    fwrite($fout, implode($file_un));
    while ($x < count($pairs))
    {
        foreach ($file_un as $a)
        {
            $z = $a;
            if ($a == $pairs[$x]) {
                $num[] = -1;
                $num[] = $y;
                $y++;
            }
            else {
                $num[] = $x;
                $y = 1;
                $x++;
                break;
            }
            $x++;
        }
    }

    echo "\n3. Compressing Mathematically...";

    fwrite($fout, $total_str);

    output($fout, $pairs, $num);

    echo "\n4. Compression Complete\n";

    return $fout;
}

function output(&$fout, array $pairs, array $nums)
{
    $order = 1;

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
            $z = $d;
            $total_str .= "|";
            for ( ; $z > 0 ; )
            {
                $total_str .= chr(($z)%256);
                $z >>= 8;
            }
            $order = 1;
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
