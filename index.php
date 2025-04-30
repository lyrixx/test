<?php

require __DIR__ . '/vendor/autoload.php';

use RobotsTxtParser\RobotsTxtParser;
use Symfony\Component\DomCrawler\Crawler;

// new RobotsTxtParser('');

$content = file_get_contents(__DIR__ . '/response.html');

$crawler = new Crawler($content, useHtml5Parser: false);

echo $crawler->filterXPath('descendant-or-self::title')->text();
