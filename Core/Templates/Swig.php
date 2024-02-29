<?php

namespace App\Core\Templates;

use App\Entities\TelegraphText;
use App\Entities\View;

class Swig extends View
{
    public function render(TelegraphText $telegraphText): string
    {
        $swig = file_get_contents(sprintf('templates/%s.swig', $this->templateName));
        foreach ($this->variables as $key) {
            $getterMethod = 'get' . ucfirst($key);
            $swig = str_replace('{{ ' . $key . ' }}', $telegraphText->$getterMethod(), $swig);
        }
        return $swig;
    }
}