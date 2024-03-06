<?php

namespace App\Entities;

use App\Entities\Storage;

require_once 'Storage.php';

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
