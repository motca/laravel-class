<?php

namespace App\Http\Controllers;


use App\Province;
use App\Room;
use App\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::all();

        return view('students.students-list', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::all();
        $rooms = Room::all();

        return view('students.register-student', compact('provinces', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Student::create($request->all());

        return redirect('/students');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $provinces = Province::all();
        $student = Student::find($id);
        $rooms = Room::all();

        return view('students.edit-student', compact('student', 'provinces', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);

        $student->name = $request->name;
        $student->last_name = $request->last_name;
        $student->father_name = $request->father_name;
        $student->room_id = $request->room_id;
        $student->province_id = $request->province_id;
        $student->phone_number = $request->phone_number;
        $student->nic = $request->nic;
        $student->save();

        return redirect('/students');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Student::find($id);

        $student->delete();

        return redirect()->back();
    }

    public function query(Request $request)
    {
        $param = $request->param;


        $students = Student::where('name', $param)
            ->get();

        return view('students.students-list', compact('students'));
    }
}
