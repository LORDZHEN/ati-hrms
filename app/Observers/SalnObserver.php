<?php

namespace App\Observers;

use App\Models\Saln;
use App\Models\TransactionHistory;
use App\Models\User;

class SalnObserver
{
    public function created(Saln $saln): void
    {
        $user = User::find($saln->user_id);
        $employeeName = $this->resolveName($saln, $user);
        $dateLabel = $saln->as_of_date?->format('F j, Y') ?? (string) now()->year;

        TransactionHistory::log([
            'user_id' => $saln->user_id,
            'employee_name' => $employeeName,
            'transaction_type' => 'SALN Submission',
            'module' => 'SALN',
            'description' => "SALN as of {$dateLabel} was submitted.",
            'status' => 'filed',
            'icon' => 'heroicon-o-document-text',
            'color' => 'rose',
            'record_id' => $saln->id,
            'record_url' => self::safeRoute('filament.hrms.resources.salns.view', $saln->id),
        ]);
    }

    public function updated(Saln $saln): void
    {
        if (!$saln->isDirty('status')) {
            return;
        }

        $user = User::find($saln->user_id);
        $employeeName = $this->resolveName($saln, $user);
        $dateLabel = $saln->as_of_date?->format('F j, Y') ?? (string) now()->year;

        TransactionHistory::log([
            'user_id' => $saln->user_id,
            'employee_name' => $employeeName,
            'transaction_type' => 'SALN Submission',
            'module' => 'SALN',
            'description' => "SALN as of {$dateLabel} was {$saln->status}.",
            'status' => $saln->status,
            'icon' => 'heroicon-o-document-text',
            'color' => match ($saln->status) {
                'approved' => 'green',
                'disapproved' => 'red',
                default => 'rose',
            },
            'record_id' => $saln->id,
            'record_url' => self::safeRoute('filament.hrms.resources.salns.view', $saln->id),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resolveName(Saln $saln, ?User $user): string
    {
        return $user?->full_name
            ?? trim(implode(' ', array_filter([
                $saln->declarant_first_name ?? null,
                $saln->declarant_middle_initial ?? null,
                $saln->declarant_family_name ?? null,
            ])))
            ?: 'Unknown Employee';
    }

    private static function safeRoute(string $name, mixed $params): ?string
    {
        try {
            return route($name, $params);
        } catch (\Exception) {
            return null;
        }
    }
}
