@if($user->branch)
    <ptu-details summary="{{$user->branch->name}} Branch">
        <p style="color: var(--colour-grey-700); margin-bottom: var(--layout-gap)">{{$user->branch->description}}</p>
        <dl>
            <dt>Branch name</dt>
            <dd>
                {{$user->branch->full_name}}
            </dd>
        </dl>
    </ptu-details>
@endif
