<?php

use App\RAG\SimilarityCalculator;
use InvalidArgumentException;

test('cosineSimilarity returns 1 for identical vectors', function () {
    $vec = [1.0, 2.0, 3.0, 4.0];
    $result = SimilarityCalculator::cosineSimilarity($vec, $vec);
    expect($result)->toBe(1.0);
});

test('cosineSimilarity returns 1 for proportional vectors', function () {
    $vecA = [1.0, 2.0, 3.0];
    $vecB = [2.0, 4.0, 6.0]; // 2x vecA
    $result = SimilarityCalculator::cosineSimilarity($vecA, $vecB);
    expect($result)->toBe(1.0);
});

test('cosineSimilarity returns 0 for orthogonal vectors', function () {
    $vecA = [1.0, 0.0, 0.0];
    $vecB = [0.0, 1.0, 0.0];
    $result = SimilarityCalculator::cosineSimilarity($vecA, $vecB);
    expect($result)->toBe(0.0);
});

test('cosineSimilarity returns -1 for opposite vectors', function () {
    $vecA = [1.0, 2.0, 3.0];
    $vecB = [-1.0, -2.0, -3.0];
    $result = SimilarityCalculator::cosineSimilarity($vecA, $vecB);
    expect($result)->toBe(-1.0);
});

test('cosineSimilarity returns correct value for known vectors', function () {
    $vecA = [3.0, 4.0];
    $vecB = [4.0, 3.0];
    // cos = (3*4 + 4*3) / (5 * 5) = 24/25 = 0.96
    $result = SimilarityCalculator::cosineSimilarity($vecA, $vecB);
    expect($result)->toBe(0.96);
});

test('cosineSimilarity throws for different dimensions', function () {
    $vecA = [1.0, 2.0, 3.0];
    $vecB = [1.0, 2.0];
    SimilarityCalculator::cosineSimilarity($vecA, $vecB);
})->throws(InvalidArgumentException::class, 'Vectors must have the same dimensions');

test('cosineSimilarity throws for empty vectors', function () {
    $vecA = [];
    $vecB = [1.0, 2.0];
    SimilarityCalculator::cosineSimilarity($vecA, $vecB);
})->throws(InvalidArgumentException::class);

test('cosineSimilarity returns 0 for zero vectors', function () {
    $vecA = [0.0, 0.0, 0.0];
    $vecB = [1.0, 2.0, 3.0];
    $result = SimilarityCalculator::cosineSimilarity($vecA, $vecB);
    expect($result)->toBe(0.0);
});

test('cosineSimilarity handles negative values correctly', function () {
    $vecA = [-1.0, -2.0];
    $vecB = [-2.0, -1.0];
    // cos = (2 + 2) / (sqrt(5) * sqrt(5)) = 4/5 = 0.8
    $result = SimilarityCalculator::cosineSimilarity($vecA, $vecB);
    expect(abs($result - 0.8) < 0.0001)->toBeTrue();
});

test('cosineSimilarity works with high dimensional vectors', function () {
    $vecA = array_fill(0, 100, 1.0);
    $vecB = array_fill(0, 100, 1.0);
    $result = SimilarityCalculator::cosineSimilarity($vecA, $vecB);
    expect($result)->toBe(1.0);
});

test('cosineSimilarity works with float precision', function () {
    $vecA = [0.1, 0.2, 0.3];
    $vecB = [0.1, 0.2, 0.3];
    $result = SimilarityCalculator::cosineSimilarity($vecA, $vecB);
    expect($result)->toBe(1.0);
});