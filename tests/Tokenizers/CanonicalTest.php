<?php

namespace DNAHash\Tests\Tokenizers;

use DNAHash\Tokenizers\Canonical;
use DNAHash\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Tokenizers
 * @covers \DNAHash\Tokenizers\Canonical
 */
class CanonicalTest extends TestCase
{
    /**
     * @var \DNAHash\Tokenizers\Canonical
     */
    protected $tokenizer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->tokenizer = new Canonical(new Kmer(6));
    }

    /**
     * @test
     */
    public function tokenize() : void
    {
        $tokens = $this->tokenizer->tokenize('CGGTTCAGCANG');

        $expected = ['CGGTTC', 'GGTTCA', 'CTGAAC', 'GCTGAA', 'TCAGCA'];

        $this->assertEquals($expected, iterator_to_array($tokens));
    }
}
