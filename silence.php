<?php

use App\Analyzer;
use App\XMLParser;
use App\Exporter;

include 'src/Exporter.php';
include 'src/Analyzer.php';
include 'src/XMLParser.php';

if (!isset($argv[1]) || $argv[1] == '-h' || $argv[1] == '--h') {
    printHelp();
    die(0);
}

try {
    $arguments = parseArguments($argv);
} catch (\Exception $e) {
    echo "\n";
    echo $e->getMessage();
    echo "\n";
    die();
}

try {

    $parser = new XMLParser();
    $parser->load($arguments['file']);
    $metadata = $parser->getMetadata();

    $analyzer = new Analyzer(
        $arguments['silence_chapter'], $arguments['silence_part'], $arguments['segment_duration']
    );
    $podcast = $analyzer->splitMetadata($metadata);

    if ($arguments['export_to'] !== '') {
        Exporter::exportToTheFile($podcast, $arguments['export_to']);
        die(0);
    }

    $as_array = $arguments['format'] == 'array';
    Exporter::exportToTheConsole($podcast, $as_array);

} catch (\Exception $e) {
    echo "\n";
    echo $e->getMessage();
    echo "\n";
}

function printHelp()
{
    print "Command line arguments:\n";
    print " -file=/path/to/file - path to file (example silence.php -file=some/file.xml);\n";
    print " -sd=10 - silence duration which reliably indicates a chapter transition, in seconds\n";
    print " -sp=5 - A silence duration which can be used to split a long chapter\n";
    print " -sd=50 - The maximum duration of a segment, in seconds\n";
    print " -export=/path/to/file - Export processed data to the file. Print data to the console if arg does not provided\n";
    print " -format=json|array - Output format (only for console output), array by default\n";
    print "\n\n";
    print "Example: php silence.php -file=./test-source/silence1.xml -sc=10 -sp=5 -sd=50";
    print "\n";
}

function parseArguments($argv)
{
    $arguments = [
        'file' => '',
        'silence_chapter' => 0,
        'silence_part' => 0,
        'segment_duration' => 0,
        'export_to' => '',
        'format' => 'array',
    ];

    unset($argv[0]); // current filename
    foreach($argv as $arg) {
        $arg = explode('=', $arg);

        switch($arg[0]) {
            case '-file':
                $arguments['file'] = $arg[1] ?? '';
                break;
            case '-sc':
                $arguments['silence_chapter'] = $arg[1] ?? 0;
                break;
            case '-sp':
                $arguments['silence_part'] = $arg[1] ?? 0;
                break;
            case '-sd':
                $arguments['segment_duration'] = $arg[1] ?? 0;
                break;
            case '-export':
                $arguments['export_to'] = $arg[1] ?? '';
                break;
            case '-format':
                $arguments['format'] = $arg[1] == 'json' ? 'json' : 'array';
                break;
            default:
                throw new \InvalidArgumentException("Unrecognized option {$arg[0]}");
        }
    }

    return $arguments;
}