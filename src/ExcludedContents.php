<?php namespace Buonzz\Scalp;

class ExcludedContents{
	
	public static function get(){
		return array('..', '.', 'index.php', '.htaccess', 
					 '.DS_Store', '.git', 
					 'composer.lock', 'vendor', 
					 'npm_modules', '.bash_profile', '.ssh', 
					 '.composer', '.gitkeep', '.gitignore');
	}
}