<?php

namespace Fiizy\Api;

use Fiizy\Api\Model\StoreOnboardingCompleteRequest;
use Fiizy\Api\Model\StoreOnboardingCompleteResponse;
use Fiizy\Api\Model\StoreOnboardingStartRequest;
use Fiizy\Api\Model\StoreOnboardingStartResponse;
use Fiizy\Api\Model\StoreStatusRequest;
use Fiizy\Api\Model\StoreStatusResponse;

/**
 * Store api requests.
 */
class Store
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
     * Get store status.
     *
     * @param StoreStatusRequest $request
     *
     * @return StoreStatusResponse
     *
     * @throws \Exception
     */
    public function status(StoreStatusRequest $request)
    {
        return $this->api->get(
            'merchant/store',
            (array) $request,
            StoreStatusResponse::class
        );
    }

    /**
     * Start store onboarding process.
     *
     * @param StoreOnboardingStartRequest $request
     *
     * @return StoreOnboardingStartResponse
     *
     * @throws \Exception
     */
    public function start(StoreOnboardingStartRequest $request)
    {
        return $this->api->post(
            'merchant/store',
            $request,
            StoreOnboardingStartResponse::class
        );
    }

    /**
     * Complete store onboarding process.
     *
     * @param StoreOnboardingCompleteRequest $request
     *
     * @return StoreOnboardingCompleteResponse
     *
     * @throws \Exception
     */
    public function complete(StoreOnboardingCompleteRequest $request)
    {
        return $this->api->put(
            'merchant/store',
            $request,
            StoreOnboardingCompleteResponse::class
        );
    }
}
