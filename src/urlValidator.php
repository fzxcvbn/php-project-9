<?php

namespace App;

use Valitron\Validator;

class UrlValidator
{
    public function __construct(private Validator $validator)
    {
        $this->validator->rule('required', 'name')->message('URL не должен быть пустым');
        $this->validator->rule('lengthMax', 'name', 255)->message('URL не должен быть пустым');
        $this->validator->rule('url', 'name')->message('Некорректный URL');
    }

    public function validate(array $url): bool
    {
        $this->validator = $this->validator->withData($url);

        return $this->validator->validate();
    }

    public function getErrors(): array
    {
        return $this->validator->errors();
    }
}