<tr>

    @isset($left)
        <td class="content-cell footer-left" align="left">{{ Illuminate\Mail\Markdown::parse($left) }}</td>
    @endisset

    @isset($center)
        <td class="content-cell footer-center" align="center">{{ Illuminate\Mail\Markdown::parse($center) }}</td>
    @endisset

    @isset($right)
        <td class="content-cell footer-right" align="right">{{ Illuminate\Mail\Markdown::parse($right) }}</td>
    @endisset

</tr>
