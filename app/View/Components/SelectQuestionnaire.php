<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\QuestionnaireType;
                                                     
class SelectQuestionnaire extends Component
{
    public $type , $input_name , $questionnaires , $value;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct( $type = null , $input_name = null , $value = null )
    {
        $this->type = empty($type) ? 'Questionnaire' : ($type) ;
        $this->input_name = $input_name;
        if( empty($this->input_name) )    
            $this->input_name = str_replace("_"," ",$type);
        $this->value = $value;
        $this->input_name = ucwords($this->input_name); 
        $this->questionnaires = QuestionnaireType::getQuestionOfType( $type );
    }
                                   
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()  
    {
        return view('components.select-questionnaire');
    }
}
