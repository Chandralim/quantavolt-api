@component('mail::message')

<hr style="widht:100px; color:black;"></hr>
<h1>Confirm Your Account</h1>
<hr style="widht:100px; color:black;"></hr>
<p>We receive request to send forget password code. Use this code to login to your account</p>

<br>
{{$code}}
<br>
Your Palm Oil X-Tion Team,
<br>
palmoilxtion.com
@endcomponent
