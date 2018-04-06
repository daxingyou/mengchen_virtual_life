<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StockOrderStatusRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $allStatus = [1, 2, 3, 4];
        $status = explode(',', $value);
        return !empty($status) and empty(array_diff($status, $allStatus));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute 验证失败.';
    }
}
