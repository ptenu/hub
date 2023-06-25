<div style="font-size: var(--fs-base)">
    @if($user->membership->status == 'failed')
        <div class="card" style="background-color: var(--colour-red-50)">
            <header>There is a problem with your membership</header>
            <p>
                We were unable to create your membership properly - this is probably because no payment
                was taken when you joined.
            </p>
            <footer>
                <a href="/renew">Renew your membership</a>
            </footer>
        </div>
    @endif

</div>
