@component('mail::message', ['url' => $url])

<hr style="widht:100px; color:black;"></hr>
<h1>Confirm Your Account</h1>
<hr style="widht:100px; color:black;"></hr>
<p>Welcome to Palm Oil X-Tion. Please verify your email address to confirm your account by clicking below:</p>


@component('mail::button', ['url' => ($url??config('app.url'))."/verify/email/".$email_token])
Verify Email
@endcomponent


Or copy this link:
<br>
{{($url??config('app.url')).'/verify/email/'.$email_token}}
<br>
Your Palm Oil X-Tion Team,
<br>
palmoilxtion.com
@endcomponent
