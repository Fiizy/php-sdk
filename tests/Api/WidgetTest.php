<?php

namespace Fiizy\Api;

use DateTime;
use Fiizy\Api\Model\Decimal;
use Fiizy\Api\Model\WidgetRequest;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\RequestInterface;

class WidgetTest extends ApiTestCase
{
    /**
     * @covers Widget::get
     */
    public function test_get_widget()
    {
        $widget = '<div
            data-key="{{public_key}}"
            data-amount="{{amount}}"
            data-currecy="{{currency}}"
            data-locale="{{locale}}"
            data-style="{{style}}">
            <p>Some widget html here</p>
        </div>';

        $timestamp = new DateTime('now + 2 days');

        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(200, [
            'Expires' => $timestamp->format(DATE_RFC2822)
        ], $widget));

        $cache = new MockCache();
        $client = $this->createClient($mock, $cache);
        $client = $client->setAuthorizationKeys("public", "private");

        $request = new WidgetRequest();
        $request->url = 'https://widget.com/path/{{public_key}}/{{amount}}/{{currency}}/{{locale}}.html';
        $request->publicKey = 'pub-key';
        $request->amount = new Decimal(10.25);
        $request->currency = 'EUR';
        $request->locale = 'en_US';

        $api = new Widget($client);
        $response = $api->get($request, true, 'widget');

        Assert::assertEquals('<div
            data-key="pub-key"
            data-amount="10.25"
            data-currecy="EUR"
            data-locale="en_US"
            data-style="">
            <p>Some widget html here</p>
        </div>', $response);

        Assert::assertNotNull(array_shift($cache->ttl));

        /** @var RequestInterface $httpRequest */
        $httpRequest = array_shift($mock->requests);

        Assert::assertEquals('https://widget.com/path/pub-key/10.25/EUR/en_US.html', (string) $httpRequest->getUri());
    }
}
