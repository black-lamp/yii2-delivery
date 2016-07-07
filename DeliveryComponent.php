<?php
namespace bl\delivery;

use bl\delivery\deamonApplication\Application;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\mail\MessageInterface;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

/**
 * config
 * ```php
 *
 * 'delivery' => [
 *  'class' => 'bl\delivery\DeliveryComponent',
 *  'defaultEmail' => 'support@example.com',
 *  'mailConfig' => [
 *      'class' => 'yii\swiftmailer\Mailer',
 *  ],
 * ]
 * ```
 * usage
 * ```php
 * \Yii::$app->delivery->send([
 *  'from' => 'admin@example.com',
 *  'to' => 'user@example.com',
 *  'subject' => 'digest',
 *  'message' => 'Hello user',
 * ]);
 * //read documentation DeliveryComponent::send;
 * ```
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
class DeliveryComponent extends Component
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'bl\delivery\controllers';

    /*Default value: \Yii::$app->params['supportEmail'] */
    public $defaultEmail;

    /** @var array mailer config */
    public $mail;

    /** @var  yii\swiftmailer\Mailer */
    private $mailer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::setAlias('@delivery', __DIR__);
        $this->defaultEmail = \Yii::$app->params['supportEmail'];
        $this->mail = [
            'class' => 'yii\swiftmailer\Mailer',
        ];
    }


    /**
     * requred params ['from', 'to', 'subject'];
     *
     * ```php
     * // example 1
     * [
     *  'from' => 'support@example.com',
     *  'to' => ['user1@example.com', 'user2@example.com' => 'user2', 'user3@example.com'],
     *  'subject' => 'digest',
     *  'isText' => true, // default false
     *  'message' => 'hello world',
     * ]
     * // example 2
     * [
     *  //propert 'from' seting from component config
     *  'to' => ['user1@example.com', 'user2@example.com' => 'user2', 'user3@example.com'],
     *  'subject' => 'digest',
     *  'view' => '@app/view/mail/info.php',
     *  'param' => ['userName' => 'BestUser', ...],
     * ]
     * ```
     * @param $param array
     * @throws \yii\base\InvalidConfigException
     */
    public function send($params)
    {
//        $pid = pcntl_fork();
//        if ($pid == -1) {

//        } elseif(!$pid) {
            /** @var string $from */
            $from = isset($params['from']) ? $params['from'] : $this->defaultEmail;

            if (empty($from)) {
                throw new InvalidParamException('Parameters \'from\' or \'defaultEmail\' mast be set');
            }

            /** @var array|string $to */
            $to = $params['to'];

            if (empty($to)) {
                throw new InvalidParamException('Parameters \'to\' mast be set');
            }

            if ($mailConfig = $this->mail) {
                $this->mailer = \Yii::createObject($mailConfig);
            }

            /** @var Mailer $mailer */
            $mailer = $this->mailer;

            /** @var MessageInterface $mail */
            $mail = $mailer;

            $subject = $params['subject'];

            if (empty($subject)) {
                throw new InvalidParamException('Parameters \'subject\' mast be set');
            }


            /* must be set param: message */
            if ($message = $params['message']) {
                $mail = $mailer->compose();
                if ($isText = $params['isText']) {
                    $mail->setTextBody($message);
                } else {
                    $mail->setHtmlBody($message);
                }

            } /* must be set params: view and/or param */
            elseif ($view = $params['view']) {
                $param = isset($params['param']) ? $params['param'] : [];
                $mailer->compose($view, $param);
            } else {
                throw new InvalidParamException('Required paraters not be set');
            }

            $mail = $mail->setTo($to)->setFrom($from);
            $mail = $mail->setSubject($subject);

            $mailer->send($mail);
//        }
//
//        /** @var Application $app */
//        $app = \Yii::createObject(Application::className());
//
//        $app->addComponent('mailer', $this->mail);
//
//        /** @var Mailer $mailer */
//        $mailer = $app->getApp()->mailer;
    }
}
