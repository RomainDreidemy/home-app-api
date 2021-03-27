<?php


namespace App\Utils;


class ErrorHelper
{
    public function __construct(
        public bool $status = false,
        public string $message = 'Une erreur est survenu',
        public mixed $data = null
    ){}
}