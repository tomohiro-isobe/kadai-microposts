@if (Auth::user()->id != $micropost->id)
    @if (Auth::user()->is_favoriteing($micropost->id))
        {!! Form::open(['route' => ['user.unfavorite', $micropost->id], 'method' => 'delete']) !!}
            {!! Form::submit('unfavorite', ['class' => 'btn btn-success btn-xs']) !!}
        {!! Form::close() !!}
    @else 
        {!! Form::open(['route' => ['user.favorite', $micropost->id]]) !!}
            {!! Form::submit('favorite', ['class' => 'btn btn-primary btn-xs']) !!}
        {!! Form::close() !!}
    @endif
@endif