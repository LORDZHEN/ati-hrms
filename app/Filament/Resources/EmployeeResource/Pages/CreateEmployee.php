<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;


class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function afterCreate(): void
    {
        $newUser = $this->record;

        if ($newUser->role === 'admin') {
            // Admin accounts active immediately
            $tempPassword = \Str::random(6);
            $newUser->update([
                'status' => 'active',
                'verification_status' => 'verified',
                'email_verified_at' => now(),
                'must_change_password' => true,
                'password' => \Hash::make($tempPassword),
            ]);

            Mail::to($newUser->email)->send(new \App\Mail\AdminTemporaryPasswordMail($newUser, $tempPassword));
        } else {
            // Employee accounts pending
            Mail::to($newUser->email)->send(new \App\Mail\PendingRegistrationMail($newUser));
        }

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('New Employee Registered')
                ->body("A new employee account was created: **{$newUser->name}**")
                ->success()
                ->sendToDatabase($admin);
        }
    }
}
