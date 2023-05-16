<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use Illuminate\Http\Request;

class BucketController extends Controller {
    /**
     * Display a listing of the bucket.
     */
    public function index() {
        $buckets =  ->paginate(5);
        return view('bucket.index',compact('buckets'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new bucket.
     */
    public function create()  {
        return view('bucket.create');
    }

    /**
     * Store a newly created bucket in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'volume' => 'required',
        ]);    
        Bucket::create($request->all());     
        return redirect()->route('bucket.index')->with('success','Bucket created successfully.');
    }

    /**
     * Display the bucket.
     */
    public function show(Bucket $bucket) {
        return view('bucket.show',compact('bucket'));
    }

    /**
     * Show the form for editing the bucket.
     */
    public function edit(Bucket $bucket){
        return view('bucket.edit',compact('bucket'));
    }

    /**
     * Update the bucket in storage.
     */
    public function update(Request $request, Bucket $bucket){
        $request->validate([
            'name' => 'required',
            'volume' => 'required',
        ]);    
        $bucket->update($request->all());    
        return redirect()->route('bucket.index')->with('success','Bucket updated successfully');
    }

    /**
     * Remove the delete from storage.
     */
    public function destroy(Bucket $bucket){
        $bucket->delete();
        return redirect()->route('bucket.index')->with('success','Buckert deleted successfully');
    }
}
