<?php

namespace Fiizy\Http;

/**
 * HTTP Client interface that combines multiple http messaging related interfaces together.
 */
interface HttpClientInterface extends ClientInterface,
                                      Message\UriFactoryInterface,
                                      Message\RequestFactoryInterface,
                                      Message\StreamFactoryInterface
{
}
