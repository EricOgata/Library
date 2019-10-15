<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use Illuminate\Support\Facades\Auth;

class CheckinBookController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function store(Book $book)
    {
        try {
            $book->checkin(Auth::user());
        } catch (\Exception $e) {
            return response([], 404);
        }
    }
}
