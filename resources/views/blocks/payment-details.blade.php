@if($user->membership->payments()->exists())
    <ptu-details summary="Payment history">
        <table style="margin-top: 0">
            <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Status</th>
                <th style="text-align: right">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($user->membership->payments()->orderBy('created_at', 'desc')->limit('12')->get() as $p)
                <tr>
                    <td>
                        {{$p->created_at->format('j M Y')}}
                    </td>
                    <td>
                        {{ucfirst($p->description)}}
                    </td>
                    <td>
                        @php
                        $colourMap = [
                            'succeeded' => 'green',
                            'processing' => 'blue',
                            'canceled' => 'red',
                            'requires_payment_method' => 'yellow',
                            'requires_action' => 'yellow',
                            'requires_confirmation' => 'yellow',
                            'requires_capture' => 'yellow'
                        ];
                        @endphp
                        <ptu-chip colour="{{ $colourMap[$p->status] }}">
                            {{$p->status}}
                        </ptu-chip>
                    </td>
                    <td style="text-align: right">
                        £ {{ number_format($p->amount / 100, 2, '.', '') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </ptu-details>
@endif
