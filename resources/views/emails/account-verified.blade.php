@component('mail::message')
# Hello, {{ $user->first_name }}! 🎉

Your employee account registration has been **approved** by the HR Administrator.

You can now log in to the **ATI-HRMS** system using the credentials below.

---

@component('mail::panel')
**Login URL:** {{ config('app.url') }}

**Email Address:** {{ $user->email }}

**Temporary Password:** `{{ $temporaryPassword }}`
@endcomponent

---

> ⚠️ **Important:** Your temporary password is your **birthday** in **MMDDYYYY** format.
> For example, if your birthday is December 4, 2002, your password is `12042002`.
>
> You will be **required to change your password** immediately after your first login.

@component('mail::button', ['url' => config('app.url'), 'color' => 'success'])
Login to ATI-HRMS
@endcomponent

If you did not register for this account or believe this was sent in error,
please contact the HR office immediately.

Thanks,
**ATI-HRMS Team**
Agricultural Training Institute
@endcomponent
