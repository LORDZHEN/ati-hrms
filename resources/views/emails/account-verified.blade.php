@component('mail::message')
# Hello {{ $user->first_name }},

Your employee account has been successfully verified by the HR administrator.

**Temporary Password:** `{{ $temporaryPassword }}`

Please use this password to log in for the first time. You will be required to change your password upon login.

If you did not request this registration, please contact support immediately.

Thanks,
ATI HRMS Team
@endcomponent
