#!/usr/bin/php
<?php
$url = 'http://192.168.0.136/cgi-bin/refreshPrg.cgi';
$username = 'admin';
$password = 'admin';
$outputFile = './output.m3u';

/*********************[Edit Above]*******************************/

$ch = curl_init();

$chOptions = [
    CURLOPT_URL => $url,
    CURLOPT_USERPWD => $username . ':' . $password,
    CURLOPT_RETURNTRANSFER => true,
];

curl_setopt_array($ch, $chOptions);
$out = curl_exec($ch);
curl_close($ch);

$file = $out;
//$file = file_get_contents('./refreshPrg.cgi.txt');

$tuners = explode('^-^', $file);

$output = '#EXTM3U' . PHP_EOL;

foreach($tuners as $tuner) {
    $rows = explode(';', $tuner);

    unset($rows[0], $rows[1], $rows[2], $rows[3], $rows[4], $rows[5], $rows[6]);
    array_pop($rows);

    foreach($rows as $channel) {
        $channel = explode(',', $channel);

        if (filter_var($channel[12], FILTER_VALIDATE_IP) === false) {
            continue;
        }

        $channel[3] = base64_decode($channel[3]);

        if (substr($channel[6], 0, 2) !== '0x') {
            $channel[6] = base64_decode($channel[6]);
        } else if (substr($channel[7], 0, 2) !== '0x') {
            $channel[7] = base64_decode($channel[7]);
        }

        $output .= '#EXTINF:-1 group-title="' . $channel[7] . '",' . $channel[3] . '
udp://' . $channel[12] . ':' . $channel[13] . PHP_EOL;
    }
}

file_put_contents($outputFile, $output);
