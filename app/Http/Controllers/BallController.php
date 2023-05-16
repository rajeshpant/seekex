<?php

namespace App\Http\Controllers;

use App\Models\Ball;
use Illuminate\Http\Request;

class BallController extends Controller {
    /**
     * Display a listing of the ball.
     */
    public function index() {
        $balls = Ball::latest()->paginate(5);
        return view('ball.index',compact('balls'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new ball.
     */
    public function create()  {
        return view('ball.create');
    }

    /**
     * Store a newly created ball in storage.
     */
    public function store(Request $request) {
        $request->validate(['name' => 'required', 'volume' => 'required']);    
        Ball::create($request->all());     
        return redirect()->route('ball.index')->with('success','Ball created successfully.');
    }

    /**
     * Display the ball.
     */
    public function show(Ball $ball) {
        return view('ball.show',compact('ball'));
    }

    /**
     * Show the form for editing the ball.
     */
    public function edit(Ball $ball){
        return view('ball.edit',compact('ball'));
    }

    /**
     * Update the ball in storage.
     */
    public function update(Request $request, Ball $ball){
        $request->validate(['name' => 'required', 'volume' => 'required']);    
        $ball->update($request->all());    
        return redirect()->route('ball.index')->with('success','Ball updated successfully');
    }

    /**
     * Remove the delete from storage.
     */
    public function destroy(Ball $ball){
        $ball->delete();
        return redirect()->route('ball.index')->with('success','Ball deleted successfully');
    }
}
