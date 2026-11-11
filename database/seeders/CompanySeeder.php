<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $airlines = Ticket::query()
            ->select('airline')
            ->distinct()
            ->pluck('airline')
            ->filter();

        foreach ($airlines as $airline) {
            $logo = Ticket::where('airline', $airline)->whereNotNull('airline_logo')->value('airline_logo');

            $company = Company::firstOrCreate(
                ['name' => $airline],
                ['logo' => $logo]
            );

            Ticket::where('airline', $airline)
                ->whereNull('company_id')
                ->update(['company_id' => $company->id]);
        }

        $skyAir = Company::where('name', 'SkyAir')->first();
        if ($skyAir && !$skyAir->logo) {
            $skyAir->update(['logo' => 'images/logo.svg']);
        }
        if ($skyAir) {
            Ticket::where('airline', 'SkyAir')
                ->whereNull('airline_logo')
                ->update(['airline_logo' => $skyAir->logo]);
        }
    }
}
