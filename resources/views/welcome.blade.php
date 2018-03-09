@extends('layouts.app')
        
@section('content')
    <div class="center jumbotron">
         <div class="text-center">
             <h1>Welcom to the Microposts</h1>
             {!! link_to_route('signup.get', 'sign up now!', null, ['class' => 'btn btn-lg btn-primary']) !!}
        </div>
    </div>
@endsection
        
 
