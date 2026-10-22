<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $mainUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Utilisateur Test',
                'email_verified_at' => now(),
                'password' => 'password',
            ]
        );

        // Emails fixes : relancer le seeder réutilise les mêmes comptes au lieu d'en empiler de nouveaux.
        $users = collect(range(1, 10))
            ->map(fn (int $number) => User::firstOrCreate(
                ['email' => "user{$number}@example.com"],
                [
                    'name' => fake()->name(),
                    'email_verified_at' => now(),
                    'password' => 'password',
                ]
            ))
            ->prepend($mainUser);

        $organizations = collect([
            'Association Sportive Paris',
            'Collectif Foot & Co',
            'Club Multisports ESIEE',
        ])->map(function (string $name, int $index) use ($users) {
            $org = Organization::firstOrCreate(
                ['slug' => Str::slug($name).'-'.($index + 1)],
                ['name' => $name]
            );

            $owner = $users[$index];
            $org->members()->syncWithoutDetaching([$owner->id => ['is_owner' => true]]);

            foreach ($users->slice($index + 1, 3) as $member) {
                $org->members()->syncWithoutDetaching([$member->id => ['is_owner' => false]]);
            }

            return $org;
        });

        $sports = collect([
            'Football',
            'Basketball',
            'Tennis',
            'Badminton',
            'Running',
            'Padel',
            'Volleyball',
            'Escalade',
        ])->map(fn (string $name) => Sport::firstOrCreate(
            ['slug' => Str::slug($name)],
            [
                'name' => $name,
                'active' => true,
            ]
        ));

        $ownerIdsByOrganization = $organizations
            ->mapWithKeys(fn (Organization $org, int $index) => [$org->id => $users[$index]->id]);

        $isFirstSeed = ! Event::exists();

        $eventsData = [
            ['title' => 'Five du jeudi soir', 'sport' => 'Football', 'city' => 'Paris', 'venue' => 'UrbanSoccer Porte d\'Ivry', 'days' => 2, 'hour' => 19, 'capacity' => 10, 'duration' => 60],
            ['title' => 'Tournoi Basket 3x3', 'sport' => 'Basketball', 'city' => 'Lyon', 'venue' => 'Gymnase Jean Macé', 'days' => 3, 'hour' => 18, 'capacity' => 12, 'duration' => 90],
            ['title' => 'Session Badminton double', 'sport' => 'Badminton', 'city' => 'Nantes', 'venue' => 'Halle Sportive Beaulieu', 'days' => 4, 'hour' => 20, 'capacity' => 8, 'duration' => 60],
            ['title' => 'Sortie Running 10km', 'sport' => 'Running', 'city' => 'Paris', 'venue' => 'Parc des Buttes-Chaumont', 'days' => 5, 'hour' => 10, 'capacity' => 20, 'duration' => 60],
            ['title' => 'Match de Padel intermédiaire', 'sport' => 'Padel', 'city' => 'Bordeaux', 'venue' => '4Padel Bordeaux', 'days' => 6, 'hour' => 19, 'capacity' => 4, 'duration' => 90],
            ['title' => 'Volley 6x6 convivial', 'sport' => 'Volleyball', 'city' => 'Lille', 'venue' => 'Complexe Sportif Vauban', 'days' => 7, 'hour' => 18, 'capacity' => 12, 'duration' => 120],
            ['title' => 'Session Bloc du week-end', 'sport' => 'Escalade', 'city' => 'Paris', 'venue' => 'Arkose Nation', 'days' => 8, 'hour' => 14, 'capacity' => 15, 'duration' => 120],
            ['title' => 'Entraînement Tennis ouvert', 'sport' => 'Tennis', 'city' => 'Marseille', 'venue' => 'Tennis Club Phocéen', 'days' => 9, 'hour' => 17, 'capacity' => 6, 'duration' => 90],
            ['title' => 'Foot à 7 dimanche matin', 'sport' => 'Football', 'city' => 'Toulouse', 'venue' => 'Stade des Fontaines', 'days' => 10, 'hour' => 10, 'capacity' => 14, 'duration' => 90],
            ['title' => 'Pick-up game Basketball', 'sport' => 'Basketball', 'city' => 'Paris', 'venue' => 'Terrain Duperré', 'days' => 12, 'hour' => 18, 'capacity' => 10, 'duration' => 90],
        ];

        foreach ($eventsData as $index => $data) {
            $sport = $sports->firstWhere('name', $data['sport']) ?? $sports->random();
            $org = $organizations[$index % $organizations->count()];

            $ownerId = $ownerIdsByOrganization[$org->id];

            $event = Event::firstOrCreate(['title' => $data['title'], 'organization_id' => $org->id], Event::factory()->raw([
                'title' => $data['title'],
                'organization_id' => $org->id,
                'sport_id' => $sport->id,
                'created_by' => $ownerId,
                'city' => $data['city'],
                'venue' => $data['venue'],
                'starts_at' => now()->addDays($data['days'])->setTime($data['hour'], 0),
                'duration_minutes' => $data['duration'],
                'capacity' => $data['capacity'],
                'status' => Event::STATUS_PUBLISHED,
            ]));

            if (! $event->wasRecentlyCreated) {
                continue;
            }

            $participants = $users->reject(fn (User $user) => $user->id === $ownerId);

            foreach ($participants->random(min(3, $data['capacity'] - 1)) as $participant) {
                DB::table('eventParticipants')->insert([
                    'event_id' => $event->id,
                    'user_id' => $participant->id,
                    'status' => Event::PARTICIPANT_CONFIRMED,
                    'registered_at' => now(),
                ]);
            }
        }

        if (! $isFirstSeed) {
            return;
        }

        Event::factory()
            ->count(8)
            ->recycle($sports)
            ->state(function () use ($organizations, $ownerIdsByOrganization) {
                $org = $organizations->random();

                return ['organization_id' => $org->id, 'created_by' => $ownerIdsByOrganization[$org->id]];
            })
            ->create();
    }
}
