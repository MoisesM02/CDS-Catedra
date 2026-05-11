<?php

namespace Database\Seeders;

use App\Models\Federation;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $federation = Federation::factory()->create(['name' => 'Global Football League']);
        $teams = Team::factory(5)->create(['federation_id' => $federation->id]);

        // 3. Create 50 Players
        $players = Player::factory(50)->create();

        // 4. Attach Players to Teams with Pivot Data (History)
        foreach ($players as $player) {

            // Give EVERY player a CURRENT team (end_date is null)
            $currentTeam = $teams->random();
            $player->teams()->attach($currentTeam->id, [
                'start_date' => Carbon::now()->subMonths(rand(1, 24))->format('Y-m-d'),
                'end_date' => null, // Active
            ]);

            // Give SOME players (about 40%) a PAST team to simulate a transfer
            if (rand(1, 100) <= 40) {
                // Get a different team than their current one
                $pastTeam = $teams->where('id', '!=', $currentTeam->id)->random();

                $player->teams()->attach($pastTeam->id, [
                    // They started 3-5 years ago
                    'start_date' => Carbon::now()->subYears(rand(3, 5))->format('Y-m-d'),
                    // They left 25-36 months ago
                    'end_date' => Carbon::now()->subMonths(rand(25, 36))->format('Y-m-d'),
                ]);
            }
        }
    }
}
