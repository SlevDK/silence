# silence - divide an audio stream by silence
Test project

Run `silence.php` without arguments or with `-h` `--h` to see help.

Command line arguments:
- `-file=/path/to/file` - path to file (example silence.php -file=some/file.xml)
- `-sd=10` - silence duration which reliably indicates a chapter transition, in seconds
- `-sp=5` - A silence duration which can be used to split a long chapter
- `-sd=50` - The maximum duration of a segment, in seconds
- `-export=/path/to/file` - Export processed data to the file. Print data to the console if arg does not provided
- `-format=json|array` - Output format (only for console output), array by default

Example: `php silence.php -file=./test-source/silence1.xml -sc=10 -sp=5 -sd=50 -export=./podcast-output.json`
