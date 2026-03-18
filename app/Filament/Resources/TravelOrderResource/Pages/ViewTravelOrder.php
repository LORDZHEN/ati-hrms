<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\User;
use App\Notifications\TravelOrderStatusUpdated;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewTravelOrder extends ViewRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ── APPROVE ───────────────────────────────────────────────────────
            // The approval modal includes the administrative fields so the admin
            // can fill them in as part of the approval decision. This sidesteps
            // the ViewRecord read-only limitation entirely.
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->modalHeading('Approve Travel Order')
                ->modalDescription('Fill in the administrative details below, then confirm approval.')
                ->modalSubmitActionLabel('Approve & Save')
                ->modalWidth('2xl')
                ->visible(
                    fn() => $this->record->status === 'pending'
                    && Auth::user()->role === User::ROLE_ADMIN
                )
                ->form([
                    Forms\Components\Section::make('Administrative Details')
                        ->description('These values will be printed on the official travel order form.')
                        ->schema([
                            Forms\Components\TextInput::make('assistant_laborer_allowed')
                                ->label('Assistant and/or Laborer Allowed')
                                ->placeholder('e.g., None, 1 Laborer')
                                ->default(fn() => $this->record->assistant_laborer_allowed)
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('per_diems_expenses_allowed')
                                ->label('Per Diems / Expenses Allowed')
                                ->placeholder('e.g., Actual, ₱750/day')
                                ->default(fn() => $this->record->per_diems_expenses_allowed)
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('appropriation_funds')
                                ->label('Appropriation / Funds')
                                ->placeholder('e.g., MOOE, GAA 2024')
                                ->default(fn() => $this->record->appropriation_funds)
                                ->columnSpan(1),

                            Forms\Components\Textarea::make('remarks_special_instructions')
                                ->label('Remarks / Special Instructions')
                                ->placeholder('Any special instructions or additional remarks...')
                                ->rows(3)
                                ->default(fn() => $this->record->remarks_special_instructions)
                                ->columnSpan(1),
                        ])
                        ->columns(2),
                ])
                ->action(function (array $data) {
                    // Save administrative fields first
                    $this->record->update([
                        'assistant_laborer_allowed' => $data['assistant_laborer_allowed'] ?? null,
                        'per_diems_expenses_allowed' => $data['per_diems_expenses_allowed'] ?? null,
                        'appropriation_funds' => $data['appropriation_funds'] ?? null,
                        'remarks_special_instructions' => $data['remarks_special_instructions'] ?? null,
                    ]);

                    // Then approve via model method (handles batch propagation)
                    $this->record->approve(Auth::user());

                    $this->record->creator->notify(new TravelOrderStatusUpdated($this->record));

                    Notification::make()
                        ->title('Travel Order Approved')
                        ->body('The employee has been notified.')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                        'rejection_remark',
                        'assistant_laborer_allowed',
                        'per_diems_expenses_allowed',
                        'appropriation_funds',
                        'remarks_special_instructions',
                    ]);
                }),

            // ── REJECT ────────────────────────────────────────────────────────
            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(
                    fn() => $this->record->status === 'pending'
                    && Auth::user()->role === User::ROLE_ADMIN
                )
                ->form([
                    Forms\Components\Textarea::make('rejection_remark')
                        ->label('Reason for Rejection')
                        ->required()
                        ->rows(3)
                        ->placeholder('Please provide a clear reason for rejecting this travel order...'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Reject Travel Order')
                ->modalSubmitActionLabel('Yes, Reject')
                ->action(function (array $data) {
                    $this->record->reject($data['rejection_remark']);

                    $this->record->creator->notify(new TravelOrderStatusUpdated($this->record));

                    Notification::make()
                        ->title('Travel Order Rejected')
                        ->body('The employee has been notified.')
                        ->danger()
                        ->send();

                    $this->refreshFormData([
                        'status',
                        'rejection_remark',
                    ]);
                }),

            // ── PRINT (approved orders only) ──────────────────────────────────
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('travel-order.print', $this->record->id))
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->status === 'approved'),

            // NOTE: EditAction intentionally removed.
            // Admins fill administrative details via the Approve modal above.
            // Regular employees edit via the table row action (rejected orders only).
        ];
    }
}
