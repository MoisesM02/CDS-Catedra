<?php

namespace Tests\Feature;

use App\Models\Federation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteFederationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_delete_federation(): void
    {
        $user = User::factory()->create();

        //Create test federation
        $federation = Federation::factory()->create();

        //Check it was created
        $this->assertDatabaseHas('federations', ['id' => $federation->id]);

        //Send the request to delete the test federation
        $response = $this->actingAs($user)->delete("/federations/{$federation->id}");

        //Check there are no errors in the request
        $response->assertSessionHasNoErrors();

        //Verify the federations is no longer in the database
        $this->assertDatabaseMissing('federations', ['id' => $federation->id]);

    }
}
