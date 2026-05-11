<?php

namespace Tests\Feature;

use App\Jobs\SendEmail;
use App\Repositories\Test\TestUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Queue as FacadesQueue;
use Mailtrap\Api\General\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;
use TestUtils;

class MailQueueTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_welcom_email_sent_after_registering()
    {
        FacadesQueue::fake();

        $repo = app(TestUserRepository::class);

        $user = $repo->create([
            "name"=>"marwan",
            "email"=>"marwan@test.com",
            "password"=>"123123123"
        ]);

        SendEmail::dispatch($user);

        FacadesQueue::assertPushed(SendEmail::class);


    }
}
