<?php

namespace Fiizy\Api;

use Fiizy\Api\Model\CheckoutRequest;
use Fiizy\Api\Model\CheckoutResponse;
use Fiizy\Api\Model\StatusResponse;

/**
 * Checkout api requests.
 */
class Checkout
{
    /**
     * @var Client api client
     */
    protected $api;

    /**
     * API client.
     *
     * @param Client $api
     */
    public function __construct(Client $api)
    {
        $this->api = $api;
    }

    /**
     * Start checkout.
     *
     * @param CheckoutRequest $data
     *
     * @return CheckoutResponse
     *
     * @throws \Exception
     */
    public function start(CheckoutRequest $data)
    {
        return $this->api->post(
            'merchant/store/checkout',
            $data,
            CheckoutResponse::class
        );
    }

    /**
     * Update checkout.
     *
     * @param string $orderReference
     * @param CheckoutRequest $data
     *
     * @return void
     *
     * @throws \Exception
     */
    public function update($orderReference, CheckoutRequest $data)
    {
        $this->api->patch(
            sprintf('merchant/store/checkout/%s', $orderReference),
            $data
        );
    }

    /**
     * Cancel checkout by order reference.
     *
     * @param string $orderReference
     *
     * @return void
     *
     * @throws \Exception
     */
    public function cancel($orderReference)
    {
        $this->api->delete(
            sprintf('merchant/store/checkout/%s', $orderReference)
        );
    }

    /**
     * Get checkout status by order reference.
     *
     * @param string $orderReference
     *
     * @return StatusResponse
     *
     * @throws \Exception
     */
    public function status($orderReference)
    {
        return $this->api->get(
            sprintf('merchant/store/checkout/%s', $orderReference),
            null,
            StatusResponse::class,
            true
        );
    }
}
