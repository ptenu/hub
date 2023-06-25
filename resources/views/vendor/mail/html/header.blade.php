@props(['url'])

<table class="wrapper">
    <tr>
        <td class="float-center" align="center" valign="top">
            <center>
                <table class="container header">
                    <tr>
                        <td>
                            <table class="row">
                                <tr>
                                    <th class="first columns">
                                        <a href="{{ $url }}">
                                            <img
                                                src="https://static.peterboroughtenants.app/logos/2023/PNG/header.png"
                                                alt="PTU Logo"
                                                class="logo"
                                            />
                                        </a>
                                    </th>
                                    <th class="columns">
                                        <p class="text-right">
                                            {{ Illuminate\Mail\Markdown::parse($slot) }}
                                        </p>
                                    </th>
                                    <th class="expander"></th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>
