<?php

namespace Tests\External\DpdIreland;

use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Consignment;
use App\Modules\DpdIreland\src\Exceptions\ConsignmentValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DpdTest extends TestCase
{
    use RefreshDatabase;
    use SeedDpdTestConfiguration;

    public function testIfEnvVariablesAreSet(): void
    {
        $this->assertNotEmpty(config('dpd.test_token'), 'TEST_DPD_TOKEN is not set');
        $this->assertNotEmpty(config('dpd.test_user'), 'TEST_DPD_USER is not set');
        $this->assertNotEmpty(config('dpd.test_password'), 'TEST_DPD_PASSWORD is not set');
    }

    public function testIfAuthenticates(): void
    {
        $auth = Client::getCachedAuthorization();
        $this->assertEquals('OK', $auth['authorization_response']['Status']);
    }

    /**
     * @throws ConsignmentValidationException
     */
    public function testIfRecordIdMatches(): void
    {
        $consignment = new Consignment([
            'DeliveryAddress' => [
                'Contact' => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail' => 'john.smith@ie.ie',
                'BusinessName' => 'JS Business',
                'AddressLine1' => 'DPD Ireland, Westmeath',
                'AddressLine2' => 'Unit 2B Midland Gateway Bus',
                'AddressLine3' => 'Kilbeggan',
                'AddressLine4' => 'Westmeath',
                'CountryCode' => 'IE',
            ],
            'CollectionAddress' => [
                'Contact' => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail' => 'john.smith@ie.ie',
                'BusinessName' => 'JS Business',
                'AddressLine1' => 'DPD Ireland, Westmeath',
                'AddressLine2' => 'Unit 2B Midland Gateway Bus',
                'AddressLine3' => 'Kilbeggan',
                'AddressLine4' => 'Westmeath',
                'CountryCode' => 'IE',
            ],
        ]);

        $preAdvice = Dpd::requestPreAdvice($consignment);

        $this->assertEquals($consignment->toArray()['RecordID'], $preAdvice->getAttribute('RecordID'));
    }

    public function testIfAuthorizationIsCached(): void
    {
        $auth1 = Client::getCachedAuthorization();
        $auth2 = Client::getCachedAuthorization();

        $this->assertTrue($auth2['from_cache']);
        $this->assertEquals($auth1['authorization_response']['AccessToken'], $auth2['authorization_response']['AccessToken']);
    }

    public function testSuccessfullyGeneratePreadvice(): void
    {
        $consignment = new Consignment([
            'RecordID' => 1,
            'TotalParcels' => 1,
            'ServiceOption' => 5,
            'ServiceType' => 1,
            'DeliveryAddress' => [
                'Contact' => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail' => 'john.smith@ie.ie',
                'BusinessName' => 'JS Business',
                'AddressLine1' => 'DPD Ireland, Westmeath',
                'AddressLine2' => 'Unit 2B Midland Gateway Bus',
                'AddressLine3' => 'Kilbeggan',
                'AddressLine4' => 'Westmeath',
                'CountryCode' => 'IE',
            ],
            'CollectionAddress' => [
                'Contact' => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail' => 'john.smith@ie.ie',
                'BusinessName' => 'JS Business',
                'AddressLine1' => 'DPD Ireland, Westmeath',
                'AddressLine2' => 'Unit 2B Midland Gateway Bus',
                'AddressLine3' => 'Kilbeggan',
                'AddressLine4' => 'Westmeath',
                'CountryCode' => 'IE',
            ],
        ]);

        $preAdvice = Dpd::requestPreAdvice($consignment);

        $this->assertTrue($preAdvice->isSuccess());
    }
}
