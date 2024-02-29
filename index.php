<?php

namespace App;

use App\Core\Templates\Swig;
use App\Entities\TelegraphText;
use App\Core\Templates\Spl;
use App\Core\Templates\Txt;
use App\Entities\FileStorage;
use App\Interfaces\IRender;

require_once 'autoload.php';

$warAndPeace = new TelegraphText('Leo Tolstoy', 'war-and-peace');
$warAndPeace->editText('War and Peace', 'Eh bien, mon prince. Gênes et Lucques...');

$storage = new FileStorage();
$storage->create($warAndPeace);

$swig = new Swig('telegraph_text');
$swig->addVariablesToTemplate(['slug', 'text']);

$txt = new Txt('telegraph_text');
$txt->addVariablesToTemplate(['slug', 'title', 'text', 'author']);

$spl = new Spl('telegraph_text');
$spl->addVariablesToTemplate(['slug', 'title', 'text']);

$templateEngines = [$swig, $txt, $spl];
foreach ($templateEngines as $engine) {
    if ($engine instanceof IRender) {
        echo $engine->render($warAndPeace) . PHP_EOL;
    } else {
        echo 'Template engine does mot support render interface' . PHP_EOL;
    }
}