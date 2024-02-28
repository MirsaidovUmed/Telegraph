<?php

$textStorage = [];

class TelegraphText
{
    private string $title;
    private string $text;
    private string $author;
    private DateTimeImmutable $published;
    private string $slug;

    public function __construct(string $author, string $slug)
    {
        $this->author = $author;
        $this->slug = $slug;
        $this->published = new DateTimeImmutable();
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @param DateTimeImmutable $published
     */
    public function setPublished(DateTimeImmutable $published): void
    {
        $this->published = $published;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPublished(): DateTimeImmutable
    {
        return $this->published;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    public function storeText(): void
    {
        $textStorage = [
            'title' => $this->title,
            'text' => $this->text,
            'published' => $this->published,
            'author' => $this->author
        ];
        $serializeTextStorage = serialize($textStorage);
        file_put_contents($this->slug, $serializeTextStorage);
    }

    public function loadText(): string
    {
        if (file_exists($this->slug)) {
            $fileContent = file_get_contents($this->slug);
            if (!empty($fileContent)) {
                $textStorage = unserialize($fileContent);
                $this->title = $textStorage['title'];
                $this->text = $textStorage['text'];
                $this->author = $textStorage['author'];
                $this->published = $textStorage['published'];
            }
        }
        return $this->text;
    }

    public function editText(string $title, string $text): void
    {
        $this->title = $title;
        $this->text = $text;
    }
}

interface IRender
{
    public function render(TelegraphText $telegraphText): string;
}

abstract class View
{

    protected string $templateName;
    protected array $variables;


    public function __construct(string $templateName)
    {
        $this->templateName = $templateName;
    }

    public function addVariablesToTemplate(array $variables): void
    {
        $this->variables = $variables;

    }
}

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

class Txt extends View implements IRender
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


class Swig extends View implements IRender
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

abstract class Storage
{
    abstract function create(TelegraphText $text): string;

    abstract function read(string $slug): ?TelegraphText;

    abstract function update(TelegraphText $content, string $slug): void;

    abstract function delete(string $slug): void;

    abstract function list(): array;
}

abstract class User
{
    protected int $id;
    protected string $name;
    protected string $role;

    abstract function getTextsToEdit();
}

class FileStorage extends Storage
{
    public string $dir = "file_folder";

    private function getPath(string $slug): string
    {
        return $this->dir . DIRECTORY_SEPARATOR . $slug . '.txt';
    }

    public function create(TelegraphText $text): string
    {
        $slug = $text->getSlug() . '_' . date('Ymd');
        $count = 0;
        $suffix = '';
        while (file_exists($this->getPath($slug . $suffix))) {
            $count++;
            if ($count > 0) {
                $suffix = "_$count";
            }
        }
        $text->setSlug($slug . $suffix);
        file_put_contents($this->getPath($slug . $suffix), serialize($text));
        return $slug . $suffix;
    }


    public function read(string $slug): ?TelegraphText
    {
        if (file_exists($this->getPath($slug))) {
            return unserialize($this->getPath($slug));
        } else {
            return null;
        }
    }

    public function update(TelegraphText $content, string $slug): void
    {
        if (file_exists($this->getPath($slug))) {
            file_put_contents($this->getPath($slug), serialize($content));
        }
    }

    public function delete(string $slug): void
    {
        if (file_exists($this->getPath($slug))) {
            unlink($this->getPath($slug));
        }
    }

    /**
     * @return list<TelegraphText>
     */
    public function list(): array
    {
        $files = scandir($this->dir);
        $arrayFile = [];
        foreach ($files as $file) {
            $fullPath = $this->dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($fullPath)) {
                $data = file_get_contents($fullPath);
                $unpackedFile = unserialize($data);
                if ($unpackedFile instanceof TelegraphText) {
                    $arrayFile[] = $unpackedFile;
                }
            }
        }
        return $arrayFile;
    }
}

//$warAndPeace = new TelegraphText('Leo Tolstoy', 'war-and-peace');
//$warAndPeace->editText('War and Peace', 'Eh bien, mon prince. Gênes et Lucques...');
//
//$storage = new FileStorage();
//$storage->create($warAndPeace);

$telegraphText = new TelegraphText('Vasya', 'some slug');
$telegraphText->editText('some title', 'some text');

$swig = new Swig('telegraph_text');
$swig->addVariablesToTemplate(['slug', 'text']);

$spl = new Spl('telegraph_text');
$spl->addVariablesToTemplate(['slug', 'title', 'text']);

$txt = new Txt('telegraph_text');
$txt->addVariablesToTemplate(['slug', 'title', 'text']);


$templateEngines = [$swig, $spl, $txt];
foreach ($templateEngines as $engine) {
    if ($engine instanceof IRender) {
        echo $engine->render($telegraphText) . PHP_EOL;
    } else {
        echo 'Template engine does not support render interface' . PHP_EOL;
    }
}