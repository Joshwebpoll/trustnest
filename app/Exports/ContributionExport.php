<?php

namespace App\Exports;

use App\Models\CpContribution;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContributionExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return CpContribution::all()->map(function ($contribution) {
            return [
                "id" => $contribution->id,
                "transaction_id" => $contribution->transaction_id,
                "contribution_type" => $contribution->contribution_type,
                "amount_contributed" => Crypt::decryptString($contribution->amount_contributed),
                "reference_number" => $contribution->reference_number,
                "account_number" => $contribution->account_number,
                "payment_method" => $contribution->payment_method,
                "contribution_date" => $contribution->contribution_date,
                "status" => $contribution->status,
                "contribution_deposit_type" => $contribution->contribution_deposit_type,
                "processed_by_name" => $contribution->processed_by_name,



            ];
        });
    }

    public function headings(): array
    {
        return [
            'id',
            "Transaction id",
            "Contribution Type",
            "Amount Contributed",
            'Reference Number',
            'Account Number',
            'Payment Method',
            "Contribution Date",
            'Status',
            "Contribution Deposit Type",
            "Process Name"
        ];
    }
}
