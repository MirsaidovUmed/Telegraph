<?php

namespace App\Entities;

use DateTimeImmutable;
use Exception;
use RuntimeException;

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
     * @throws Exception
     */
    public function setText(string $text): void
    {
        if (mb_strlen($text) < 1 || mb_strlen($text) > 500 ){
            throw new Exception("Длина текста должна быть от 1 до 500 символов");
        }
        $this->text = $text;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        if (!preg_match('/^[a-zA-Z0-9-_]+\z/', $slug)) {
            throw new RuntimeException("Недопустимые символы");
        }
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
        if (mb_strlen($author) > 20) {
            throw new RuntimeException("Слишком много символов");
        }
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

    /**
     * @throws Exception
     */
    public function __set(string $name, string|DateTimeImmutable $value): void
    {
        $method = 'set' . ucfirst($name);
        if (!method_exists($this, $method)) {
            throw new Exception("Такой метод не существует");
        }
        $this->$method($value);

        if ($name == 'text') {
            $this->storeText();
        }
    }

    /**
     * @throws Exception
     */

    public function __get(string $name): string|DateTimeImmutable
    {
        $method = 'get' . ucfirst($name);
        if ($name == 'text') {
            return $this->loadText();
        } elseif (method_exists($this, $method)) {
            return $this->$method();
        } elseif (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new Exception('Такое свойство не существует');
        }
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