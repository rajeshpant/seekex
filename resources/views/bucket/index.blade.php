@extends('layouts.layout')
 
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Bucket</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('bucket.create') }}"> Create New Bucket</a>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Volume</th>
        </tr>
        @foreach ($buckets as $bucket)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $bucket->name }}</td>
            <td>{{ $bucket->volume }}</td>
            <td>
                <form action="{{ route('bucket.destroy',$bucket->id) }}" method="POST">
   
                    <a class="btn btn-info" href="{{ route('bucket.show',$bucket->id) }}">Show</a>
    
                    <a class="btn btn-primary" href="{{ route('bucket.edit',$bucket->id) }}">Edit</a>
   
                    @csrf
                    @method('DELETE')
      
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
  
    {!! $buckets->links() !!}
      
@endsection