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

    public function connectEvents($events, $teamId)
    {
        $events = array_unique(array_filter($events));
        // DELETE
        $this->database->table('event_team')->where('team_id', $teamId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($events as $event)
        {
            $data[] = [
                "event_id" => $event,
                "team_id" => $teamId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("event_team")->insert($data);
        }
        return true;
    }

    public function connectMenus($menus, $teamId)
    {
        $menus = array_unique(array_filter($menus));
        // DELETE
        $this->database->table('menu_team')->where('team_id', $teamId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($menus as $menu)
        {
            $data[] = [
                "menu_id" => $menu,
                "team_id" => $teamId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("menu_team")->insert($data);
        }
        return true;
    }

    public function connectNotes($notes, $teamId)
    {
        $notes = array_unique(array_filter($notes));
        // DELETE
        $this->database->table('note_team')->where('team_id', $teamId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($notes as $note)
        {
            $data[] = [
                "note_id" => $note,
                "team_id" => $teamId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("note_team")->insert($data);
        }
        return true;
    }

    public function connectPages($pages, $teamId)
    {
        $pages = array_unique(array_filter($pages));
        // DELETE
        $this->database->table('page_team')->where('team_id', $teamId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($pages as $page)
        {
            $data[] = [
                "page_id" => $page,
                "team_id" => $teamId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("page_team")->insert($data);
        }
        return true;
    }

    public function connectProjects($projects, $teamId)
    {
        $projects = array_unique(array_filter($projects));
        // DELETE
        $this->database->table('project_team')->where('team_id', $teamId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($projects as $project)
        {
            $data[] = [
                "project_id" => $project,
                "team_id" => $teamId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("project_team")->insert($data);
        }
        return true;
    }

    public function connectTodos($todos, $teamId)
    {
        $todos = array_unique(array_filter($todos));
        // DELETE
        $this->database->table('todo_team')->where('team_id', $teamId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($todos as $todo)
        {
            $data[] = [
                "todo_id" => $todo,
                "team_id" => $teamId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("todo_team")->insert($data);
        }
        return true;
    }

    public function connectUsers($users, $teamId)
    {
        $users = array_unique(array_filter($users));
        // DELETE
        $this->database->table('team_user')->where('team_id', $teamId)->delete();
        // INSERT NEW
        $data = [];
        foreach ($users as $user)
        {
            $data[] = [
                "user_id" => $user,
                "team_id" => $teamId,
            ];
        }
        if (!empty($data))
        {
            $this->database->table("team_user")->insert($data);
        }
        return true;
    }
    // CUD METHODS

    public function create($name, $image)
    {
        $fileId = $this->fileCRUDManager->createFromBase64($image, "", "/images/teams/");
        $fileId = (!$fileId) ? null : $fileId;

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

    public function update($id, $post)
    {
        if (isset($post['image']))
        {
            $fileId = $this->fileCRUDManager->createFromBase64($post['image'], "", "/images/teams/");
            $fileId = (!$fileId) ? null : $fileId;
            $post['file_id']=$fileId;
        }
        else
            $fileId=null;
        
        if(isset($post['events']))
            $this->connectEvents($post['events'], $id);
        if(isset($post['menus']))
            $this->connectMenus($post['menus'], $id);
        if(isset($post['notes']))
            $this->connectNotes($post['notes'], $id);
        if(isset($post['pages']))
            $this->connectPages($post['pages'], $id);
        if(isset($post['project']))
            $this->connectProjects($post['project'], $id);
        if(isset($post['users']))
            $this->connectUsers($post['users'], $id);
        if(isset($post['todos']))
            $this->connectTodos($post['todos'], $id);
        
        unset($post['id']);
        unset($post['image']);
        
        unset($post['events']);
        unset($post['menus']);
        unset($post['notes']);
        unset($post['pages']);
        unset($post['project']);
        unset($post['users']);
        unset($post['todos']);
            
        return $this->database->table('team')->where('id', $id)->update($post);
    }

}
