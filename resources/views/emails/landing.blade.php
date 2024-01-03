@component('mail::message')

<p>Name: {{ $user->name }}</p>
<p>Email: {{ $user->email }}</p>
<p>Contact Number: {{ $user->contact_no }}</p>

@isset($user->subject)
    Subject: {{ $user->subject }}
@endisset

@isset($user->msg)
    Message: {{ $user->msg }}
@endisset

New Lead For Business Joy
@endcomponent
