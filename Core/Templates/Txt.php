<?php

namespace App\Core\Templates;

use App\Entities\TelegraphText;
use App\Entities\View;

class Txt extends View
{
    public function render(TelegraphText $telegraphText): string
    {
        $txt = file_get_contents(sprintf('templates/%s.txt', $this->templateName));
        foreach ($this->variables as $key) {
            $getterMethod = 'get' . ucfirst($key);
            $txt = str_replace('%%' . $key . '%%', $telegraphText->$getterMethod(), $txt);
        }
        return $txt;
    }
}