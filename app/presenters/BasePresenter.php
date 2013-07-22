<?php


abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	protected function beforeRender()
	{
		$this->template->viewName = $this->view;
		$this->template->root = isset($_SERVER['SCRIPT_FILENAME']) ? realpath(dirname(dirname($_SERVER['SCRIPT_FILENAME']))) : NULL;

		$a = strrpos($this->name, ':');
		if ($a === FALSE) {
			$this->template->moduleName = '';
			$this->template->presenterName = $this->name;
		} else {
			$this->template->moduleName = substr($this->name, 0, $a + 1);
			$this->template->presenterName = substr($this->name, $a + 1);
		}
	}

    //pretazenie Nette funkcie, pre nacitavanie templates z configu
    public function formatLayoutTemplateFiles()
    {
        if(defined('THEME_FOLDER')) {
            $name = $this->getName();
            $presenter = substr($name, strrpos(':' . $name, ':'));
            $layout = $this->layout ? $this->layout : 'layout';
            $dir = dirname($this->getReflection()->getFileName());
            $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
            $list = array(
                "$dir/templates/" . THEME_FOLDER . "/$presenter/@$layout.latte",
                "$dir/templates/" . THEME_FOLDER . "/$presenter.@$layout.latte",
                "$dir/templates/" . THEME_FOLDER . "/$presenter/@$layout.phtml",
                "$dir/templates/" . THEME_FOLDER . "/$presenter.@$layout.phtml",
            );
            do {
                $list[] = "$dir/templates/" . THEME_FOLDER . "/@$layout.latte";
                $list[] = "$dir/templates/" . THEME_FOLDER . "/@$layout.phtml";
                $dir = dirname($dir);
            } while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));
            return $list;
        } else {
            return parent::formatLayoutTemplateFiles();
        }
    }

    //pretazenie Nette funkcie, pre nacitavanie templates z configu
    public function formatTemplateFiles()
    {
        if(defined('THEME_FOLDER')) {
            $name = $this->getName();
            $presenter = substr($name, strrpos(':' . $name, ':'));
            $dir = dirname($this->getReflection()->getFileName());
            $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
            return array(
                "$dir/templates/" . THEME_FOLDER . "/$presenter/$this->view.latte",
                "$dir/templates/" . THEME_FOLDER . "/$presenter.$this->view.latte",
                "$dir/templates/" . THEME_FOLDER . "/$presenter/$this->view.phtml",
                "$dir/templates/" . THEME_FOLDER . "/$presenter.$this->view.phtml",
            );
        } else {
            return parent::formatTemplateFiles();
        }
    }

}
