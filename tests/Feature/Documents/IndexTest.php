<?php

namespace Tests\Feature\Documents;

use App\Models\DataCollection;
use App\Models\MailTemplate;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = 'documents';

    protected User $user;

    protected MailTemplate $mailTemplate;

    protected DataCollection $dataCollection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->mailTemplate = MailTemplate::factory()->create(['code' => 'transaction_email_receipt']);
        $this->dataCollection = DataCollection::factory()->create();
    }

    public function testIfUriSet(): void
    {
        $this->assertNotEmpty($this->uri);
    }

    public function testGuestCall(): void
    {
        $response = $this->get($this->uri);

        $response->assertRedirect('/login');
    }

    public function testUserCall(): void
    {
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri.'?'.http_build_query([
            'template_code' => $this->mailTemplate->code,
            'data_collection_id' => $this->dataCollection->id,
            'output_format' => 'pdf',
        ]));
        ray($response);

        $response->assertSuccessful();
    }

    public function testAdminCall(): void
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri.'?'.http_build_query([
            'template_code' => $this->mailTemplate->code,
            'data_collection_id' => $this->dataCollection->id,
            'output_format' => 'pdf',
        ]));

        $response->assertSuccessful();
    }
}
