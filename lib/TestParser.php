<?php

namespace DcTest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;

class TestParser {

    /**
     * @var string
     */
    private $proxy;

    /**
     * @var \DOMXPath
     */
    private $xpath;

    /**
     * @var string
     */
    private $url;

    /**
     * @var Client
     */
    private $guzzle;

    public function __construct() {
        $this->guzzle = new Client();
    }

    private $tasks = [
        '<а>' => '//a/@href',
        '<img>' => '//img/@src',
        '<script>' => '//script/@src',
        '<link>' => '//link/@href',
    ];

    /**
     * @param string $url
     * @throws GuzzleException
     */
    public function parse(string $url) {
        $this->loadDocumentData($url);

        foreach ($this->tasks as $name => $task) {
            echo $name . PHP_EOL;
            foreach ($this->proceedTask($task) as $resultString) {
                echo $resultString . PHP_EOL;
            }
            echo PHP_EOL;
        }
    }

    /**
     * @param string $url
     * @throws GuzzleException
     */
    public function loadDocumentData(string $url): void {
        $this->url = $url;

        $doc = new \DOMDocument();
        @$doc->loadHTML($this->loadHttpData($this->url));
        $this->xpath = new \DOMXPath($doc);
    }

    private function proceedTask(string $path): array {
        $query = $this->xpath->query($path);

        $result = [];
        /** @var \DOMNode $node */
        foreach ($query as $node) {

            $result[] = UriResolver::resolve(new Uri($this->url), new Uri($node->nodeValue));
        }

        return $result;
    }

    /**
     * @param string $url
     * @return string
     * @throws GuzzleException
     */
    public function loadHttpData(string $url): string {
        $options = [];
        if ($this->proxy) {
            $options['proxy'] = $this->proxy;
        }
        return $this->guzzle->get($url, $options)->getBody()->getContents();
    }

    public function setProxy(string $host, ?int $port = 3128) {
        $this->proxy = "$host:$port";
    }
}