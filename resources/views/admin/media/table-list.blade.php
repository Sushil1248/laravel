<div class=" table-responsive list-items">
    <table class="table">
        <thead>
            <tr>
                <th scope="col" class="purchase-order-date">
                    @sortablelink('name', 'Name')
                </th>
                <th scope="col" class="purchase-order-date">
                    Type
                </th>
                <th scope="col">
                    Image
                </th>
                <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">
                    @sortablelink('status', 'Status')
                </th>
                <th scope="col" class="purchase-order-date text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $singleFile)
            <tr>
                <td class="purchase-order-date">
                    {{ $singleFile->name }}
                </td>
                <td class="purchase-order-date">
                    {{ $singleFile->type }}
                </td>
                <td>
                    <img src="{{ Storage::url($singleFile->path) }}" style="max-width:60px">
                </td>

                <td>
                    {{ changeDateFormat($singleFile->created_at) }}
                </td>
                <td class="text-center status-text">
                    <input data-id="{{ jsencode_userdata($singleFile->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-offstyle="danger" data-height="20" data-width="70" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $singleFile->status ? 'checked' : '' }}>
                </td>
                <td class="text-center purchase-order-date">
                    @if( empty($deleteRecords) )
                    <a title="Edit" href="{{ route('media.edit',['id'=>jsencode_userdata($singleFile->id)]) }}" ><i class="fas fa-pencil-alt" style="color:#33383a"></i></a>&nbsp;&nbsp;
                    
                    <a title="Delete"  class="delete-temp" href="{{ route('media.delete',['id'=>jsencode_userdata($singleFile->id)]) }}">
                        <i class="fas fa-trash" style="color:#FF0000"></i>
                    </a>
                    @else
                    <a title="Restore" href="{{ route('media.restore',['id'=>jsencode_userdata($singleFile->id)]) }}">
                        <i class="fas fa-trash-restore"></i>
                    </a>
                    @endif


                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    @if( empty($deleteRecords) )
                    No media uploaded yet!
                    @else
                    No media deleted yet!
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot class="table-footer">
            <tr>
                <td colspan="7">
                    {{ $data->appends(request()->except('dpage','open_section','page'))->links() }}
                    <p>
                        Displaying {{$data->count()}} of {{ $data->total() }} media(s).
                    </p>
                </td>
            </tr>
        </tfoot>
    </table>
</div>