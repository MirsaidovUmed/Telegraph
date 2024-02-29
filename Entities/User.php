<?php

namespace App\Entities;

abstract class User
{
    protected int $id;
    protected string $name;
    protected string $role;

    abstract function getTextsToEdit();
}