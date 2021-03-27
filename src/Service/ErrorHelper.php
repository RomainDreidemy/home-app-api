<?php


namespace App\Service;


class ErrorHelper
{
    public function __construct(
        public bool $status = false,
        public string $message = 'Une erreur est survenu',
        mixed $data = null
    ){}
}