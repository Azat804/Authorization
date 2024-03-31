<?php

namespace common;

require_once ("a_content.php");
require_once ("json_loader.php");

class page
{
    private a_content $content;
    private array $pages;
    private string $pages_file = "data/pages.json";

    public function __construct(a_content $content){
        $this->content = $content;
        $this->pages = json_loader::get_full_info($this->pages_file);
        $this->create_headers();
        $this->create_body();
        $this->finish_page();
    }

    private function create_headers(): void
    {
        ?>
        <!DOCTYPE HTML>
        <html lang="ru"><head>
            <?php
			
			
			?>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <link href="css/bootstrap.min.css" rel="stylesheet">
			<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rock+Salt&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/main.css">
            <script src="js/bootstrap.bundle.min.js"></script>
			
			
			<?php
			$pi = $this->get_current_page_info();
			print("<title>{$pi['name']}</title>")
			
			?>
        </head><body>
        <?php
    }

    private function create_body(): void
    {
        $this->create_body_head();
        print ('<div class="container-fluid mx-0 my-0 g-0 w-100 text-center ">');
        print ('<div class="row align-items-start w-100 mx-0 g-1">');
		print('<div class=" col-xl-12 w-100">');
        $this->content->show_content();
		print ('</div>');
		print ('</div>');
        print ('</div>');
        $this->create_footer();
    }

    private function finish_page(): void
    { ?>
	<script src="js/script.js"></script>
	<?php
        print("</body></html>");
    }

    private function create_body_head(): void
    {
        ?>
		
        <nav class="navbar navbar-expand-lg bg-body-tertiary " >
  <div class="container-fluid w-100 g-0 mx-0">
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
	
  <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
  
  <?php
  
  print ('<ul class="navbar-nav fs-5  text-center">');
  $pi = $this->get_current_page_info();
        foreach ($this->pages as $page){
        if (!(strcmp($page['alias'],'4')===0 ||  strcmp($page['alias'],'5')===0)){
            if (strcmp($pi['uri'], $page['uri']) === 0){
                print ("<li class='nav-item text-start'><span class='nav-link active' aria-current='page'>{$page['header']}</span></li>");
				
            } else {
				print('<li class="nav-item text-start">');
                print ("<a href='{$page['uri']}' class='nav-link'>{$page['header']}</a>");
				print('</li>');
            }
		}
		
        }
		print('</ul>');
		
;			if (isset($_SESSION['user']) && isset($_SESSION['user_id']) && isset($_SESSION['password_status'])) {
			print("<div class='text-primary fs-5  pe-3 ps-5  text-center'>Здравствуйте. Ваш user_id: {$_SESSION['user_id']}, Password_status: {$_SESSION['password_status']}</div>");
			print ("<a href='{$this->pages[1]['uri']}?exit=1' class='btn btn-primary  me-1 fs-5  text-center'  role='button' id ='btn_sign_out'>Выйти</a>");
			}
			else {
				print ("<a href='{$this->pages[1]['uri']}' class='btn btn-primary  fs-5  me-1 text-center' role='button' id='btn_sign_in'>{$this->pages[1]['header']}</a>");
			}
			print ("<a href='{$this->pages[2]['uri']}' class='btn btn-primary   fs-5   text-center me-1' role='button' id='btn_reg'>{$this->pages[2]['header']}</a>");
	  
	  
	  ?>
	  </div>
      
  </div>
</nav>
        <?php
    }

    private function create_menu(): void
    {
        print ('<ul class="list-unstyled mb-1 mt-1 text-start">');
        foreach ($this->pages as $page){
            
            $pi = $this->get_current_page_info();
            
            if (strcmp($pi['uri'], $page['uri']) === 0){
                print ("<li class='d-inline fw-bold mb-1'>{$page['header']}</li>");
            } else {
				print('<li class="mb-1">');
                print ("<a href='{$page['uri']}' class='link-dark link-offset-2-hover link-underline-opacity-0 link-underline-opacity-100-hover'>{$page['header']}</a>");
				print('</li>');
            }
        }
		print('</ul>');
        
    }

    private function create_footer(): void
    {
        print ('<div class="row w-100 gap-0 g-0 mx-0 mb-0 d-md-flex text-center">');
        print ('<div class="col-12 text-secondary bg-body-tertiary border border-tertiary  text-end py-4 px-2" style="height:100px;" >&copy Азат Халиуллин, 2024.</div>');
        print ('</div>');
    }

    private function get_current_page_info(): array | null
    {
		$file = preg_replace('/\\?.*/', '', basename($_SERVER['REQUEST_URI']));
        foreach ($this->pages as $page){
            if (strcmp($file, $page['uri']) === 0 || isset($page['alias']) && strcmp($file, $page['alias']) === 0){
                return $page;
            }
        }
        return null;
    }

}