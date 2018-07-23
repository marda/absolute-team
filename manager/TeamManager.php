<?php

namespace Absolute\Module\Team\Manager;

use Nette\Database\Context;
use Absolute\Core\Manager\BaseManager;
use Absolute\Module\Team\Entity\Team;
use Absolute\Module\File\Manager\FileManager;

class TeamManager extends BaseManager
{

    public function __construct(  Context $database, FileManager $fileManager)
    {
        parent::__construct($database);
        $this->fileManager = $fileManager;
    }

    /* DB TO ENTITY */

    public function _getTeam($db)
    {
        if ($db == false) {
            return false;
        }
        $object = new Team($db->id, $db->name, $db->created);
        if ($db->ref('file')) {
            $object->setImage($this->fileManager->_getFile($db->ref('file')));
        }
        return $object;
    }

    /* INTERNAL/EXTERNAL INTERFACE */

    public function _getById($id)
    {
        $resultDb = $this->database->table('team')->get($id);
        return $this->_getTeam($resultDb);
    }

    private function _getList()
    {
        $ret = array();
        $resultDb = $this->database->table('team');
        foreach ($resultDb as $db) {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getListWithUsers()
    {
        $ret = array();
        $resultDb = $this->database->table('team');
        foreach ($resultDb as $db) {
            $object = $this->_getTeam($db);
            foreach ($db->related('team_user') as $userDb) {
                $user = $this->_getUser($userDb->user);
                if ($user) {
                    $object->addUser($user);
                }
            }
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getSearch($search)
    {
        $ret = array();
        $resultDb = $this->database->table('team')->where('name REGEXP ?', $search);
        foreach ($resultDb as $db) {
            $object = $this->_getTeam($db);
            $ret[] = $object;
        }
        return $ret;
    }

    /* EXTERNAL METHOD */

    public function getById($id)
    {
        return $this->_getById($id);
    }

    public function getList()
    {
        return $this->_getList();
    }

    public function getListWithUsers()
    {
        return $this->_getListWithUsers();
    }

    public function getSearch($search)
    {
        return $this->_getSearch($search);
    }

}
