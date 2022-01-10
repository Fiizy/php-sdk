# Fiizy PHP SDK

[![Fiizy](https://www.fiizy.com/images/fiizy-logo.png)](https://fiizy.com)

[Fiizy](https://fiizy.com) financing solutions.

## Installation

You can use [Composer](https://getcomposer.org):

```bash
composer require fiizy/fiizy-api-sdk
```

## Usage

```php
$item = new LineItem();
$item->type = LineItemType::Product;
$item->subType = LineItemSubType::PhysicalProduct;
$item->reference = 'product-101';
$item->status = 'in_stock';
$item->name = 'Product name';
$item->description = 'Product description';
$item->url = 'http://product-url';
$item->imageUrl = 'http://product-image-url';
$item->quantity = 1;
$item->price = 97.45;
$item->taxRate = 21.00;
$item->totalAmount = 117.91;
$item->metadata = array('id' => 101);

$shipping = new LineItem();
$shipping->type = LineItemType::Fee;
$shipping->subType = LineItemSubType::ShippingFee;
$shipping->reference = 'shipping-1';
$shipping->name = 'Flat rate';
$shipping->quantity = 1;
$shipping->price = 11.85;
$shipping->taxRate = 21.00;
$shipping->totalAmount = 14.34;
$shipping->metadata = array('id' => 1);

$discount = new LineItem();
$discount->type = LineItemType::Discount;
$discount->subType = LineItemSubType::DiscountDiscount;
$discount->reference = 'discount-1';
$discount->name = 'Discount';
$discount->quantity = 1;
$discount->price = 5.00;
$discount->totalAmount = 5.00;
$discount->metadata = array();

$request = new CheckoutRequest();
$request->order = new Order();
$request->order->reference = 'ref-1';
$request->order->number = '#001';
$request->order->status = OrderStatus::NewOrder;
$request->order->currency = 'EUR';
$request->order->taxAmount = 22.95;
$request->order->totalAmount = 127.25;
$request->order->metadata = array('id' => 1);
$request->order->lineItems = array($item, $shipping, $discount);

$request->customer = new Customer();
$request->customer->firstName = 'Johnny';
$request->customer->lastName = 'Appleseed';
$request->customer->email = 'johnny.appleseed@fiizy.com';
$request->customer->phoneNumber = '+34111111111';

$request->shippingAddress = new Address();
$request->shippingAddress->firstName = 'Johnny';
$request->shippingAddress->lastName = 'Appleseed';
$request->shippingAddress->phone = '+34111111111';
$request->shippingAddress->countryIso = 'ES';
$request->shippingAddress->postalCode = '28013';
$request->shippingAddress->state = 'Madrid';
$request->shippingAddress->city = 'Madrid';
$request->shippingAddress->addressLine1 = 'Prta del Sol';
$request->shippingAddress->addressLine2 = '1';

$request->billingAddress = $request->shippingAddress;

$request->endpoints = new Endpoints();
$request->endpoints->returnUrl = 'http://return-url';
$request->endpoints->cancelUrl = 'http://cancel-url';
$request->endpoints->webhookUrl = 'http://webhook-url';

$request->client = new Client();
$request->client->ip = '127.0.0.1';
$request->client->userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.55 Safari/537.36';

$normalizer = new ObjectNormalizer();
$client = new Client();
$client->setHttpClient(new CurlHttpClient('Fiizy-PHP-SDK/1.0.0'));
$client->setSerializer(new SimpleSerializer(array($normalizer)));
$client->setDenormalizer($normalizer);
$client->setAuthorizationKeys('public-key', 'private-key');
$checkout = new Checkout($client);
$response = $checkout->start($request);

header('Location: ' . $response->redirectUrl);
```

## Documentation

