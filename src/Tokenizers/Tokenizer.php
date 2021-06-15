<?php

namespace DNAHash\Tokenizers;

use Generator;

interface Tokenizer
{
    /**
     * Tokenize a sequence.
     *
     * @internal
     *
     * @param string $sequence
     * @return \Generator<string>
     */
    public function tokenize(string $sequence) : Generator;
}
