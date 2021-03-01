<?php

namespace App;

class Exporter
{
    /**
     * Export data to the file.
     *
     * @param array $data
     * @param string $path_to_file
     */
    public static function exportToTheFile(array $data, string $path_to_file)
    {
        $data = json_encode($data);

        $result = @file_put_contents($path_to_file, $data);

        if ($result === false) {
            throw new \RuntimeException("Can not write into {$path_to_file} file.");
        }
    }

    /**
     * Print data to the console directly.
     *
     * @param array $data
     * @param bool $as_array
     */
    public static function exportToTheConsole(array $data, $as_array = true)
    {
        if ($as_array) {
            var_dump($data);
        } else {
            echo "\n";
            echo json_encode($data);
            echo "\n";
        }
    }
}