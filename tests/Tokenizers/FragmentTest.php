<?php

namespace DNATools\Tests\Tokenizers;

use DNATools\Tokenizers\Fragment;
use PHPUnit\Framework\TestCase;

/**
 * @group Tokenizers
 * @covers \DNATools\Tokenizers\Fragment
 */
class FragmentTest extends TestCase
{
    /**
     * @var \DNATools\Tokenizers\Fragment
     */
    protected $tokenizer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->tokenizer = new Fragment(4);
    }

    /**
     * @test
     */
    public function tokenize() : void
    {
        $expected = ['CGGT', 'TCAG', 'TAAT'];

        $tokens = $this->tokenizer->tokenize('CGGTTCAGCANGTAAT');

        $this->assertEquals($expected, iterator_to_array($tokens));
    }
}
