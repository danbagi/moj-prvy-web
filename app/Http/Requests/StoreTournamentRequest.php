<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTournamentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'sport' => ['required','in:FOOTBALL,BASKETBALL,HANDBALL,VOLLEYBALL,OTHER'],
            'season' => ['required','string','max:20'],
            'location' => ['nullable','string','max:255'],
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'format' => ['required','in:GROUPS_ONLY,GROUPS_PLUS_PLAYOFF,ROUND_ROBIN,KNOCKOUT'],
        ];
    }
}
