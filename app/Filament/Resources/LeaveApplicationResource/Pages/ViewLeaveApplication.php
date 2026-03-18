<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewLeaveApplication extends ViewRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        view()->share('isReadOnly', true);
    }

    // =========================================================================
    //  HEADER ACTIONS
    //
    //  ADMIN    : Approve (if not yet approved) | Add/Edit Remarks | Print (approved) | Back
    //  EMPLOYEE : Print (approved only) | Back
    //             Admin remarks are shown via the Textarea in the form schema.
    // =========================================================================

    protected function getHeaderActions(): array
    {
        $isAdmin = Auth::user()->role === 'admin';

        return [

            // ── ADMIN: Approve ────────────────────────────────────────────────
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $isAdmin && $this->record->status !== 'approved')
                ->requiresConfirmation()
                ->modalHeading('Approve Leave Application')
                ->modalDescription('Are you sure you want to approve this leave application?')
                // AFTER
                ->action(function () {
                    \Illuminate\Support\Facades\DB::transaction(function () {
                        // ── Duplicate-deduction guard — inside transaction ────────
                        $alreadyDeducted = \App\Models\LeaveCreditLog::where('leave_application_id', $this->record->id)
                            ->where('transaction_type', 'deduction')
                            ->exists();

                        // ── Status update ─────────────────────────────────────────
                        $this->record->update(['status' => 'approved']);

                        if (!$alreadyDeducted) {
                            // ── Snapshot columns for print blade (Bug #4) ─────────
                            $credit = \App\Models\LeaveCredit::where('user_id', $this->record->employee_id)
                                ->lockForUpdate()
                                ->first();

                            if ($credit) {
                                $days = (float) $this->record->number_of_working_days;
                                $vlBefore = (float) $credit->vacation_leave_balance;
                                $slBefore = (float) $credit->sick_leave_balance;
                                $balanceCol = \App\Models\LeaveCredit::balanceColumn($this->record->type_of_leave);
                                $vlLess = ($balanceCol === 'vacation_leave_balance') ? $days : 0;
                                $slLess = ($balanceCol === 'sick_leave_balance') ? $days : 0;

                                // AFTER
                                $this->record->update([
                                    'as_of_date' => now()->toDateString(),
                                    'date_approved_disapproved' => now()->toDateString(),
                                    'authorized_officer' => Auth::user()->name,
                                    'vacation_leave_total_earned' => $vlBefore,
                                    'vacation_leave_less_application' => $vlLess,
                                    'vacation_leave_balance' => max(0, $vlBefore - $vlLess),
                                    'sick_leave_total_earned' => $slBefore,
                                    'sick_leave_less_application' => $slLess,
                                    'sick_leave_balance' => max(0, $slBefore - $slLess),
                                    // 7.C: All approved days go to "with pay" by default (no split logic in system).
                                    // If without-pay or other splits are needed in future, set them explicitly here.
                                    'approved_days_with_pay' => $this->record->number_of_working_days,
                                    'approved_days_without_pay' => null,
                                    'approved_others' => null,
                                ]);
                            }

                            // ── Credit deduction ──────────────────────────────────
                            app(\App\Services\LeaveCreditService::class)
                                ->deductForApplication($this->record);
                        }
                    });

                    // ── Outside transaction: notifications and redirect ───────────
                    $this->record->user?->notify(new \App\Notifications\LeaveApplicationStatusUpdated($this->record));
                    Notification::make()
                        ->success()
                        ->title('Leave Application Approved')
                        ->body('The leave application has been approved and the employee has been notified.')
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            // AFTER — insert after the closing of the 'approve' Actions\Action::make() block
            // ── ADMIN: Disapprove ─────────────────────────────────────────────
            // AFTER
            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => $isAdmin && $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Disapprove Leave Application')
                ->modalDescription('Please provide a reason for disapproval.')
                ->form([
                    \Filament\Forms\Components\Textarea::make('disapproval_reason')
                        ->label('Reason for Disapproval')
                        ->rows(3)
                        ->required()
                        ->placeholder('State the reason for disapproval...'),
                ])
                ->action(function (array $data) {
                    \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
                        $this->record->update([
                            'status' => 'disapproved',
                            'disapproval_reason' => $data['disapproval_reason'],
                            'authorized_officer' => Auth::user()->name,
                            'date_approved_disapproved' => now()->toDateString(),
                        ]);
                    });

                    $this->record->user?->notify(new \App\Notifications\LeaveApplicationStatusUpdated($this->record));
                    Notification::make()
                        ->warning()
                        ->title('Leave Application Disapproved')
                        ->body('The leave application has been disapproved and the employee has been notified.')
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            // ── ADMIN: Add / Edit Remarks ─────────────────────────────────────
            Actions\Action::make('remarks')
                ->label(fn() => blank($this->record->remarks) ? 'Add Remarks' : 'Edit Remarks')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning')
                ->visible(fn() => $isAdmin)
                ->fillForm(fn() => ['remarks' => $this->record->remarks])
                ->form([
                    \Filament\Forms\Components\Textarea::make('remarks')
                        ->label('Admin Remarks')
                        ->rows(5)
                        ->required()
                        ->placeholder('Add notes or feedback for the employee...'),
                ])
                ->action(function (array $data) {
                    $this->record->update(['remarks' => $data['remarks']]);
                    $this->record->user?->notify(new \App\Notifications\LeaveApplicationRemarksAdded($this->record));
                    Notification::make()
                        ->success()
                        ->title('Remarks Updated')
                        ->body('Remarks saved and the employee has been notified.')
                        ->send();
                    // Refresh so remarks are visible immediately
                    $this->redirect(request()->header('Referer'));
                }),

            // ── ADMIN + EMPLOYEE: Print (approved only) ───────────────────────
            Actions\Action::make('print')
                ->label('Print Leave Form')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => $this->record->status === 'approved')
                ->url(fn() => route('leave_application.print', $this->record->id))
                ->openUrlInNewTab(),

            // ── Back to list ──────────────────────────────────────────────────
            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
