<?php

require_once ("common/page.php");
require_once ("common/a_content.php");
require_once ("common/db_helper.php");


class the_content extends \common\a_content {

    public function __construct(){
		
        $this->isProtected = false;
        parent::__construct();
        $this->check_user_data();
    }

    private string $raw_user = '';
    private string $raw_password = '';
	private array $message;

    private function identify(): bool{
        return \common\db_helper::get_instance()->user_exists($this->raw_user) &&
                \common\db_helper::get_instance()->auth_ok($this->raw_user, $this->raw_password);
    }
    private function check_user_data():void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['exit'])) {
			if (isset($_GET['exit']) && $_GET['exit']==1){
                unset($_SESSION['user']);
				unset($_SESSION['user_id']);
				unset($_SESSION['password_status']);
            } else {
                if (isset($_POST['login'])) 
                    $this->raw_user = htmlspecialchars($_POST['login']);
                    if (isset($_POST['password']))
                        $this->raw_password = htmlspecialchars($_POST['password']);
                    if ($this->identify()) {
                        $_SESSION['user'] = $this->raw_user;
						$_SESSION['user_id']=\common\db_helper::get_instance()->get_user_info($_SESSION['user'])['id'];
						$_SESSION['password_status'] = \common\db_helper::get_instance()->get_user_info($_SESSION['user'])['password_status'];
						$this->message = array("user_id"=> $_SESSION['user_id'], "code"=>200);
						$this->get_response($this->message);
                }
				else {
					$this->message = array("error"=>"unauthorized");
					$this->get_response($this->message);
				}
        }
    }
	}
	
private function get_response(array $data): void {
	header("Content-Type: application/json; charset=UTF-8");
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit();
}
    public function show_content(): void
    {
            ?>
            <div class="m-auto card p-2 bg-primary bg-gradient bg-opacity-25" style="width: 500px;">
            <form action="login.php" method="post">
                <div class="row p-2 mb-2">
                    <div class="col-2 align-self-center">
                        <label for="login" class="text-center">E-mail:</label>
                    </div>
                    <div class="col align-self-center">
                        <input class="form-control form-control-md" type="text" value="<?php print $this->raw_user;?>" placeholder="Введите логин" name="login" id="login">
                    </div>
                </div>
                <div class="row p-2 mb-2">
                    <div class="col-2 align-self-center">
                        <label for="password" class="text-center">Пароль:</label>
                    </div>
                    <div class="col align-self-center">
                        <input class="form-control form-control-md" type="password" value="<?php print $this->raw_password;?>" placeholder="Введите пароль" name="password" id="password">
                    </div>
                </div>
                <div class="row mb-2 mt-4">
                    <div class="col">
                        <input type="submit" value="Отправить" class="form-control-color btn btn-primary w-50">
                    </div>
                </div>
            </form>
            </div>
            <?php
        } 
    }

$content = new the_content();
new \common\page($content);
