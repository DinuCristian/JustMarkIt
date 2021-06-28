<?php

require_once('BaseModel.php');

class RoleModel extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'role';
        $this->pk = 'id';
    }

    function getRoles()
    {
        $query = "SELECT * 
                    FROM $this->table";
        $stmt = $this->connection->query($query);

        return $stmt->fetchAll();
    }

    function getRole($roleId)
    {
        $query = "SELECT role
                    FROM role
                    WHERE role.id = :roleId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':roleId', $roleId);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }
}