<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->call(CampaignTypesSeeder::class);
    }
}

class AdminSeeder extends Seeder
{
    /**
     * Import all admins.
     *
     * @return void
     */
    public function run()
    {
        // Create a list of all admins to be seeded.
        $admins = [
            [
                "name" => "Thomas Cutting",
                "email" => "thomas@reservetechinc.com",
                "password" => "00t21212!"
            ],
            [
                "name" => "Wes Eads",
                "email" => "wes@reservetechinc.com",
                "password" => "0w21212!"
            ],
            [
                "name" => "Generic Developer Account",
                "email" => "developer@" . env('APP_DOMAIN'),
                "password" => "0password1997!"
            ]
        ];

        foreach ($admins as $admin) {
            // Create new user.
            $newUser = \App\User::create([
                "name" => $admin["name"],
                "email" => $admin["email"],
                "password" => bcrypt($admin["password"]),
            ]);
            // Fire off new event.
            event(new \App\Events\UserRegistered($newUser, "admin"));
        }
    }
}

class CampaignTypesSeeder extends Seeder
{
    /**
     * Import all campaign types.
     * @return void
     */
    public function run()
    {
        $campaignTypes = [
            [
                "name" => "CPL",
                "description" => "Cost-Per-Lead (Linkouts)"
            ],
            [
                "name" => "CPA",
                "description" => "Cost-Per-Acquisition"
            ],
            [
                "name" => "Leadgen",
                "description" => "Co-Registration"
            ],
            [
                "name" => "Linkout",
                "description" => "For text/email links.."
            ]
        ];

        foreach ($campaignTypes as $campaignType) {
            // Create new CampaignType.
            \App\CampaignType::create([
                "name" => $campaignType["name"],
                "description" => $campaignType["description"]
            ]);
        }
    }
}