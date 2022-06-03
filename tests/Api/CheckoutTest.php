<?php

namespace Fiizy\Api;

use Fiizy\Api\Exception\ApiClientException;
use Fiizy\Api\Model\LineItemStatus;
use Fiizy\Api\Model\LineItemSubType;
use Fiizy\Api\Model\Decimal;
use Fiizy\Api\Model\OrderStatus;
use PHPUnit\Framework\Assert;

class CheckoutTest extends ApiTestCase
{
    /**
     * @covers Checkout::status
     */
    public function test_checkout_status()
    {
        $serializer = $this->createSerializer();

        $expected = new \Fiizy\Api\Model\StatusResponse();
        $expected->reference = 'reference-1';
        $expected->number = 'number-1';
        $expected->status = 'status-1';
        $expected->amount = 100.55;
        $expected->currency = 'EUR';
        $expected->metadata = array(
            'meta-key-1' => 'value-1'
        );

        $secret = "private";
        $timestamp = time();
        $data = $serializer->serialize($expected);
        $payload = "{$timestamp}.{$data}";
        $signature = hash_hmac('sha256', $payload, $secret);

        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(200, [
            Util\Signature::HEADER_KEY => sprintf('t=%d, s=%s', $timestamp, $signature)
        ], sprintf('{
            "success":true,
            "data": %s
        }', $data)));

        $client = $this->createClient($mock);
        $client = $client->setAuthorizationKeys("public", $secret);

        $api = new Checkout($client);
        $response = $api->status("order-ref");

        Assert::assertEquals($expected, $response);
    }

    /**
     * @covers Checkout::start
     */
    public function test_checkout_start_unauthorized()
    {
        $request = new \Fiizy\Api\Model\CheckoutRequest();

        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(401, [], 'Unauthorized'));

        $client = $this->createClient($mock);
        $client = $client->setAuthorizationKeys("public", "private");

        $api = new Checkout($client);

        $this->expectException(ApiClientException::class);

        $api->start($request);
    }

    /**
     * @covers Checkout::start
     */
    public function test_checkout_start_error()
    {
        $request = new \Fiizy\Api\Model\CheckoutRequest();

        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(400, [], '{
            "success": false,
            "errors": [{"error": "order reference missing"}]
        }'));

        $client = $this->createClient($mock);
        $client = $client->setAuthorizationKeys("public", "private");

        $api = new Checkout($client);

        $this->expectException(ApiClientException::class);

        $api->start($request);
    }

    /**
     * @covers Checkout::start
     */
    public function test_checkout_start_request_format()
    {
        $item = new \Fiizy\Api\Model\LineItem();
        $item->type = \Fiizy\Api\Model\LineItemType::Product;
        $item->subType = LineItemSubType::PhysicalProduct;
        $item->reference = 'product-101';
        $item->status = LineItemStatus::InStock;
        $item->name = 'Product name';
        $item->description = 'Product description';
        $item->url = 'http://product-image-url';
        $item->imageUrl = 'http://product-url';
        $item->quantity = 1;
        $item->price = new Decimal(97.45);
        $item->taxRate = new Decimal(21.00);
        $item->totalAmount = new Decimal(92.45);
        $item->metadata = array('id' => 101);

        $shipping = new \Fiizy\Api\Model\LineItem();
        $shipping->type = \Fiizy\Api\Model\LineItemType::Fee;
        $shipping->subType = LineItemSubType::ShippingFee;
        $shipping->reference = 'shipping-1';
        $shipping->name = 'Flat rate';
        $shipping->quantity = 1;
        $shipping->price = new Decimal(11.85);
        $shipping->taxRate = new Decimal(21.00);
        $shipping->totalAmount = new Decimal(15.00);
        $shipping->metadata = array('id' => 1);

        $discount = new \Fiizy\Api\Model\LineItem();
        $discount->type = \Fiizy\Api\Model\LineItemType::Discount;
        $discount->subType = LineItemSubType::DiscountDiscount;
        $discount->reference = 'discount-1';
        $discount->name = 'Discount';
        $discount->quantity = 1;
        $discount->price = new Decimal(5.00);
        $discount->totalAmount = new Decimal(5.00);
        $discount->metadata = array();

        $request = new \Fiizy\Api\Model\CheckoutRequest();
        $request->order = new \Fiizy\Api\Model\Order();
        $request->order->reference = 'ref-1';
        $request->order->number = '#001';
        $request->order->status = OrderStatus::NewOrder;
        $request->order->currency = 'EUR';
        $request->order->taxAmount = new Decimal(22.56);
        $request->order->totalAmount = new Decimal(130.01);
        $request->order->metadata = array('id' => 1);
        $request->order->lineItems = array($item, $shipping, $discount);

        $request->customer = new \Fiizy\Api\Model\Customer();
        $request->customer->firstName = 'Johnny';
        $request->customer->lastName = 'Appleseed';
        $request->customer->email = 'johnny.appleseed@apple.com';
        $request->customer->phoneNumber = '+34917699100';

        $address = new \Fiizy\Api\Model\Address();
        $address->firstName = 'Johnny';
        $address->lastName = 'Appleseed';
        $address->phone = '+34917699100';
        $address->countryIso = 'ES';
        $address->postalCode = '28013';
        $address->state = 'Madrid';
        $address->city = 'Madrid';
        $address->addressLine1 = 'Prta del Sol';
        $address->addressLine2 = '1';

        $request->shippingAddress = $address;
        $request->billingAddress = $address;

        $request->endpoints = new \Fiizy\Api\Model\Endpoints();
        $request->endpoints->returnUrl = 'http://return-url';
        $request->endpoints->cancelUrl = 'http://cancel-url';
        $request->endpoints->webhookUrl = 'http://webhook-url';

        $request->client = new \Fiizy\Api\Model\Client();
        $request->client->ip = '172.20.0.1';
        $request->client->userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.55 Safari/537.36';

        $expected = new \Fiizy\Api\Model\CheckoutResponse();

        $serializer = $this->createSerializer();
        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(200, [], sprintf('{"success":true, "data": %s}', $serializer->serialize($expected))));

        $client = $this->createClient($mock);
        $client = $client->setAuthorizationKeys("90fedd67c6a6de2ac185abf84a6954a3ec6d23d5", "32ff7694244e12fa34abd187aef994169f107aaa");

        $api = new Checkout($client);
        $response = $api->start($request);

        Assert::assertEquals($expected, $response);
    }
}
