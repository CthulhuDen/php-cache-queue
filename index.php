<?php

require __DIR__ . '/vendor/autoload.php';

$arguments = explode('/', $_SERVER['REQUEST_URI']);
array_shift($arguments);

$test = array_shift($arguments);

$tests = [
    'fake' => \App\Test\Fake::class,
    'cache' => \App\Test\Cache::class,
    'queue' => \App\Test\Queue::class,
];
/* @var \App\Test[] $tests */

if (!isset($tests[$test])) {
    throw new Exception('Unknown test');
}

$test = $tests[$test];
$test = $test::create();
$test->run(...$arguments);
