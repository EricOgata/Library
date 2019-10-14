<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Book;

class BookTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_author_id_is_recorded()
    {
        Book::create([
            'title'     =>  'Cool Title',
            'author_id' =>  1,
        ]);

        // Conta quantos registros existem no banco de dados; Deve haver 1 registro.
        $this->assertCount(1, Book::all());
    }    
}
