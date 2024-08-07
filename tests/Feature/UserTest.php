<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use http\Client\Curl\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'hasan',
            'password' => 'rahasia',
            'name' => 'Hasan Almunawar'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'username' => 'hasan',
                    'name' => 'Hasan Almunawar'
                ]
            ]);
    }

    public function testLogin()
    {
        // Seed the database with a user
        $this->seed(UserSeeder::class);

        // Attempt to log in
        $response = $this->post('/api/login', [
            'username' => 'test',  // Ensure this username matches what you seeded
            'password' => 'test',  // Ensure this password matches what you seeded
        ]);

        // Assert successful response and valid JSON structure
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'Hasan Almunawar',  // Ensure this name matches your seeder
                ]
            ]);

        // Check if the user has a token
        $user = User::where('username', 'test')->first();
        $this->assertNotNull($user->token);  // Ensure 'token' is a column in the database
    }


    public function testRegisterUserAlreadyExist()
    {

    }

}
