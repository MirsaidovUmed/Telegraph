<?php

namespace App;

use App\Core\Templates\Swig;
use App\Entities\TelegraphText;
use App\Core\Templates\Spl;
use App\Core\Templates\Txt;
use App\Entities\FileStorage;
use App\Interfaces\IRender;

require_once 'autoload.php';

$warAndPeace = new TelegraphText('Leo Tolstoy', 'war-and-peace', 'Leo', 'Slug');
$warAndPeace->editText('War and Peace', 'Eh bien, mon prince. Gênes et Lucques...');

$storage = new FileStorage();
$storage->create($warAndPeace);
