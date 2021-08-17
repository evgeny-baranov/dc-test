<?php
namespace DcTest;

require 'vendor/autoload.php';
$p = new TestParser();

global $argv, $argc;
$self = array_shift($argv);
if ($argc < 2) {
    echo "usage: $self <url> [<proxy> [<port>]]";
    exit (0);
}

$url = array_shift($argv);
$proxy = array_shift($argv) ?? null;
$port = array_shift($argv) ?? 3128;

if ($proxy) {
    $p->setProxy($proxy, $port);
}

$p->parse($url);