@if( !request()->ajax() )
<div class="media-container">
	<h5>{{ $heading }} @if($required)<span class="required-field">*</span>@endif 
		<img class="select-media-image" src="{{ $selectedMedia ? $selectedMedia->image_url : '' }}">
	 </h5>
	@if( !$mediaFiles->count() && $required )
	<p>No media images added yet. Please <a href="{{ route('media.list') }}">click here</a> to upload images.</p>
	@endif
	
	<div class="boxes-listing justify-content-start flex-wrap filter-listing all-media-boxes" data-selected-id="{{ jsencode_userdata($selectId) }}">
@endif
		@foreach( $mediaFiles as $singleFile )
		<input type="radio" {{ $selectId == $singleFile->id ? "checked" : "" }} name="{{ $inputName }}" value="{{ jsencode_userdata($singleFile->id) }}" id="{{ $inputName }}-{{ jsencode_userdata($singleFile->id) }}">
		<label class="box-item" for="{{ $inputName }}-{{ jsencode_userdata($singleFile->id) }}">
			<div class="box-img">
				<img src="{{ $singleFile->image_url }}" alt="icon" style="max-width:100%">
			</div>
			<h5>{{ $singleFile->name }}</h5>
		</label>
		@endforeach
		@if( $mediaFiles->count() )
		<input type="hidden" name="mpage" value="{{ $mediaFiles->currentPage() + 1 }}">
		@endif
@if( !request()->ajax() )
	</div>
</div>
@endif