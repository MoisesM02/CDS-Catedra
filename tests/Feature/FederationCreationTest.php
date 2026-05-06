<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Federation;
use Tests\TestCase;

class FederationCreationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_create_federation(): void
    {
        //Create a user to pass the middleware
        $user = User::factory()->create();

        //Use the user generated before to send the post request
        $response = $this->actingAs($user)->post('/federations', [
            'name' => 'Federation 123',
            'date_of_foundation' => '2020-12-31',
            'address' => 'Av. Evergreen 103',
            'logo' => 'https://www.placehold.co/90'
        ]);
        //Check that validation passes
        $response->assertSessionHasNoErrors();
        //Check the database has a record with the name provided in the test
        $this->assertDatabaseHas('federations', ['name' => 'Federation 123']);
        $this->assertAuthenticated();
    }
}
