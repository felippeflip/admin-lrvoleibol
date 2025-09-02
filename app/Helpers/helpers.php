<?php

use Illuminate\Support\Carbon;

if (!function_exists('carbon')) {
    /**
     * Get a Carbon instance.
     *
     * @param  mixed  $time
     * @param  string|null  $tz
     * @return \Carbon\Carbon
     */
    function carbon($time = null, $tz = null)
    {
        return Carbon::parse($time, $tz);
    }
}

if (!function_exists('removeSpecialCharsFromCPF')) {
    /**
     * Remove special characters from CPF string, leaving only numbers.
     *
     * @param  string  $cpf
     * @return string
     */
    function removeSpecialCharsFromCPF($cpf)
    {
        // Remove tudo que não for números
        return preg_replace('/[^0-9]/', '', $cpf);
    }


if(!function_exists('removeSpecialCharsFromCNPJ')) {
    /**
     * Remove special characters from CNPJ string, leaving only numbers.
     *
     * @param  string  $cnpj
     * @return string
     */
    function removeSpecialCharsFromCNPJ($cnpj)
    {
        // Remove tudo que não for números
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

}

}
