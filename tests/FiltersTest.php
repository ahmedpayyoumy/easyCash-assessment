<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class FiltersTest extends TestCase
{
    public function testRequestWithoutHeader()
    {
        $this->json('get', '/api/v1/transactaions', ['provider' => 'DataProviderW'])->seeStatusCode(401);
    }

    public function testRequestWithHeader()
    {
        $this->json('get', '/api/v1/transactaions', ['provider' => 'DataProviderW'], ['Authorization' => 'EasyCashCustomAuth'])->seeStatusCode(200);
    }

    public function testProviderFilter()
    {
        $this->get('api/v1/transactaions?provider=DataProviderW', ['Authorization' => 'EasyCashCustomAuth'])->assertResponseStatus(200);
    }

    public function testProviderAndAmountFilter()
    {
        $this->get('api/v1/transactaions?provider=DataProviderW&amounteMin=100&amounteMax=500', ['Authorization' => 'EasyCashCustomAuth'])->assertResponseStatus(200);
    }

    public function testAmountFilter()
    {
        $this->get('api/v1/transactaions?amounteMin=100&amounteMax=500', ['Authorization' => 'EasyCashCustomAuth'])->assertResponseStatus(200);
    }

    public function testAmountAndCurrencyFilter()
    {
        $this->get('api/v1/transactaions?amounteMin=100&amounteMax=500&currency=EGP', ['Authorization' => 'EasyCashCustomAuth'])->assertResponseStatus(200);
    }

    public function testAmountAndStatusFilter()
    {
        $this->get('api/v1/transactaions?amounteMin=100&amounteMax=500&statusCode=paid', ['Authorization' => 'EasyCashCustomAuth'])->assertResponseStatus(200);
    }

    public function testAllFilters()
    {
        $this->get('api/v1/transactaions?provider=DataProviderW&amounteMin=100&amounteMax=500&statusCode=paid&currency=EGP', ['Authorization' => 'EasyCashCustomAuth'])->assertResponseStatus(200);
    }

}
