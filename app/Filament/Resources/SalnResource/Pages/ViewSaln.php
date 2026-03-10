<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use App\Notifications\SalnRemarksAdded;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSaln extends ViewRecord
{
    protected static string $resource = SalnResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        view()->share('isReadOnly', true);
    }

    protected function getHeaderActions(): array
    {
        $isAdmin = auth()->user()?->role === 'admin';

        return [
            // ── Approve ──────────────────────────────────────────────────
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(function () use ($isAdmin) {
                    $this->record->refresh();
                    return $isAdmin && in_array($this->record->status, ['submitted', 'disapproved']);
                })
                ->requiresConfirmation()
                ->modalHeading('Approve SALN')
                ->modalDescription('Are you sure you want to approve this SALN?')
                ->modalSubmitActionLabel('Yes, Approve')
                ->action(function () {
                    $this->record->update(['status' => 'approved', 'remarks' => null]);
                    $this->record->user?->notify(new SalnRemarksAdded($this->record));
                    \Filament\Notifications\Notification::make()
                        ->title('SALN Approved')->success()->send();
                    $this->record->refresh();
                    $this->refreshFormData(['status', 'remarks']);
                }),

            // ── Disapprove ───────────────────────────────────────────────
            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(function () use ($isAdmin) {
                    $this->record->refresh();
                    return $isAdmin && in_array($this->record->status, ['submitted', 'approved']);
                })
                ->form([
                    \Filament\Forms\Components\Textarea::make('remarks')
                        ->label('Reason for Disapproval')->required()->rows(3)
                        ->placeholder('Explain why this SALN is being disapproved...'),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'disapproved', 'remarks' => $data['remarks']]);
                    $this->record->user?->notify(new SalnRemarksAdded($this->record));
                    \Filament\Notifications\Notification::make()
                        ->title('SALN Disapproved')->danger()->send();
                    $this->record->refresh();
                    $this->refreshFormData(['status', 'remarks']);
                }),

            // ── Remarks ──────────────────────────────────────────────────
            Actions\Action::make('remarks')
                ->label(fn() => blank($this->record->remarks) ? 'Add Remarks' : 'Edit Remarks')
                ->icon('heroicon-o-chat-bubble-left-right')->color('warning')
                ->visible(fn() => $isAdmin)
                ->fillForm(fn() => ['remarks' => $this->record->remarks])
                ->form([
                    \Filament\Forms\Components\Textarea::make('remarks')
                        ->label('Admin Remarks')->rows(4)->required()
                        ->placeholder('Enter administrative remarks or comments...'),
                ])
                ->action(function (array $data) {
                    $this->record->update(['remarks' => $data['remarks']]);
                    $this->record->user?->notify(new SalnRemarksAdded($this->record));
                    \Filament\Notifications\Notification::make()
                        ->title('Remarks Updated')->success()->send();
                    $this->record->refresh();
                    $this->refreshFormData(['remarks']);
                }),

            // ── Print ────────────────────────────────────────────────────
            Actions\Action::make('print')
                ->label('Print SALN')->icon('heroicon-o-printer')->color('success')
                ->url(fn() => route('saln.print', $this->record))->openUrlInNewTab(),

            // ── Back ─────────────────────────────────────────────────────
            Actions\Action::make('back')
                ->label('Back to List')->icon('heroicon-o-arrow-left')->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
