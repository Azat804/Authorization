<?php

require_once ("common/page.php");
require_once ("common/a_content.php");
require_once ("common/db_helper.php");
enum error_type{
    case ok;
    case pass_defferent;
    case pass_incorrect_content;
    case login_exists;
    case login_incorrect_content;
    case reg_error;
}

class the_content extends \common\a_content {

    private string $raw_login = '';
    private string $raw_password = '';
    private string $raw_password2 = '';
	private string $current_password_status = '';
    private array $check_res;

    public function __construct(){
        $this->isProtected = false;
        parent::__construct();
        $this->check_res = $this->check_user_data();
        if (count($this->check_res) === 1 && $this->check_res[0] === error_type::ok){
            $this->do_reg();
        }
    }

    private function do_reg(){
        if (!\common\db_helper::get_instance()->add_user(
            $this->raw_login,
            password_hash($this->raw_password, PASSWORD_DEFAULT), 
            $this->current_password_status,
        )) $this->check_res[] = error_type::reg_error;
        else {
            $_SESSION['user'] = $this->raw_login;
			$_SESSION['user_id']=\common\db_helper::get_instance()->get_user_info($_SESSION['user'])['id'];
			$_SESSION['password_status'] = \common\db_helper::get_instance()->get_user_info($_SESSION['user'])['password_status'];
            header("Location: index.php");
        }
    }

    private function is_correct_content_login(string $s):bool{
		return preg_match("/^([-a-z0-9!#$%&'*+\=?^_`{|}~]+(\.[-a-z0-9!#$%&'*+\=?^_`{|}~]+)*@([a-z0-9]([-a-z0-9]{0,61}[a-z0-9])?\.)(aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|[a-z][a-z]))$/u",$s)===1;
    }
	
	private function is_correct_content_pass(string $s):bool{
		if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/u',$s)===1) {
			$this->current_password_status = 'perfect';
			return true;
		}
else if (preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/u',$s)===1){
	$this->current_password_status = 'good';
	return true;
}
else {
	return false;
}	
    }

    private function check_user_data():array{
        $res = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->raw_login = $login = (isset($_POST['login']))?htmlspecialchars($_POST['login']):"";
            $this->raw_password = $pass =  (isset($_POST['password']))?htmlspecialchars($_POST['password']):"";
            $this->raw_password2 = $pass2 = (isset($_POST['password2']))?htmlspecialchars($_POST['password2']):"";
            if (!$this -> is_correct_content_login($login)){
                $res[] = error_type::login_incorrect_content;
            }
            if (\common\db_helper::get_instance()->user_exists($login)){
                $res[] = error_type::login_exists;
            }
            if ($pass!==$pass2){
                $res[] = error_type::pass_defferent;
            }
            if (!$this -> is_correct_content_pass($pass)){
                $res[] = error_type::pass_incorrect_content;
            }
            if (count($res) == 0) {
                $res[] = error_type::ok;
            }
        }
        return $res;
    }

    private function show_error_text(string $msg){
        ?>
        <div class="alert alert-danger fw-bold text-center">
            <?php print $msg;?>
        </div>
        <?php
    }

    private function show_error(error_type $err){
        $msg = match ($err){
            error_type::login_incorrect_content => 'Неверный формат E-mail.',
            error_type::login_exists => 'Пользователь с таким E-mail уже существует. Придумайте другой E-mail.',
            error_type::pass_defferent => 'Введенные пароли не совпадают.',
            error_type::pass_incorrect_content => 'Ошибка weak_password. Пароль должен содержать как минимум 8 символов. Используйте буквы латинского алфавита в нижнем и верхнем регистре, цифры и знаки "@", "$", "!", "%", "*", "#","?","&".',
            error_type::reg_error => 'Не удалось зарегистрировать пользователя. ;(',
            default => ''
        };
        $this->show_error_text($msg);
    }

    public function show_content(): void
    {
        foreach ($this->check_res as $error){
            if ($error === error_type::ok) continue;
            $this->show_error($error);
        }
        ?>
        <div class="m-auto card p-2 bg-primary bg-gradient bg-opacity-25" style="width: 500px;">
            <form action="reg.php" method="post">
                <div class="row p-2 mb-2">

                    <div class="col-3 align-self-center">
                        <label for="login" class="text-center">E-mail:</label>
                    </div>
                    <div class="col align-self-center">
                        <input class="form-control form-control-md" type="text" value="<?php print $this->raw_login;?>" placeholder="Введите логин" name="login" id="login">
                    </div>
                </div>

                <div class="row p-2 mb-2">
                    <div class="col-3 align-self-center">
                        <label for="password" class="text-center">Пароль:</label>
                    </div>
                    <div class="col align-self-center">
                        <input class="form-control form-control-md" type="password" value="<?php print $this->raw_password;?>" placeholder="Введите пароль" name="password" id="password">
                    <i class="far fa-eye" id="togglePassword" style="margin-left: -30px; cursor: pointer;"></i>
					</div>
                </div>
                <div class="row p-2 mb-2">
                    <div class="col-3 align-self-center">
                        <label for="password2" class="text-center">Повторите пароль:</label>
                    </div>
                    <div class="col align-self-center">
                        <input class="form-control form-control-md" type="password" value="<?php print $this->raw_password2;?>" placeholder="Введите пароль повторно" name="password2" id="password2">
                    </div>
                </div>

                <div class="row mb-2 mt-4">
                    <div class="col">
                        <input type="submit" class="form-control-color btn btn-primary w-50">
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

$content = new the_content();
new \common\page($content);
