<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PlayerManagementTest extends TestCase
{
    use RefreshDatabase; // This resets the database after every single test

    protected function setUp(): void
    {
        parent::setUp();
        // Assuming your routes are protected by auth, we authenticate a test user
        $this->actingAs(User::factory()->create());
    }

    public function test_it_creates_a_player_without_a_team()
    {
        $payload = [
            'name' => 'John Doe',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'team_id' => null, // Free agent
        ];

        $response = $this->post(route('player.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('players', [
            'name' => 'John Doe',
        ]);
        // Ensure no pivot records were created
        $this->assertDatabaseCount('player_team', 0);
    }

    public function test_it_creates_a_player_with_an_initial_team()
    {
        $team = Team::factory()->create();
        $today = Carbon::now()->format('Y-m-d');

        $payload = [
            'name' => 'Jane Smith',
            'date_of_birth' => '1995-05-15',
            'gender' => 'female',
            'team_id' => $team->id,
        ];

        $this->post(route('player.store'), $payload);

        $player = Player::where('name', 'Jane Smith')->first();

        // Check pivot table
        $this->assertDatabaseHas('player_team', [
            'player_id' => $player->id,
            'team_id' => $team->id,
            'start_date' => $today,
            'end_date' => null,
        ]);
    }

    public function test_it_handles_player_transfers_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();
        $player = Player::factory()->create();

        // 1. Give player an active contract with Team A (from a month ago)
        $pastDate = Carbon::now()->subMonth()->format('Y-m-d');
        $player->teams()->attach($teamA->id, [
            'start_date' => $pastDate,
            'end_date' => null
        ]);
        $player->refresh();
        // 2. Submit update form to transfer them to Team B
        $today = Carbon::now()->format('Y-m-d');
        $payload = [
            'name' => $player->name,
            'date_of_birth' => $player->date_of_birth,
            'gender' => $player->gender,
            'team_id' => $teamB->id,
        ];

        $response = $this->patch(route('player.update', $player), $payload);
        $response->assertSessionHasNoErrors();
        // 3. Assert Team A's contract was CLOSED today
        $this->assertDatabaseHas('player_team', [
            'player_id' => $player->id,
            'team_id' => $teamA->id,
            'start_date' => $pastDate,
            'end_date' => $today,
        ]);

        // 4. Assert Team B's contract was OPENED today
        $this->assertDatabaseHas('player_team', [
            'player_id' => $player->id,
            'team_id' => $teamB->id,
            'start_date' => $today,
            'end_date' => null,
        ]);
    }

    public function test_it_deletes_a_player()
    {
        $player = Player::factory()->create();
        $team = Team::factory()->create();

        $player->teams()->attach($team->id, ['start_date' => now()]);

        $this->delete(route('player.destroy', $player));

        // Ensure player is gone
        $this->assertDatabaseMissing('players', ['id' => $player->id]);

        // Ensure pivot records are gone (requires detach() in controller or cascade in DB)
        $this->assertDatabaseMissing('player_team', ['player_id' => $player->id]);
    }
}
