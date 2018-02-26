<?php

namespace App\Http\Controllers;


use App\Room;
use App\Student;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{

    public function home()
    {
        $roomsCount = Room::count();
        $studentsCount = Student::count();
        return view('welcome', compact('roomsCount', 'studentsCount'));
    }
}
