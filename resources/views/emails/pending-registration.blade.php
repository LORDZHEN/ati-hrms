@component('mail::message')
# Hello, {{ $user->first_name }}! 👋

Thank you for registering with the **ATI-HRMS** system.

Your account has been received and is currently **pending review** by the HR Administrator.

@component('mail::panel')
**Name:** {{ $user->name }}

**Email:** {{ $user->email }}

**Status:** Pending Approval 🕐
@endcomponent

Once your account is approved, you will receive another email containing your **login credentials** and temporary password.

> Please do **not** reply to this email. For concerns, contact the HR office directly.

Thanks,
**ATI-HRMS Team**
Agricultural Training Institute
@endcomponent
