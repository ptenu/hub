@props(['url'])

<tr>
    <td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
        <table class="inner-header" align="center" width="570" cellpadding="0" cellspacing="0"
               role="presentation">
            <!-- Header content -->
            <tr>
                <td class="content-cell">
                    <table>
                        <tr>
                            <td class="header" style="text-align: left; vertical-align: top">
                                <a href="{{ $url }}" style="display: inline-block;">
                                    <img src="https://static.peterboroughtenants.app/logos/2023/PNG/header.png" class="logo" alt="PTU Logo">
                                </a>
                            </td>
                            <td style="text-align: right; vertical-align: top">
                                {{ Illuminate\Mail\Markdown::parse($slot) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
