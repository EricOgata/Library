<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Book;

class BookReservationTest extends TestCase 
{
    use RefreshDatabase;

    /** @test */
    public function a_book_can_be_added_to_the_library(){
        $this->withoutExceptionHandling();
        // Realiza uma requisição POST para endpoint /books
        $response = $this->post('/books',[
            'title'     =>  'Cool Book Title',
            'author'    =>  'Eric',
        ]);

        $response->assertOk();              // Testa se resposta foi 200
        $this->assertCount(1, Book::all()); // Testa se livro foi adicionado
    }

    /** @test */
    public function a_title_is_required()
    {
        $response = $this->post('/books',[
            'title'     =>  '',
            'author'    =>  'Eric',
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_author_is_required()
    {
        $response = $this->post('/books',[
            'title'     =>  'A Cool Book',
            'author'    =>  '',
        ]);

        $response->assertSessionHasErrors('author'); 
    }
    
    /** @test */
    public function a_book_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $this->post('/books',[
            'title'     =>  'A Cool Book',
            'author'    =>  'Eric',
        ]);

        $book = Book::first();

        $response = $this->patch('/books/' . $book->id, [
            'title'     =>  'New Title',
            'author'    =>  'New Author',
        ]);

        $this->assertEquals('New Title', Book::first()->title);
        $this->assertEquals('New Author', Book::first()->author);
    }
    
    
}
