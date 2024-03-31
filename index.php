<?php
require_once ("common/page.php");
require_once ("common/a_content.php");
require_once ("common/db_helper.php");

class index extends \common\a_content {
	public function __construct()
    {
        $this->isProtected = false;
        parent::__construct();
    }
	
    public function show_content(): void {
        print('<div class="alert alert-dark fs-4" role="alert">Упрощенный сервис с регистрацией и авторизацией</div>');			
    }
}

$content = new index();
new \common\page($content);