<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Book;
use App\Author;

class BookManagementTest extends TestCase 
{
    use RefreshDatabase;

    /** @test */
    public function a_book_can_be_added_to_the_library(){
        // Realiza uma requisição POST para endpoint /books
        $response = $this->post('/books', $this->data());

        $book = Book::first();

        $this->assertCount(1, Book::all()); // Testa se livro foi adicionado

        $response->assertRedirect($book->path());
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
        $response = $this->post('/books', array_merge($this->data(), ['author_id' => '']));

        $response->assertSessionHasErrors('author_id'); 
    }
    
    /** @test */
    public function a_book_can_be_updated()
    {
        $this->post('/books', $this->data());

        $book = Book::first();

        $response = $this->patch($book->path(), array_merge($this->data(), [
            'title'     =>  'New Title',
            'author_id' =>  'New Author',
        ]));

        $this->assertEquals('New Title', Book::first()->title);
        $this->assertEquals('New Author', Author::find(Book::first()->author_id)->name);
        $response->assertRedirect($book->fresh()->path());
    }
    
    /** @test */
    public function a_book_can_be_deleted(){
        $this->post('/books', $this->data());

        $book = Book::first();

        $this->assertCount(1, Book::all());
        
        $response = $this->delete($book->path());

        $this->assertCount(0, Book::all());
        $response->assertRedirect('/books');
    }   

    /** @test */
    public function a_new_author_is_automatically_added() {

        $this->withoutExceptionHandling();

        $this->post('/books',[
            'title'     =>  'A Cool Book',
            'author_id'    =>  'Eric',
        ]);

        $book  = Book::first();
        $author = Author::first();

        $this->assertEquals($author->id, $book->author_id);
        $this->assertCount(1, Author::all());
    }    

    private function data(){
        return [
            'title'     =>  'Cool Book Title',
            'author_id'    =>  'Eric',
        ];
    }
    
}
