<?php

namespace common;

use mysqli;

class db_helper
{
    private mysqli $ms;
    private static ?db_helper $db = null;
    private function __construct()
    {
        $this->ms = new mysqli("localhost", "root", "", "users", 3306);
    }

    public static function get_instance(): ?db_helper
    {
        if (self::$db === null)
            self::$db = new db_helper();
        return self::$db;
    }

    public function add_user(string $login, string $password_hash, string $password_status): bool{
        if (!isset($login) || mb_strlen(trim($login))==0){
            return false;
        }
        if (!$this->user_exists($login)){
            try {
                $this->ms->begin_transaction(name:"add_user");
                $stmt = $this->ms->prepare("INSERT INTO `users` (login, password, password_status) VALUES (?, ?, ?)");
                if ($stmt === false)
                    throw new \Exception("Ошибка подготовки запроса");
                if (!$stmt->bind_param("sss", $login, $password_hash, $password_status))
                    throw new \Exception("Ошибка связывания параметров");
                if (!$stmt->execute())
                    throw new \Exception("Ошибка выполнения запроса");
                $this->ms->commit(name:"add_user");
                return true;
            } catch (\Exception $e){
                $this->ms->rollback(name:"add_user");
                return false;
            }
        }
        return false;
    }

    public function user_exists(string $login): bool
    {
        if (!isset($login) || mb_strlen(trim($login))==0){
            return false;
        }
        $stmt = $this->ms->prepare("SELECT COUNT(login) FROM `users` WHERE `login`=?");
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_NUM);
        $res = $row[0];
        $result->close();
        $stmt->close();
        return $res > 0;
    }

    private function get_user_pass(string $user): string | null {
        $stmt = $this->ms->prepare("SELECT `password` FROM `users` WHERE `login`=?");
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $res = $row['password'];
        $result->close();
        $stmt->close();
        return $res;
    }

    public function auth_ok(string $user, string $pass): bool{
        if (!(mb_strlen($user) > 0 && mb_strlen($pass) > 0)) return false;
        if (!$this->user_exists($user)) return false;
        return password_verify($pass, $this->get_user_pass($user) ?? '');
    }
	
	public function get_user_info(string $login): array {
		$stmt = $this->ms->prepare("SELECT id, password_status FROM `users` WHERE login=?");
		$stmt->bind_param('s', $login);
		$stmt->execute();
        $result = $stmt->get_result();
		$user_info =$result->fetch_array(MYSQLI_ASSOC);
		$result->close();
        $stmt->close();
		return $user_info;
	}
}