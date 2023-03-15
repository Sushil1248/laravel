@if( $exercises->count() )
@foreach( $exercises->distinct()->get() as $singleExercise )
<a target="_blank" href="{{ route('exercise.list',['exercise_id'=> jsencode_userdata($singleExercise->id)]) }}">
{{ $singleExercise->name }} <i class="fa fa-external-link"></i>
</a>{{ !$loop->last ? ", " : "" }}
@endforeach
@else
NA
@endif