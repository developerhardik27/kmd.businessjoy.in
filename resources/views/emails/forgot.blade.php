@component('mail::message')

<p>Hello  {{$user->firstname}} </p>

<p>We understand it happens.</p>
    @component('mail::button',['url'=>url('admin/reset/'.$user->pass_token)])
        Reset Your Password
    @endcomponent

<p> In case you have any issues  recovering your password,please contact us.</p>

Thanks    
@endcomponent