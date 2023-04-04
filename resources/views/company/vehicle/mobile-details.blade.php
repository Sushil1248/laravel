@if(count($userDetail->company_detail->current_photos)>0)
{{-- <div class="question-list">
    <h4>Current Images</h4>  
    <div class="mobile-details-inputs cstm-current-img">
        <ul>
            @foreach( $userDetail->company_detail->current_photos as $key => $value )
            <li>
                <p >{{ ucwords(str_replace("_"," ",$key)) }} Image</p>
                <div class="input-group input-group-sm invoice-value">
                    @if( $value )
                    <img src="{{ $value }}">
                    @else
                    <p>NA</p>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    
</div> --}}
@endif