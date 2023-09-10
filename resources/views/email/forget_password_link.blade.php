@component('mail::message', ['url' => $url])

<hr style="widht:100px; color:black;"></hr>
<h1>Forget Password Request Link</h1>
<hr style="widht:100px; color:black;"></hr>
<p>We get request to set new password from your Palm Oil Xtion Account. If you want to set new password please click link below:</p>


@component('mail::button', ['url' => ($url??config('app.url'))."/visitor/setNewPassword?token=".$email_token."&code=".$code])
Set New Password
@endcomponent


Or copy this link:
<br>
{{($url??config('app.url'))."/visitor/setNewPassword?token=".$email_token."&code=".$code}}
<br>
Your Palm Oil X-Tion Team,
<br>
palmoilxtion.com
@endcomponent
