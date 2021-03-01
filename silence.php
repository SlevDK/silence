<?php

use App\Analyzer;
use App\XMLParser;
use App\Exporter;

include 'src/Exporter.php';
include 'src/Analyzer.php';
include 'src/XMLParser.php';

if ($argv[1] == '-h' || $argv[1] == '--h') {
    printHelp();
    die();
}

$file = $argv[1];
$chapter_split = (int) $argv[2];
$subchapter_split = (int) $argv[3];
$max_segment_duration = (int) $argv[4];

$parser = new XMLParser();
$parser->load($file);
$metadata = $parser->getMetadata();

$analyzer = new Analyzer($chapter_split, $subchapter_split, $max_segment_duration);
$podcast = $analyzer->splitMetadata($metadata);

//Exporter::exportToTheFile($podcast, "./test-output.txt");
Exporter::exportToTheConsole($podcast, true);



function printHelp()
{
    print "Command line arguments:\n";
    print " first argument - path to file (example silence.php some/file.xml);\n";
    print " second argument - silence duration which reliably indicates a chapter transition, in seconds\n";
    print " third argument - The maximum duration of a segment, in seconds\n";
    print " next argument - A silence duration which can be used to split a long chapter\n";
    print "\n\n";
    print "Example: php silence.php ./test-source/silence1.xml 10 5 50";
    print "\n";
}