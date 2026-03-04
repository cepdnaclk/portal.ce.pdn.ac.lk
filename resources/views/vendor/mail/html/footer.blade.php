<tr>
    <td>
        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td class="content-cell" align="center">
                    This is auto-generated email. Please do not reply to the sender. If you have any concern about the
                    email, or if you feel you are not the correct person, please contact us through
                    <a
                        href="mailto:{{ config('email-service.support_email') }}">{{ config('email-service.support_email') }}</a>.

                    <div style="padding-top: 1rem">
                        {{ Illuminate\Mail\Markdown::parse($slot) }}
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
