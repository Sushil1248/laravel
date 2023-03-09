<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Media;
use Illuminate\Support\Facades\Config;
class MediaFiles extends Component
{
    public $mediaFiles,
    $selectId,
    $required,
    $selectedMedia,
    $heading,
    $inputName;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct( $selectId = null , $required = null , $heading = "Choose Image" , $inputName = "media_id" )
    {
        $this->selectId = $selectId;
        $this->required = $required;
        $this->mediaFiles = Media::active()->latest()->paginate( 16 ,'*','mpage' );
        $this->selectedMedia = null;
        $this->heading = $heading;
        $this->inputName = $inputName;
        if( $this->selectId ){
            $this->selectedMedia = Media::find( $this->selectId );
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.media-files');
    }
}
