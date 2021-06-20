<?php

namespace DNATools\Extractors;

interface Writable
{
    /**
     * Export a sequence dataset.
     *
     * @param iterable<string> $iterator
     */
    public function export(iterable $iterator) : void;
}
