<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;

class BooksController extends Controller
{
    // Public Methods
    public function store(){
        $book = Book::create($this->validateRequest());
        return redirect($book->path());
    }

    public function update(Book $book) {
        $book->update($this->validateRequest());
        return redirect($book->path());
    }

    public function destroy(Book $book){
        $book->delete();
        return redirect('/books');
    }

    // Utilities
    protected function validateRequest(){
        // Creates a sanitized data
        return request()->validate([
            'title'     =>  'required',
            'author_id'    =>  'required'
        ]);
    }
}
