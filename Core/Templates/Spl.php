<?php

namespace App\Core\Templates;

use App\Entities\TelegraphText;
use App\Entities\View;

class Spl extends View
{
    public function render(TelegraphText $telegraphText): string
    {
        $spl = file_get_contents(sprintf('templates/%s.spl', $this->templateName));
        foreach ($this->variables as $key) {
            $getterMethod = 'get' . ucfirst($key);
            $spl = str_replace('$$' . $key . '$$', $telegraphText->$getterMethod(), $spl);
        }
        return $spl;
    }
}