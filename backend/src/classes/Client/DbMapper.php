<?php

namespace Acme\Pay\Client;

class DbMapper
{
    /**
     * @var \stdClass
     */
    private $client;

    /**
     * @param \stdClass $client http://acmepay.local/schema/client.json
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function clientTableRow()
    {
        return [
            'name' => $this->client->name,
            'city' => isset($this->client->city) ? $this->client->city : null,
            'country' => isset($this->client->country) ? $this->client->country : null
        ];
    }
}
