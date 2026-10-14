<?php

use App\Search\WebSearchProvider;
use Illuminate\Support\Facades\Config;

test('WebSearchProvider binding resolves correctly with config', function () {
    Config::set('ai.search.base_url', 'https://api.search.example.com');
    Config::set('ai.search.api_key', 'test-key');
    Config::set('ai.search.timeout', 10);

    $this->app->forgetInstance(WebSearchProvider::class);

    $provider = app(WebSearchProvider::class);

    expect($provider)->not->toBeNull();
    expect($provider)->toBeInstanceOf(\App\Search\SearchApiProvider::class);
});

test('WebSearchProvider binding returns null without config', function () {
    Config::set('ai.search.base_url', '');
    Config::set('ai.search.api_key', '');

    $this->app->forgetInstance(WebSearchProvider::class);

    $provider = app(WebSearchProvider::class);

    expect($provider)->toBeNull();
});