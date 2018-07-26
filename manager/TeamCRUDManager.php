<?php

namespace Absolute\Module\Team\Manager;

use Nette\Database\Context;
use Absolute\Core\Manager\BaseCRUDManager;
use Absolute\Module\File\Manager\FileCRUDManager;

class TeamCRUDManager extends BaseCRUDManager
{

    private $fileCRUDManager;

    public function __construct(Context $database, FileCRUDManager $fileCRUDManager)
    {
        parent::__construct($database);
        $this->fileCRUDManager = $fileCRUDManager;
    }

    // OTHER METHODS
    // CONNECT METHODS
    // CUD METHODS

    public function create($name, $image)
    {
        if ($image instanceof \Nette\Http\FileUpload && $image->getName())
        {
            $fileId = $this->fileCRUDManager->createFromUpload($image, $image->getSanitizedName(), "/images/teams/");
            $fileId = (!$fileId) ? null : $fileId;
        }
        else
        {
            $fileId = null;
        }
        $result = $this->database->table('team')->insert(array(
            'name' => $name,
            'created' => new \DateTime(),
            'file_id' => $fileId,
        ));
        return $result;
    }

    public function delete($id)
    {
        $db = $this->database->table('team')->get($id);
        if (!$db)
        {
            return false;
        }
        if ($db->file_id)
        {
            $this->fileCRUDManager->delete($db->file_id);
        }
        $this->database->table('event_team')->where('team_id', $id)->delete();
        $this->database->table('menu_team')->where('team_id', $id)->delete();
        $this->database->table('note_team')->where('team_id', $id)->delete();
        $this->database->table('page_team')->where('team_id', $id)->delete();
        $this->database->table('project_team')->where('team_id', $id)->delete();
        $this->database->table('team_user')->where('team_id', $id)->delete();
        $this->database->table('todo_team')->where('team_id', $id)->delete();
        return $this->database->table('team')->where('id', $id)->delete();
    }

    public function update($id, $name, $image)
    {
        $db = $this->database->table('team')->get($id);
        if (!$db)
        {
            return false;
        }
        if ($image instanceof \Nette\Http\FileUpload && $image->getName())
        {
            $fileId = $this->fileCRUDManager->createFromUpload($image, $image->getSanitizedName(), "/images/teams/");
            $fileId = (!$fileId) ? null : $fileId;
            if ($db->file_id)
            {
                $this->fileCRUDManager->delete($db->file_id);
            }
        }
        else
        {
            $fileId = $db->file_id;
        }
        return $this->database->table('team')->where('id', $id)->update(array(
                    'name' => $name,
                    'file_id' => $fileId,
        ));
    }

}
