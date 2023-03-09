<div class=" table-responsive list-items">
    <table class="table">
        <thead>
            <tr>
                <th scope="col" class="purchase-order-date">
                    @sortablelink('amount_paid', 'Amount Paid')
                </th>
                <th scope="col" class="purchase-order-date">
                    Subscription
                </th>
                <th scope="col">
                    User
                </th>
                <th scope="col">
                    @sortablelink('created_at', 'Paid On')
                    {{-- Paid On --}}
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $payment)
            <tr>
                <td class="purchase-order-date">
                    {{ $payment->amount_paid }}
                </td>
                <td class="purchase-order-date">
                    {{ $payment->subscription->name }}
                </td>
                <td>
                    {{ $payment->subscription->user->full_name }}
                </td>
                <td>
                    {{ $payment->created_at }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    No Payment received yet!
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot class="table-footer">
            <tr>
                <td colspan="7">
                    {{ $data->appends(request()->except('dpage','page','open_section'))->links() }}
                    <p>
                        Displaying {{$data->count()}} of {{ $data->total() }} exercise(s).
                    </p>
                </td>
            </tr>
        </tfoot>
    </table>
</div>