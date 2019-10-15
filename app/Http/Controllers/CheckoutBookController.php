<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use Illuminate\Support\Facades\Auth;

class CheckoutBookController extends Controller
{

    // Aqui informa-se ao sistema que o controlador terá acesso apenas via autenticação
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Book $book)
    {
        $book->checkout(Auth::user()); // Registra um novo checkout.
    }
}
