<?php

namespace Tests\Feature;

use App\Models\Federation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateFederationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_federation(): void
    {
        //Create user to modify the federation
        $user = User::factory()->create();

        //Create federation that will be updated
        $federation = Federation::factory()->create();
        $oldName = $federation->name;
        //Modify federation name
        $federation->name = 'Test completed successfully';
        //Send patch request to update the federation
        $response = $this->actingAs($user)->patch("/federations/{$federation->id}", $federation->except('id'));

        //Verify validation rules
        $response->assertSessionHasNoErrors();
        //Verify new name is in the record
        $this->assertDatabaseHas('federations', ['name' => 'Test completed successfully', 'id' => $federation->id]);

        //Verify original record is no longer in the database
        $this->assertDatabaseMissing('federations', ['name' => $oldName, 'id' => $federation->id]);
    }
}
