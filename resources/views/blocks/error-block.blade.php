@if(count($errors) > 0)
    <ptu-section>
        <article class="card prose danger">
            <header>There was a problem:</header>
            <ul role="list">
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </article>
    </ptu-section>
@endif
