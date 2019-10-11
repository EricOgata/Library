<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Author;

class AuthorsController extends Controller
{    

    public function store(){
        $author = Author::create(request()->only([
            'name', 'dob'
        ]));
    }
}
