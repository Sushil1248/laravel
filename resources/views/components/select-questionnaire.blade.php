<p>{{ $input_name }}</p>
<div class="input-group input-group-sm invoice-value">
    <select class="custom-select" name="{{ $type }}">
        <option value="">Select {{ $input_name }}</option>
        @foreach( $questionnaires as $singleQuestion )
        <option {{ $value == $singleQuestion->id ? 'selected' : '' }} value="{{ jsencode_userdata($singleQuestion->id) }}">{{ $singleQuestion->question }}</option>
        @endforeach
    </select>
</div>