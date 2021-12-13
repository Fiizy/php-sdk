<?php

namespace Fiizy\Http;

interface HttpClientInterface extends ClientInterface,
                                      Message\UriFactoryInterface,
                                      Message\RequestFactoryInterface,
                                      Message\StreamFactoryInterface
{
}
