<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    use ValidatesProjectInput;

    public function authorize(): bool
    {
        return true;
    }
}
