<?php

namespace Acme\Pay;

class CommonTest extends Test\WebTestCase
{

    public function testIndexPageWorks()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains(
            'Welcome to ACME pay',
            $crawler->filter('h1')->text()
        );
    }

}
