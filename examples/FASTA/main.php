<?php

include __DIR__ . '/../../vendor/autoload.php';

use DNAHash\Extractors\Concatenator;
use DNAHash\Extractors\FASTA;
use DNAHash\DNAHash;
use DNAHash\Tokenizers\Canonical;
use DNAHash\Tokenizers\Kmer;

$extractor = new Concatenator([
    new FASTA('datasets/exon-part1.fa'),
    new FASTA('datasets/exon-part2.fa'),
    new FASTA('datasets/exon-part3.fa'),
    new FASTA('datasets/exon-part4.fa'),
    new FASTA('datasets/exon-part5.fa'),
    new FASTA('datasets/exon-part6.fa'),
]);

$hashTable = new DNAHash(0.001);

echo 'Counting sequences ...' . PHP_EOL;

$hashTable->import($extractor, new Canonical(new Kmer(25)));

file_put_contents('results.json', json_encode([
    'top10' => $hashTable->top(10),
    'numSingletons' => $hashTable->numSingletons(),
    'numNonSingletons' => $hashTable->numNonSingletons(),
    'numUniqueSequences' => count($hashTable),
    'totalSequences' => $hashTable->totalSequences(),
    'filter' => [
        'numHashes' => $hashTable->filter()->numHashes(),
        'numLayers' => $hashTable->filter()->numLayers(),
        'size' => $hashTable->filter()->size(),
        'capacity' => $hashTable->filter()->capacity(),
        'falsePositiveRate' => $hashTable->filter()->falsePositiveRate(),
    ],
], JSON_PRETTY_PRINT));

echo 'Results saved to results.json' . PHP_EOL;
