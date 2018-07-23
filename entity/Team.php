<?php

namespace Absolute\Module\Team\Entity;

use Absolute\Core\Entity\BaseEntity;

class Team extends BaseEntity
{

    private $id;
    private $name;
    private $created;
    private $users = [];
    private $image = null;

    public function __construct($id, $name, $created)
    {
        $this->id = $id;
        $this->name = $name;
        $this->created = $created;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function getImage()
    {
        return $this->image;
    }

    // SETTERS

    public function setImage($image)
    {
        $this->image = $image;
    }

    // ADDERS

    public function addUser($user)
    {
        $this->users[] = $user;
    }

    // OTHER METHODS

    public function toSelectJson()
    {
        return array(
            "id" => $this->id,
            "text" => $this->name,
        );
    }

    public function toJson()
    {
        return array(
            "id" => $this->id,
            "name" => $this->name,
            "imageUrl" => $this->image->getPath()
        );
    }

    public function toJsonString()
    {
        return json_encode(array(
            "id" => $this->id,
            "name" => $this->name,
            "users" => array_map(function($user) {
                    return $user->toJson();
                }, $this->users),
        ));
    }

}
