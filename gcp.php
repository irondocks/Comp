<?php

$char_cnt = 5;

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

//    echo "1. Creating Dictionary...";

    $filemark = str_split($file, $xy);

    $rrr = [];
    foreach ($filemark as $file_i)
    {
        $pair = sequence($file_i);
        $rrr[] = $pair;
        $x = count($rrr);
        $rrr = array_unique($rrr);
        $num[] = ($x + 1 == count($rrr)) ? -1 : count($rrr);
    }

    echo ".";
//    echo "\n2. Writing Dictionary...";

    $num = [];
    $y = 0;
    fwrite($fout, implode($rrr));
    unset($imp);
//    while (count($num) < count($pairs))
//    {
//        foreach ($pairs as $a)
//        {
//            if (($a) == $file_un[$y%(count($file_un) + 1)]) {
//                $num[] = -1;
//                $y++;
//            }
//            else
//            {
//                $x = -1;
//                $y++;
//                foreach($file_un as $b)
//                {
//                    if ($b == $a)
//                    {
//                        $num[] = $x + 1;
//                        break;
//                    }
//                    $x++;
//                }
//            }
//            if ($x >= 0)
//                break;
//        }
//    }

//    echo "\n3. Compressing Mathematically...";
//
//    unset($pairs);

    output($fout, $num);

//    echo "\n4. Compression Complete\n";

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
    if (strlen($filestr) > 500000)
    {
        $fout = dictionary(substr($filestr,0,500000), $char_cnt, $fout);
        $filestr = substr($filestr,500000);
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
