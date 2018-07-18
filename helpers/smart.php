<?php
    /*
    | Author : Ata amini
    | Email  : ata.aminie@gmail.com
    | Date   : 2018-04-19
    | TIME   : 11:12 PM
    */

    use Modules\Users\Models\User;

    if (!function_exists('app')) {
        /**
         * @param null $make
         *
         * @return null|object|\Smart\Web\Application|\yii\web\Application
         */
        function app($make = null)
        {
            if (!$make) {
                return Yii::$app;
            } else {
                return Yii::$app->get($make, false);
            }
        }
    }

    if (!function_exists('homeUrl')) {
        /**
         * get home url
         *
         * @return mixed|string
         */
        function homeUrl()
        {
            return app()->request->baseUrl;
        }
    }

    if (!function_exists('trans')) {
        /**
         * yii translation wrapper
         *
         * @param       $category
         * @param       $message
         * @param array $params
         * @param null  $language
         *
         * @return string
         */
        function trans($category, $message, $params = [], $language = null)
        {
            return Yii::t($category, $message, $params, $language);
        }
    }


    if (!function_exists('smart_services')) {
        /**
         * @return mixed|\Smart\Engine\Services
         * @throws \yii\base\InvalidConfigException
         */
        function smart_services()
        {
            return app()->services;
        }
    }

    if (!function_exists('smart_hooks')) {
        /**
         * return smart hooks instance
         *
         * @return \Smart\Hooks\HooksService
         */
        function smart_hooks()
        {
            return smart_services()->hooksService;
        }
    }

    if (!function_exists('smart_flash')) {
        /**
         * flash messages
         *
         * @return \Smart\Flash\FlashNotifier
         */
        function smart_flash()
        {
            return smart_services()->flash;
        }
    }

    if (!function_exists('smart_input')) {
        /**
         * return input instance
         *
         * @return \Smart\Engine\Input
         */
        function smart_input()
        {
            return smart_services()->input;
        }
    }

    if (!function_exists('smart_view_render')) {
        /**
         * render view
         *
         * @param       $view
         * @param array $vars
         * @param null  $context
         *
         * @return bool|string
         * @throws \yii\base\InvalidConfigException
         */
        function smart_view_render($view, $vars = [], $context = null)
        {
            if (!app()->view) {
                return false;
            }
            return app()->view->render($view, $vars, $context);
        }
    }

    if (!function_exists('smart_view_extend')) {
        /**
         * extend views
         *
         * @param     $view
         * @param     $viewExtension
         * @param int $priority
         *
         * @return bool
         * @throws \yii\base\InvalidConfigException
         */
        function smart_view_extend($view, $viewExtension, $priority = 501)
        {
            if (!app()->view) {
                return false;
            }

            app()->view->extendView($view, $viewExtension, $priority);
        }
    }

    if (!function_exists('smart_depend_asset')) {
        /**
         * add an asset as other asset depends dynamically
         *
         * @param string $dependTo
         * @param string $asset
         *
         */
        function smart_depend_asset(string $dependTo, string $asset)
        {
            // register as dependency on asset registration
            smart_hooks()->register('register:bundle', $dependTo, function ($params, $ret) use ($asset) {
                if (is_array($ret))
                    $ret[] = $asset;
                return $ret;
            });
        }
    }


    if (!function_exists('smart_ajax')) {
        /**
         * smart ajax
         *
         * @return \Smart\Components\Ajax\Contracts\Ajax
         */
        function smart_ajax()
        {
            return app()->services->ajax;
        }
    }


    if (!function_exists('smart_is_guest')) {
        /**
         * return is user is guest
         *
         * @return bool
         */
        function smart_is_guest()
        {
            return app()->user->isGuest;
        }
    }

    if (!function_exists('smart_is_user_logged_in')) {
        /**
         * return is user logged in
         *
         * @return bool
         */
        function smart_is_user_logged_in()
        {
            return smart_is_guest() === false;
        }
    }

    if (!function_exists('smart_logged_in_user_entity')) {
        /**
         * return user entity
         *
         * @return \Modules\users\models\User
         */
        function smart_logged_in_user_entity()
        {
            return app()->user->identity;
        }
    }

    if (!function_exists('smart_logged_in_user_id')) {
        /**
         * return logged in user id
         *
         * @return int|string
         */
        function smart_logged_in_user_id()
        {
            return smart_logged_in_user_entity()->id;
        }
    }

    if (!function_exists('ensure_aliases')) {
        /**
         * ensure alias path
         *
         * @param null $item
         *
         * @return bool|null|string
         */
        function ensure_aliases($item = null)
        {
            if (false !== strpos($item, '@'))
                $item = Yii::getAlias($item, false);
            return $item;
        }
    }


    if (!function_exists('smart_message')) {
        /**
         * send message
         *
         * @param $to send to
         * @param $title message title
         * @param $message message content
         * @param $from send from
         * @param $params params
         *
         * @return bool
         */
        function smart_message($to, $title, $message, $from = null, $params = [])
        {
            if(!$to instanceof User){
                $to = User::findOne(['id'=>$to]);
            }
            if(is_null($from)){
                $from = User::findOne(['id'=>Yii::$app->user->id]);
            }
            \Yii::$app->services->notificationsService->notify($to, $title, $message, $params, ['message'], $from);
            return true;
        }
    }