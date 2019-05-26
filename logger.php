<?php
/*
 * PHP EpSolar Tracer Class (PhpEpsolarTracer) v1.0
 *
 * May 2019
 * windyhen@outlook.com
 */

//EPEver tracer php library
require_once 'PhpEpsolarTracer.php';
//influxDB php client library
require 'vendor/autoload.php';

//Define IP of influxDB
$host = 'localhost';

//define db name
$dbname = 'db_solar';

//influx API write
$influx_url = "http://localhost:8086/write?db=" . $dbname;

$tracer = new PhpEpsolarTracer('/dev/ttyUSB0');
$client = new InfluxDB\Client($host, 8086, "root", "root");

$db = $client->selectDB("logger");

//'http://localhost:8086/write?db=mydb' --data-binary 'cpu_load_short,host=server01,region=us-west value=0.64 1434055562000000000'

print "\n Realtime Data\n";

if ($tracer->getRealtimeData()) {
    for ($i = 0; $i < count($tracer->realtimeData); $i++) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $influx_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $Item = $tracer->realtimeData[$i];
        $Key_Name = str_replace(" ", "-", $tracer->realtimeKey[$i]);
        print str_pad($i, 2, '0', STR_PAD_LEFT) . " " . $Key_Name . " " . $Item . "\n";
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$Key_Name,unit=Realtime value=$Item");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        $result = curl_exec($ch);
    }
} else print "Cannot get RealTime Data\n";

print "\n Statistical Data\n";

if ($tracer->getStatData()) {
    for ($i = 0; $i < count($tracer->statData); $i++) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $influx_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST,           1);
        $Item = $tracer->statData[$i];
        $Key_Name = str_replace(" ", "-", $tracer->statKey[$i]);
        $Key_Name = 'Stat-' . $Key_Name;
        print str_pad($i, 2, '0', STR_PAD_LEFT) . " " . $Key_Name . " " . $Item . "\n";
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$Key_Name,unit=statData value=$Item");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        $result = curl_exec($ch);
    }
} else print "Cannot get Statistical Data\n";
