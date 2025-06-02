<?php

namespace App;

use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

readonly class UrlChecker
{
    public function __construct(private Client $httpClient, private Document $document)
    {
    }
    
    public function check(string $url): array
    {
        $response = $this->httpClient->request('GET', $url, ['timeout' => 3]);

        $data = $this->content($response->getBody()->getContents());

        return array_merge(
            ['status_code' => $response->getStatusCode()],
            $data
        );
    }

    private function content(string $content): array
    {
        $this->document->loadHtml($content);
        $h1 = $this->document->first('h1');
        $title = $this->document->first('title');
        $description = $this->document->first('meta[name="description"]');

        return [
            'h1' => $h1 ? optional($h1)->text() : null,
            'title' => $title ? optional($title)->text() : null,
            'description' => $description ? $description->getAttribute('content') : null,
        ];
    }
}
