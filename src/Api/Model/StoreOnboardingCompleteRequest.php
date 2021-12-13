<?php

namespace Fiizy\Api\Model;

/**
 * Store onboarding process complete request.
 */
class StoreOnboardingCompleteRequest
{
    /** @var string ISO 639-1 two-letter language code */
    public $language;
    /** @var string onboarding process hash */
    public $hash;
}
