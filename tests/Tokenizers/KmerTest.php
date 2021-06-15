<?php

namespace DNAHash\Tests\Tokenizers;

use DNAHash\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Tokenizers
 * @covers \DNAHash\Tokenizers\Kmer
 */
class KmerTest extends TestCase
{
    /**
     * @var \DNAHash\Tokenizers\Kmer
     */
    protected $tokenizer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->tokenizer = new Kmer(6);
    }

    /**
     * @test
     */
    public function tokenize() : void
    {
        $tokens = $this->tokenizer->tokenize('CGGTTCAGCANG');

        $expected = ['CGGTTC', 'GGTTCA', 'GTTCAG', 'TTCAGC', 'TCAGCA'];

        $this->assertEquals($expected, iterator_to_array($tokens));
    }
}
