<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Exercise;
class WorkoutDayExercise extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $details, $is_super_set_div , $exercise, $program;

    public function __construct( $details = null , $isSuperSetDiv = null , $program = null )
    {
        //
        //dump( $isSuperSetDiv );
        $this->details = $details;
        $this->is_super_set_div = $isSuperSetDiv;
        $this->program = $program;
        $this->exercise = Exercise::active()->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.workout-day-exercise');
    }
}
