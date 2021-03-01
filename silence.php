<?php

use App\Analyzer;
use App\XMLParser;

include 'src/XMLParser.php';
include 'src/Analyzer.php';

$parser = new XMLParser();
$parser->load("./test-source/silence1.xml");
$metadata = $parser->getMetadata();

$analyzer = new Analyzer(10, 5, 10);

$result = $analyzer->splitMetadata($metadata);

var_dump($result);



function printHelp()
{
    print "Command line arguments:\n";
    print " -f=/path/to/file.xml - path to file (can be absolute or relative);\n";
    print " -chapter-split-sec=1 - silence duration which reliably indicates a chapter transition, in seconds\n";
    print " -max-segment-duration=1 - The maximum duration of a segment, in seconds\n";
    print " -subchapter-split-sec=1 - A silence duration which can be used to split a long chapter\n";
}