<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PhpParser\Node\Expr\BinaryOp\Equal;
use App\Book;
use App\User;
use App\Reservation;

class BookReservationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_book_can_be_checked_out() {
        
        // $this->withoutExceptionHandling();
        $book  = factory(Book::class)->create();
        $user   = factory(User::class)->create();

        $book->checkout($user); // Registra um novo checkout.

        $this->assertCount(1, Reservation::all()); // Verifica se existe um novo registro dentro da tabela Reservations
        $this->assertEquals($user->id, Reservation::first()->user_id); // ID usuário deve bater
        $this->assertEquals($book->id, Reservation::first()->book_id); // Id livro deve bater
        $this->assertEquals(now(), Reservation::first()->checked_out_at); // Verifica data que foi retirado o livro
    }

    /** @test */
    public function a_book_can_be_returned() {
        $this->withoutExceptionHandling();
        
        $book  = factory(Book::class)->create();
        $user  = factory(User::class)->create();

        $book->checkout($user); // Registra um novo checkout.
        $book->checkin($user); // Registra um novo checkin
        $this->assertCount(1, Reservation::all());
        $this->assertEquals($user->id, Reservation::first()->user_id); // ID usuário deve bater
        $this->assertEquals($book->id, Reservation::first()->book_id); // Id livro deve bater
        $this->assertNotNull(Reservation::first()->checked_in_at);
        $this->assertEquals(now(), Reservation::first()->checked_in_at);
    }
    

    /** @test 
     * Simula o sistema tentar dar check_in em um livro que nunca foi checked_out
    */
    public function if_not_checked_out_exception_is_thrown()
    {
        $this->expectException(\Exception::class);

        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();

        $book->checkin($user);
    }
    

    // A user can check out a book twice.
    /** @test */
    public function a_user_can_check_out_a_book_twice()
    {
        $this->withoutExceptionHandling();
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();

        // usuário pega livro emprestado e devolve
        $book->checkout($user); // Registra um novo checkout.
        $book->checkin($user); // Registra um novo checkin

        // usuário pega livro emprestado novamente
        $book->checkout($user); // Registra um novo checkout.

        $this->assertCount(2, Reservation::all());
        $this->assertEquals($user->id, Reservation::find(2)->user_id); // ID usuário deve bater
        $this->assertEquals($book->id, Reservation::find(2)->book_id); // Id livro deve bater
        $this->assertNull(Reservation::find(2)->checked_in_at); // Garante que livro pode ser emprestado
        $this->assertEquals(now(), Reservation::find(2)->checked_out_at);

        $book->checkin($user); // Registra um novo checkin

        $this->assertCount(2, Reservation::all());
        $this->assertEquals($user->id, Reservation::find(2)->user_id); // ID usuário deve bater
        $this->assertEquals($book->id, Reservation::find(2)->book_id); // Id livro deve bater
        $this->assertNotNull(Reservation::find(2)->checked_in_at); // Garante que livro pode ser emprestado
        $this->assertEquals(now(), Reservation::find(2)->checked_in_at);
    }
    
    // A user can check out more than one book;

    /** @test */
    public function a_user_check_out_more_than_one_book_at_time()
    {
        $book1 = factory(Book::class)->create();
        $book2 = factory(Book::class)->create();
        $user = factory(User::class)->create();

        $book1->checkout($user);
        $book2->checkout($user);

        $this->assertCount(2, Reservation::all());

        $this->assertEquals($book1->id, Reservation::find(1)->book_id);
        $this->assertEquals($user->id, Reservation::find(1)->user_id);
        $this->assertEquals($book2->id, Reservation::find(2)->book_id);
        $this->assertEquals($user->id, Reservation::find(2)->user_id);
    }
    
    // A user can checkout only 3 books;
    /** @test */
    public function a_user_can_checkout_only_3_books_at_time()
    {
        $book1 = factory(Book::class)->create();
        $book2 = factory(Book::class)->create();
        $book3 = factory(Book::class)->create();
        $book4 = factory(Book::class)->create();
        $book5 = factory(Book::class)->create();
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $book1->checkout($user);
        $book2->checkout($user);
        $book3->checkout($user);
        $book4->checkout($user2);        
        $this->assertCount(3, Reservation::activeReservationsByUser($user));
        $this->assertCount(4, Reservation::all());

        $book5->checkout($user);
        $this->assertCount(3, Reservation::activeReservationsByUser($user));

        $book1->checkin($user);
        $this->assertCount(2, Reservation::activeReservationsByUser($user));        

        $book5->checkout($user);
        $this->assertCount(3, Reservation::activeReservationsByUser($user));        
    }
}
