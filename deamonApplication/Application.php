<?php
namespace bl\delivery\deamonApplication;

use yii\base\Component;

/**
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 *
 */
class Application extends Component
{
    /**
     * App config
     * @var array|string|null
     */
    public $config;
    /**
     * @var \yii\console\Application
     */
    private $app;

    public function init()
    {
        parent::init();

        $config = $this->config;
        if (is_array($config)) {
            $this->app = \Yii::createObject($this->config);
        } elseif (is_string($config)) {
            $this->app = \Yii::createObject(require($config));
        } else {
            $this->app = \Yii::createObject(require(\Yii::getAlias('@delivery/deamonApplication/config/main.php')));
        }
    }


    /**
     * @return \yii\console\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    public function addComponent($id, $defination)
    {
        $this->app->set($id, $defination);

    }
}