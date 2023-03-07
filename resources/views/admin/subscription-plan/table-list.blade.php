<div class=" table-responsive list-items">
    <table class="table">
        <thead>
            <tr>
                <th scope="col" class="purchase-order-date">
                    @sortablelink('name', 'Name')
                </th>
                <th scope="col" class="purchase-order-date">
                    Price
                </th>
                <th scope="col">
                    Validity(Months)
                </th>
                <th scope="col">@sortablelink('created_at', 'Created Date')</th>
                <th scope="col" class="text-center status-text purchase-order-date" style="display:table-cell">
                    @sortablelink('status', 'Status')
                </th>
                <th scope="col" class="purchase-order-date text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $singlePage)
            <tr>
                <td class="purchase-order-date">
                    <a href="#" class="open-section get-workout-detail" data-target="workout-details" data-workout-id="{{ jsencode_userdata($singlePage->id) }}"></a>
                    {{ $singlePage->name }}
                </td>
                <td class="purchase-order-date">
                    ${{ $singlePage->price }}
                </td>
                <td>
                    {{ $singlePage->billing_interval_count }}
                </td>
                <td>
                    {{ changeDateFormat($singlePage->created_at) }}
                </td>
                <td class="text-center status-text">
                    <input data-id="{{ jsencode_userdata($singlePage->id) }}" class="toggle-class"  data-style="ios" type="checkbox" data-onstyle="success" data-offstyle="danger" data-height="20" data-width="70" data-toggle="toggle"  data-size="mini" data-on="Active" data-off="InActive" {{ $singlePage->status ? 'checked' : '' }}>
                </td>
                <td class="text-center purchase-order-date">
                    @if( empty($deleteRecords) )
                    <a title="Edit" href="{{ route('subscription-plan.edit',['id'=>jsencode_userdata($singlePage->id)]) }}" ><i class="fas fa-pencil-alt" style="color:#33383a"></i></a>&nbsp;&nbsp;
                    
                    <a title="Delete"  class="delete-temp" href="{{ route('subscription-plan.delete',['id'=>jsencode_userdata($singlePage->id)]) }}">
                        <i class="fas fa-trash" style="color:#FF0000"></i>
                    </a>
                    @else
                    <a title="Restore" href="{{ route('subscription-plan.restore',['id'=>jsencode_userdata($singlePage->id)]) }}">
                        <i class="fas fa-trash-restore"></i>
                    </a>
                    @endif


                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    @if( empty($deleteRecords) )
                    No subscription plan created yet!
                    @else
                    No subscription plan deleted yet!
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot class="table-footer">
            <tr>
                <td colspan="7">
                    {{ $data->appends(request()->except('dpage','page','open_section'))->links() }}
                    <p>
                        Displaying {{$data->count()}} of {{ $data->total() }} Subscription Plan(s).
                    </p>
                </td>
            </tr>
        </tfoot>
    </table>
</div>