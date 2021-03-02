<?php

namespace App;

interface ParserInterface
{
    public function load(string $path_to_file): self;

    public function getMetadata(): array;
}