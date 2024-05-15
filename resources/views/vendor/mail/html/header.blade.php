@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{asset('admin/images/bjlogo3.png')}}" class="logo" alt="BusinesJoy Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
