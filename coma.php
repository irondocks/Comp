<?php

$char_cnt = 24;

function sequence(string $file)
{
//    global $char_cnt;
//    return
    $file_c = str_split($file,1);
    $pairs = "";
    foreach ($file_c as $c) {
        $dcbn = decbin(ord($c));
        if (strlen($dcbn) < 5) {
            $dcbn = "1" . str_repeat("0",4-strlen($dcbn));;
            // $dcbn .= str_repeat("0",4-strlen($dcbn));
        }
        else {
            $dcbn = "0" . str_repeat("0",8-strlen($dcbn));;
            // $dcbn .= str_repeat("0",9-strlen($dcbn));
        }
        $pairs = ($dcbn) . $pairs;
    }
    $string = "";
    while (strlen($pairs) > 8)
    {
        $string .= chr(bindec(substr($pairs,0,8)));
        $pairs = substr($pairs,8);
    }
    // $string = chr(bindec($pairs));
    return $string;
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

    echo "1. Creating Dictionary...";

    $filemark = str_split($file, $chars);

    $rrr = [];
    $num = [];
    $x = 0;
    foreach ($filemark as $file_i)
    {
        $pair = sequence($file_i);
        $x = count($rrr);
        if (!in_array($pair,$rrr)) {
            $rrr[] = $pair;
        }
    //      Was $rrr appended to?    No? Where is the match?      Yes? This will be ordinal anyway, so, -1
        $num[] = ($x == count($rrr)) ? array_search($pair, $rrr): -1;
    }

    echo ".";
    echo "\n2. Writing Dictionary...";

    $y = 0;
    $hhh = "";
    $total_str = implode($rrr);
    $total_str = str_split($total_str,8);
    $total_numbers = implode($num);
    $total_numbers = str_split($total_numbers,8);
    foreach ($total_str as $value)
    {
        $hhh .= chr(bindec($value));
    }
    unset($rrr);

    fwrite($fout, $hhh);
    $hhh = "";
    foreach ($total_numbers as $value)
    {
        $hhh .= chr(bindec($value));
    }
    echo "\n3. Compressing Mathematically...";
    fwrite($fout, $hhh);
    // output($f    out, $total_numbers);

    echo "\n4. Compression Complete\n";
}

function output(&$fout, array $nums)
{
    $order = 0;

    $total_str = "";
    foreach ($nums as $d)
    {
        if ($d == -1) {
            $order++;
        }
        else {
            $z = $order;
            for ( ; $z > 0 ; )
            {
                $total_str .= (($z)%256);
                $z >>= 8;
            }
            $total_str .= "?";

            $total_str .= "$d|";
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
    if (strlen($filestr) > 10000000)
    {
        dictionary(substr($filestr,0,10000000), $char_cnt, $fout);
        $filestr = substr($filestr,10000000);
    }
    else
    {
        dictionary($filestr, $char_cnt, $fout);
        $filestr = "";
    }
}
fclose($fout);
echo "This file was compressed in ";
echo date("i:s",((hrtime(true) - $time) / 1000000000));
echo "\ndone\n";
?>
