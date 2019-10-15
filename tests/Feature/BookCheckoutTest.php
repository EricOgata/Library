<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Book;
use App\Reservation;
use Illuminate\Support\Facades\Auth;

class BookCheckoutTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_book_can_be_checked_out_by_a_signed_in_user()
    {
        $book  = factory(Book::class)->create();

        $this->actingAs($user = factory(User::class)->create())
            ->post('/checkout/' . $book->id);
        
        $this->assertCount(1, Reservation::all()); // Verifica se existe um novo registro dentro da tabela Reservations
        $this->assertEquals($user->id, Reservation::first()->user_id); // ID usuário deve bater
        $this->assertEquals($book->id, Reservation::first()->book_id); // Id livro deve bater
        $this->assertEquals(now(), Reservation::first()->checked_out_at); // Verifica data que foi retirado o livro
    }

    /** @test */
    public function only_a_signed_in_user_can_checkout_a_book()    
    {
        $book  = factory(Book::class)->create();

        $this->post('/checkout/' . $book->id)
            ->assertRedirect('/login');

        $this->assertCount(0, Reservation::all()); // Verifica se existe um novo registro dentro da tabela Reservations
    }
    
    /** @test */
    public function only_real_books_can_be_checked_out()
    {
        $this->actingAs($user = factory(User::class)->create())
            ->post('/checkout/123')
            ->assertStatus(404);
        
        $this->assertCount(0, Reservation::all());
    }

    /** @test */
    public function a_book_can_be_checked_in_by_a_signed_in_user()
    {
        $book  = factory(Book::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user)->post('/checkout/' . $book->id);

        $this->actingAs($user)->post('/checkin/' . $book->id);

        $this->assertCount(1, Reservation::all()); // Verifica se existe um novo registro dentro da tabela Reservations
        $this->assertCount(0, Reservation::activeReservationsByUser($user));

        $this->assertEquals($user->id, Reservation::first()->user_id); // ID usuário deve bater
        $this->assertEquals($book->id, Reservation::first()->book_id); // Id livro deve bater
        $this->assertEquals(now(), Reservation::first()->checked_out_at); // Verifica data que foi retirado o livro
        $this->assertEquals(now(), Reservation::first()->checked_in_at); // Verifica data que foi retirado o livro
    }

    /** @test */
    public function only_a_signed_in_user_can_checkin_a_book()
    {
        $book  = factory(Book::class)->create();

        $this->actingAs($user = factory(User::class)->create())
            ->post('/checkout/' . $book->id);
        
        Auth::logout();

        $this->post('/checkin/' . $book->id)
            ->assertRedirect('/login');

        $this->assertCount(1, Reservation::all()); // Verifica se existe um novo registro dentro da tabela Reservations
        $this->assertCount(1, Reservation::activeReservationsByUser($user)); // Verifica se existe um novo registro dentro da tabela Reservations
        $this->assertNull(Reservation::first()->checked_in_at);
    }
    
    /** @test */
    public function a_404_is_thrown_if_a_book_is_not_checked_out_first()
    {
        $book  = factory(Book::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post('/checkin/' . $book->id)
            ->assertStatus(404);
        
        $this->assertCount(0, Reservation::all()); // Verifica se existe um novo registro dentro da tabela Reservations
    }
    
}
