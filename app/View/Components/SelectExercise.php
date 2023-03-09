<?php
 
namespace App\View\Components;
 
use Illuminate\View\Component;
use App\Models\Exercise;
class SelectExercise extends Component
{
    
    public $exercises,$selected;
 
    /**
     * Create the component instance.
     *
     * @param  string  $type
     * @param  string  $message
     * @return void
     */
    public function __construct($exercises, $selected = [])
    {
        $this->exercises = [];
        $this->selected = $selected;
        if( $this->selected )
            $this->exercises = Exercise::whereIn("id",$selected)->get();
        // echo "dd";
        // exit;
    }
 
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        // return '';
        return view('components.select-exercise');
    }
}