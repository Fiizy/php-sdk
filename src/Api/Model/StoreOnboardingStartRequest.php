<?php

namespace Fiizy\Api\Model;

/**
 * Store onboarding process start request.
 */
class StoreOnboardingStartRequest
{
    /** @var string ISO 639-1 two-letter language code */
    public $language;
    /** @var Store store data */
    public $store;
    /** @var Endpoints onboarding process endpoints */
    public $endpoints;
}
