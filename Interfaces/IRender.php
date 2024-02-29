<?php

namespace App\Interfaces;

use App\Entities\TelegraphText;

interface IRender
{
    public function render(TelegraphText $telegraphText): string;
}