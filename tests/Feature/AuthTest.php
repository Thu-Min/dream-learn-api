<?php

use App\Models\User;
use Laravel\Passport\ClientRepository;

// it('can create account', function() {
//     $clientRepository = app(ClientRepository::class);
//     $client = $clientRepository->createPersonalAccessClient(
//         null, 'Test Personal Access Client', '/'
//     );

//     $data = [
//         'name' => 'thumin_testing',
//         'user_name' => 'tmtmtmtm',
//         'email' => 'minnt5637@gmail.com',
//         'password' => '123456789',
//         'confirmed_password' => '123456789'
//     ];

//     $response = $this->json('POST', '/api/v1/sign-up', $data);

//     $response->assertStatus(200);
// });

it('can sign in', function() {
    $clientRepository = app(ClientRepository::class);
    $client = $clientRepository->createPersonalAccessClient(
        null, 'Test Personal Access Client', '/'
    );

    $password = 'password';
    $user = User::factory()->create([
        'password' => Hash::make($password),
    ]);

    $data = [
        'email' => $user->email,
        'password' => $password
    ];

    $response = $this->json('POST', '/api/v1/sign-in', $data);

    $response->assertStatus(200);
});
