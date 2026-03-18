<?php

namespace App\Http\Controllers;

use App\Models\Saln;
use Illuminate\Http\Request;

class SalnPrintController extends Controller
{
    public function print(Saln $saln)
    {
        try {
            $saln->load([
                'user',
                'children',
                'realProperties',
                'personalProperties',
                'liabilities',
                'businessInterests',
                'relativesInGovernment',
                // Issue #1 fix: Annex B relationships (declarant's exclusive properties)
                'annexBRealProperties',
                'annexBPersonalProperties',
                'annexBLiabilities',
                'annexBBusinessInterests',
                // Issue #1 fix: Annex C relationships (spouse & children's exclusive properties)
                'annexCRealProperties',
                'annexCPersonalProperties',
                'annexCLiabilities',
                'annexCBusinessInterests',
            ]);

            // Calculate totals if not already calculated
            $totalRealPropertyValue = $saln->realProperties->sum('current_fair_market_value');
            $totalPersonalPropertyValue = $saln->personalProperties->sum('acquisition_cost');
            $totalAssets = $totalRealPropertyValue + $totalPersonalPropertyValue;
            $totalLiabilities = $saln->liabilities->sum('outstanding_balance');
            $netWorth = $totalAssets - $totalLiabilities;

            if (
                $saln->total_assets != $totalAssets ||
                $saln->total_liabilities != $totalLiabilities ||
                $saln->net_worth != $netWorth
            ) {
                $saln->update([
                    'total_assets' => $totalAssets,
                    'total_liabilities' => $totalLiabilities,
                    'net_worth' => $netWorth,
                ]);

                $saln->refresh();
            }

            return view('filament.employee.saln.print', compact('saln'));

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('SALN Print Error: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'Unable to generate SALN print view. Please try again.');
        }
    }

    /**
     * Alternative method for opening print in new window/tab
     */
    public function printWindow(Saln $saln)
    {
        return $this->print($saln);
    }
}
