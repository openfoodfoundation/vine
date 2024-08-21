@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ \Illuminate\Support\Facades\URL::to('/img/ofn-logo.png') }}" class="" alt="{{ config('app.name') }} Logo">
</a>
</td>
</tr>
