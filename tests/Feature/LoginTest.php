<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class LoginTest extends TestCase
{
   /** @testing if the a user is being validated with the right credentials */
   public function a_user_can_login_with_valid_credentials()
   {
       // Cria um usuário
       $user = User::factory()->create([
           'password' => Hash::make('password123'), // define a senha
       ]);

       // Realiza o login com as credenciais corretas
       $response = $this->post('/login', [
           'email' => $user->email,
           'password' => 'password123',
       ]);

       // Verifica se o redirecionamento está correto
       $response->assertRedirect('/home');

       // Verifica se o usuário está autenticado
       $this->assertAuthenticatedAs($user);
   }

   /** @testing when a user is being validated with the wrong credentials */
   public function a_user_cannot_login_with_invalid_credentials()
   {
       // Tenta realizar o login com credenciais inválidas
       $response = $this->post('/login', [
           'email' => 'invalid@example.com',
           'password' => 'wrongpassword',
       ]);

       // Verifica se a resposta não redireciona para /home
       $response->assertSessionHasErrors();
       $this->assertGuest();
   }
}
