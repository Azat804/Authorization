<?php

namespace common;

abstract class a_content
{
	protected bool $isProtected = true;
	public function __construct(){
        session_start();
		if ($this->isProtected && !isset($_SESSION['user'])){
            header("Location: login.php");
        }
    }
	
    abstract function show_content(): void;
	
	
}