@if( $userDetail->subscriptions->count() )
<div class=" table-responsive list-items">
    <table class="table">
        <thead>
            <tr>
                <th scope="col" class="purchase-order-date">
                    Plan Name
                </th>
                <th scope="col" class="purchase-order-date">
                    Status
                </th>
                <th scope="col">
                    Created At
                </th>
                <th scope="col">
                    Updated At
                </th>
                <th scope="col">
                    Ends At
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($userDetail->subscriptions as $singleSubscription)
            <tr>
                <td class="purchase-order-date">
                    {{ $singleSubscription->name }}
                </td>
                <td class="purchase-order-date">
                    {{ ucfirst($singleSubscription->stripe_status) }}
                </td>
                <td class="purchase-order-date">
                    {{ $singleSubscription->created_at }}
                </td>
                <td class="purchase-order-date">
                    {{ $singleSubscription->updated_at }}
                </td>
                <td class="purchase-order-date">
                    {{ $singleSubscription->ends_at ? $singleSubscription->ends_at : "NA" }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class="m-0">No subscription subscribed yet!</p>
@endif

<pre style="display:none">
    {{ print_r( $userDetail->subscriptions->toArray() , 1 ) }}
</pre>
<pre style="display:none">
    {{ print_r( $userDetail->user_detail->body_measurements , 1 ) }}
</pre>
<pre style="display:none">
    {{ print_r( $userDetail->user_detail->current_photos , 1 ) }}
</pre>