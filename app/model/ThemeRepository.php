<?php

class ThemeRepository extends Repository
{
    public function getByHost($host)
    {
        $where = array(
            'host' => $host,
            'active' => 1,
        );

        return $this->findBy($where)->fetch();
    }

    public function themeInit($url)
    {
        $theme = $this->getByHost($url->host);

        //ak nemam host v databaze, zobrazim udrzbarsku stranku
        if(!$theme) { require __DIR__ . '/../../.maintenance.php'; }

        define('THEME_FOLDER', $theme->theme_folder);
        define('THEME_ID', $theme->id);
    }
}