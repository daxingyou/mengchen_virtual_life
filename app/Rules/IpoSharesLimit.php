<?php

namespace App\Rules;

use App\Models\Configuration;
use Illuminate\Contracts\Validation\Rule;

class IpoSharesLimit implements Rule
{
    protected $configuration;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
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
        $ipoSharesLimitation = $this->configuration->base_ipo_shares;
        return $value <= $ipoSharesLimitation;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute 超过了发行数量限制';
    }
}
