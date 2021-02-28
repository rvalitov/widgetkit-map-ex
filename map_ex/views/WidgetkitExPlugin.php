<?php
/*
This is a helper class that is suitable for use with any type of plugin for Widgetkit 2:
1) Widgets
2) Content provider plugins

In order to use this class in your plugin you need to rename the namespace of this class according to the name of your plugin, for example, for MapEx widget this class should be declared in namespace "WidgetkitEx\MapEx".
If you need extra unique functions that are plugin-specific, then you should declare your own class that extends this class, see usage example for the WidgetkitExMapPlugin in the MapEx widget.

Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
*/

namespace WidgetkitEx\MapEx {

    /**
     * Class describes file or directory
     * Class WKDiskItem
     */
    class WKDiskItem
    {
        public $name;
        public $fullName;
        public $relativeName;
        public $is_writable;
        public $size;//Can be zero for directory
        public $hash;
        public $is_file;
        public $contents;//Other items inside the directory, empty if it's a file

        /**
         * @param string $fullName
         * @param string $relative_path relative path of the root item, used for filling the $relativeName fields
         * @param bool $gitHash if true, then SHA1 hash of Git style is calculated, else SHA1 of the file.
         * @see WKDiskItem
         */
        public function AnalyzeItem($fullName, $relative_path = "", $gitHash = true)
        {
            $this->fullName = $fullName;
            $this->name = pathinfo($fullName, PATHINFO_BASENAME);
            $this->is_file = !is_dir($this->fullName);
            $this->is_writable = is_writable($this->fullName);
            $this->relativeName = $this->name;
            if ($relative_path)
                $this->relativeName = $relative_path . $this->name;
            $this->contents = null;
            if ($this->is_file) {
                $this->size = filesize($this->fullName);
                if ($gitHash) {
                    $fc = file_get_contents($this->fullName);
                    if ($fc !== false)
                        $this->hash = sha1('blob ' . $this->size . "\0" . $fc);
                } else
                    $this->hash = sha1_file($this->fullName);
            } else {
                $result = array();
                $dirItems = scandir($fullName);
                foreach ($dirItems as $key => $value)
                    if (!in_array($value, array(".", ".."))) {
                        $item = new WKDiskItem();
                        $item->AnalyzeItem($fullName . DIRECTORY_SEPARATOR . $value, $this->relativeName . DIRECTORY_SEPARATOR);
                        array_push($result, $item);
                    }
                $this->contents = $result;
            }
        }

        /**
         * @param WKDiskItem $value
         * @return string
         */
        private static function printDiskItem($value)
        {
            if (is_object(!$value))
                return '';
            $list = '';
            if ($value->is_writable)
                $color = '<span class="uk-text-success"><span uk-icon="icon: check"></span> ';
            else
                $color = '<span class="uk-text-danger"><span uk-icon="icon: warning"></span> ';
            if ($value->contents) {
                //Folder
                $list .= '<li>' . $color . '<span uk-icon="icon: folder"></span> ' . htmlspecialchars($value->name) . '</span>';
                $list .= '<ul class="uk-list">';
                $list .= WKDiskItem::printStructureInt($value->contents, true);
                $list .= '</li></ul>';
            } else {
                //File
                $list .= '<li>' . $color . '<span uk-icon="icon: file"></span> ' . htmlspecialchars($value->name) . '</span></li>';
            }
            return $list;
        }

        /**
         * Makes a beautiful output of directory structure
         * @param WKDiskItem|WKDiskItem[] $array
         * @param bool $nested
         * @return string
         */
        private static function printStructureInt($array, $nested = false)
        {
            $list = '';
            if (!$nested)
                $list .= '<ul class="uk-list">';
            if (is_array($array))
                foreach ($array as $value)
                    $list .= WKDiskItem::printDiskItem($value);
            else
                $list .= WKDiskItem::printDiskItem($array);
            if (!$nested)
                $list .= '</ul>';
            return $list;
        }

        /**
         * Makes a beautiful output of directory structure
         * @return string
         */
        public function printStructure()
        {
            return WKDiskItem::printStructureInt($this);
        }

        /**
         * @return bool
         */
        public function hasWriteAccessProblems()
        {
            if (!$this->is_writable)
                return true;
            if (is_array($this->contents))
                foreach ($this->contents as $value)
                    if (!$value->is_writable)
                        return true;
            return false;
        }

        /**
         * @return array|false
         */
        public function toArrayItem()
        {
            if (!$this->is_file)
                return false;
            return array('name' => $this->relativeName, 'size' => $this->size, 'hash' => $this->hash);
        }

        /**
         * Returns all the information about files in a single array
         * @return array|array[]
         */
        public function toArray()
        {
            $l = array();
            $i = $this->toArrayItem();
            if (is_array($i))
                return array($i);
            if (is_array($this->contents))
                foreach ($this->contents as $value) {
                    $i = $value->toArray();
                    if (is_array($i))
                        $l = array_merge($l, $i);
                }
            return $l;
        }
    }

    class WidgetkitExPlugin
    {

        private $plugin_info;

        private $isWidget = false;
        private $isContentProvider = false;

        //Below are the versions of PHP and Widgetkit that are OK
        const minPHPVersion = '5.3';
        const stablePHPVersion = '5.6';
        const minWKVersion = '3.0.0';
        const stableWKVersion = '3.0.3';
        const minUIkitVersion = '3.0.0';

        //Unique id of the plugin, usually this id is used as HTML id
        private $id;

        //The 3 arrays below contain strings that will be used for console log (JS) output, see usage example in the MapEx widget.
        private $debug_info = array();
        private $debug_warning = array();
        private $debug_error = array();

        //true, if current CMS is Joomla
        private $isJoomla;

        //Version of CMS
        private $CMSVersion;

        private $CMS;

        //true or false if installation path is correct
        private $pathCorrect;

        //Use {wk} or uk prefix for CSS classes. Old Widgetkit uses uk prefix for UIkit, latest Widgetkit uses {wk}
        private $useWKPrefix;

        //Version of UIkit installed
        private $UIkitVersion;

        /**
         * WidgetkitExPlugin constructor.
         * @param $appWK
         * @param int $id
         */
        public function __construct($appWK, $id = 0)
        {
            $this->id = $id;

            $this->isJoomla = self::IsJoomlaInstalled();

            $this->plugin_info = $this->getWKPluginInfo($appWK);

            if ($this->isJoomla) {
                $this->CMSVersion = $this->getJoomlaVersion();
                $this->CMS = "Joomla";
            } else {
                $this->CMSVersion = $this->getWPVersion();
                $this->CMS = "WordPress";
            }

            $wk_version = $this->getWKVersion();
            $php_version = @phpversion();
            array_push($this->debug_info, 'Processing widget ' . $this->plugin_info['name'] . ' (version ' . $this->plugin_info['version'] . ') on ' . $this->CMS . ' ' . $this->CMSVersion . ' with Widgetkit ' . $wk_version . ' and PHP ' . $php_version . '(' . @php_sapi_name() . ')');
            if (version_compare(self::minPHPVersion, $php_version) > 0)
                array_push($this->debug_error, 'Your PHP is too old! Upgrade is strongly required! This widget may not work with your version of PHP.');
            else
                if (version_compare(self::stablePHPVersion, $php_version) > 0)
                    array_push($this->debug_warning, 'Your PHP is quite old. Although this widget can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.');

            if (version_compare(self::minWKVersion, $wk_version) > 0)
                array_push($this->debug_warning, "Your Widgetkit version is quite old. Although this widget may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit. Besides, you may experience some issues of missing options in the settings of this widget if you don't upgrade.");

            array_push($this->debug_info, 'Host: ' . @php_uname());
            $installPath = $this->plugin_info['path'];
            array_push($this->debug_info, 'Widget installation path: ' . $installPath);
            $this->pathCorrect = false;
            if ($this->isJoomla)
                if (preg_match_all('@.*' . preg_quote('/administrator/components/com_widgetkit/plugins/' . ($this->isWidget ? 'widgets' : 'content') . '/') . '.+@', $installPath)) {
                    array_push($this->debug_info, 'Installation path is correct');
                    $this->pathCorrect = true;
                } else
                    array_push($this->debug_error, 'Installation path is not correct, please fix it. Read more in the Wiki.');
            else
                if (preg_match_all('@.*' . preg_quote('/wp-content/plugins/widgetkit/plugins/' . ($this->isWidget ? 'widgets' : 'content') . '/') . '.+@', $installPath)) {
                    array_push($this->debug_info, 'Installation path is correct');
                    $this->pathCorrect = true;
                } else
                    array_push($this->debug_warning, 'Installation path is not correct, please fix it. Read more in the Wiki.');

            if ($this->isJoomla)
                array_push($this->debug_info, 'Detected CMS: Joomla');
            else
                array_push($this->debug_info, 'Detected CMS: WordPress');

            $this->useWKPrefix = false;
            $this->UIkitVersion = null;
            if ($this->pathCorrect) {
                $wkUIKit = $installPath . '/../../../vendor/assets/wkuikit';
                if ((file_exists($wkUIKit)) && (is_dir($wkUIKit))) {
                    $this->useWKPrefix = true;
                    $wkUIKit .= '/js/uikit.min.js';
                    $this->UIkitVersion = self::readUIKitVersion($wkUIKit);
                }
                if ($this->UIkitVersion == '') {
                    $wkUIKit = $installPath . '/../../../vendor/assets/uikit/js/uikit.min.js';
                    $this->UIkitVersion = self::readUIKitVersion($wkUIKit);
                }
            }
        }

        /**
         * Reads UIkit version from specified uikit.min.js file
         * @param string $filename
         * @return string|null
         */
        private static function readUIKitVersion($filename)
        {
            if ((!file_exists($filename)) || (!is_file($filename)) || (!is_readable($filename)))
                return null;

            $file_contents = file_get_contents($filename, false, null, 0, 30);
            if ($file_contents === false)
                return null;
            /* Example of version format:
            /*! UIkit 2.27.2 |
            */
            if ((preg_match('@' . preg_quote('/*!') . '\s+UIkit\s+(?<version>\d+\.\d+\.\d+)\s+\|@', $file_contents, $matches) == 1) && (isset($matches['version'])) && ($matches['version'] != ''))
                return $matches['version'];
            else
                return null;
        }

        /**
         * @param $appWK
         * @return string
         */
        public static function getCSSPrefix($appWK)
        {
            return $appWK['config']->get('theme.support') === 'noconflict' ? 'wk' : 'uk';
        }

        /**
         * @return string
         */
        public function getUIkitVersion()
        {
            return (!empty($this->UIkitVersion)) ? $this->UIkitVersion : '2.26.3';
        }

        /**
         * @param $widgetkit_user - parameter that must be set to $app['user'] upon call.
         * @param bool $firstName if true, then returns first name of the current user or empty string if the first name is unknown.
         * if false, then returns second name of the current user or empty string if the second name is unknown
         * @return string
         */
        private function extractWKUserName($widgetkit_user, $firstName = true)
        {
            //$name=trim($widgetkit_user->getName());
            //There is a bug in Widgetkit - it doesn't get the name of the user, so the code above is obsolete
            if (!$this->isCMSJoomla()) {
                //For Wordpress:
                $current_user = wp_get_current_user();
                if (!$current_user)
                    return "";
                if ($firstName)
                    if ($current_user->user_firstname)
                        return $current_user->user_firstname;
                    else
                        return $current_user->user_login;
                else
                    return $current_user->user_lastname;
            }
            //For Joomla:
            /** @noinspection PhpFullyQualifiedNameUsageInspection */
            $user = \JFactory::getUser($widgetkit_user->getId());
            if ($user)
                $name = $user->name;
            else
                return "";

            $split_name = explode(' ', $name);
            if ($firstName)
                return ((sizeof($split_name) > 0) ? $split_name[0] : $name);
            @array_shift($split_name);
            return ((sizeof($split_name) > 0) ? implode(' ', $split_name) : '');
        }

        /**
         * @return bool true if Joomla is installed
         */
        public function isCMSJoomla()
        {
            return $this->isJoomla;
        }

        /**
         * @return bool
         */
        public function isCMSWordPress()
        {
            return !$this->isJoomla;
        }

        /**
         * @noinspection PhpUnused
         * @return string returns CMS version
         */
        public function getCMSVersion()
        {
            return $this->CMSVersion;
        }

        /**
         * @return string returns CMS name (Joomla or WordPress)
         */
        public function getCMSName()
        {
            return $this->CMS;
        }

        /**
         * @return bool returns true, if the current CMS is Joomla
         */
        public static function IsJoomlaInstalled()
        {
            return ((class_exists('JURI')) && (method_exists('JURI', 'base')));
        }

        /**
         * @param string $url
         * @return bool returns true, if it's a valid accessible URL
         */
        public static function url_exists($url)
        {
            if (!$fp = curl_init($url)) return false;
            return true;
        }

        /**
         * @return string returns Joomla version or empty string if failed
         */
        public function getJoomlaVersion()
        {
            if ($this->isCMSJoomla()) {
                /** @noinspection PhpFullyQualifiedNameUsageInspection */
                $versionJoomla = new \JVersion;
                return $versionJoomla->getShortVersion();
            } else
                return "";
        }

        /**
         * @return string returns WordPress version or empty string if failed
         */
        public function getWPVersion()
        {
            if (!$this->isCMSWordPress())
                return "";
            $f = @file_get_contents($this->getRootDirectory() . '/wp-includes/version.php', false, null, 0, 1400);
            if (!$f)
                return "";

            $v = '';
            if (preg_match_all("@.*\\\$wp_version\s*=\s*'.+';@", $f, $matches))
                $v .= explode("'", $matches[0][0], 3)[1];
            return trim($v);
        }

        /**
         * @return string returns Widgetkit version or empty string if failed
         */
        public function getWKVersion()
        {
            $f = @file_get_contents($this->getWKDirectory() . '/config.php', false, null, 0, 1400);
            if ((!$f) || (!preg_match_all("@.*'version'\s+=>\s+'.+',@", $f, $matches)))
                return "";
            return explode("'", $matches[0][0], 5)[3];
        }

        /**
         * @param bool $htmlEncode
         * @return string[]
         */
        public function getInfo($htmlEncode = true)
        {
            if (!$htmlEncode)
                return $this->plugin_info;

            $result = array();
            foreach ($this->plugin_info as $key => $value)
                $result[$key] = htmlspecialchars($value);
            return $result;
        }

        /**
         * @noinspection PhpUnused
         * @return bool
         */
        public function isWidget()
        {
            return $this->isWidget;
        }

        /**
         * @noinspection PhpUnused
         * @return bool
         */
        public function isContentProvider()
        {
            return $this->isContentProvider;
        }

        /**
         * @noinspection PhpUnused
         * @return bool
         */
        public function isJoomla()
        {
            return $this->isJoomla;
        }

        public function getPluginDirectory()
        {
            return $this->plugin_info['path'];
        }

        public function getPluginURL()
        {
            return $this->plugin_info['url'];
        }

        public function getWebsiteRootURL()
        {
            return $this->plugin_info['root_url'];
        }

        public function getWKDirectory()
        {
            return $this->plugin_info['wk_path'];
        }

        public function getRootDirectory()
        {
            return $this->plugin_info['root'];
        }

        /**
         * Returns array with info about current plugin (no matter if it's a widget or a content provider). It works only for custom plugins that are created with updater.js file.
         * @param array $appWK The array contains following fields:
         * name            - the name of the plugin or empty string if unknown.
         * version        - the version of the plugin or empty string if unknown.
         * codename        - the name of the distro (codename) or empty string if unknown.
         * date            - the release date of the plugin or empty string if unknown.
         * logo            - the absolute URL of the logo of the plugin or empty string if unknown.
         * wiki            - the absolute URL of wiki (manual) for the plugin or empty string if unknown.
         * website        - the absolute URL of home website (homepage) for the plugin or empty string if unknown.
         * root_url        - the absolute URL of the current website
         * path            - directory on the server where the plugin is located
         * relative_path    - relative path to the plugin from the Widgetkit directory
         * wk_path        - directory on the server where the Widgetkit is installed
         * root            - directory on the server where the website is located
         * url            - absolute URL to the directory where the plugin is located
         * safe_name        - unique safe name of the plugin, which can be used in CSS, HTML and JavaScript
         * @return string[]
         */
        private function getWKPluginInfo($appWK)
        {
            $info = [
                'name' => '',
                'version' => '',
                'codename' => '',
                'date' => '',
                'logo' => '',
                'wiki' => '',
                'website' => '',
                'root_url' => '',
                'path' => '',
                'relative_path' => '',
                'wk_path' => '',
                'root' => '',
                'url' => '',
                'safe_name' => '',
            ];

            //We perform a sequential scan of parent directories of the current script to find the plugin install directory
            if ($this->isCMSJoomla()) {
                $widgetkit_dir_name = DIRECTORY_SEPARATOR . "administrator" . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_widgetkit";
                /** @noinspection PhpFullyQualifiedNameUsageInspection */
                $baseurl = \JURI::base();
            } else {
                $widgetkit_dir_name = DIRECTORY_SEPARATOR . "wp-content" . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR . "widgetkit";
                $baseurl = get_site_url();
            }
            if (($baseurl) && ($baseurl[strlen($baseurl) - 1] != '/'))
                $baseurl .= '/';
            $info['root_url'] = $baseurl;

            $needle = $widgetkit_dir_name . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR . "widgets" . DIRECTORY_SEPARATOR;
            $pos = strrpos(__DIR__, $needle);
            if (!$pos) {
                $needle = $widgetkit_dir_name . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR . "content" . DIRECTORY_SEPARATOR;
                $pos = strrpos(__DIR__, $needle);
            }
            if ($pos) {
                $info['root'] = substr(__DIR__, 0, $pos);
                $offset = $pos + strlen($needle);
                $pos2 = strpos(__DIR__, DIRECTORY_SEPARATOR, $offset);
                if (!$pos2)
                    $info['path'] = __DIR__;
                else
                    $info['path'] = substr(__DIR__, 0, $pos2);

                $pos = strrpos($info['path'], $widgetkit_dir_name);
                if ($pos)
                    $info['relative_path'] = substr($info['path'], $pos + strlen($widgetkit_dir_name));
            }
            if ($info['root']) {
                $info['wk_path'] = $info['root'] . $widgetkit_dir_name;
            }

            if ($info['path']) {
                $f = @file_get_contents($info['path'] . DIRECTORY_SEPARATOR . 'plugin.php', false, null, 0, 2400);
                if (($f) && (preg_match_all("@^\s*'config'\s*=>\s*array\s*\(.*$@m", $f, $matches, PREG_OFFSET_CAPTURE))) {
                    $offset = $matches[0][0][1];
                    if (preg_match_all("@^\s*'label'\s*=>\s*'.*$@m", $f, $matches, PREG_PATTERN_ORDER, $offset)) {
                        $info['name'] = explode("'", trim($matches[0][0]))[3];
                    }
                    if (preg_match_all("@^\s*'name'\s*=>\s*'.*$@m", $f, $matches, PREG_PATTERN_ORDER, $offset)) {
                        $info['codename'] = explode("'", trim($matches[0][0]))[3];
                    }
                    if (preg_match_all("@^\s*'plugin_version'\s*=>\s*'.*$@m", $f, $matches)) {
                        $info['version'] = explode("'", trim($matches[0][0]))[3];
                    }
                    if (preg_match_all("@^\s*'plugin_date'\s*=>\s*'.*$@m", $f, $matches)) {
                        $info['date'] = explode("'", trim($matches[0][0]))[3];
                    }
                    if (preg_match_all("@^\s*'plugin_logo'\s*=>\s*'.*$@m", $f, $matches)) {
                        $info['logo'] = explode("'", trim($matches[0][0]))[3];
                    }
                    if (preg_match_all("@^\s*'plugin_wiki'\s*=>\s*'.*$@m", $f, $matches)) {
                        $info['wiki'] = explode("'", trim($matches[0][0]))[3];
                    }
                    if (preg_match_all("@^\s*'plugin_website'\s*=>\s*'.*$@m", $f, $matches)) {
                        $info['website'] = explode("'", trim($matches[0][0]))[3];
                    }
                    if (preg_match_all("@^\s*'name'\s*=>\s*'.*$@m", $f, $matches)) {
                        $raw_name = explode("'", trim($matches[0][0]))[3];
                        $this->isWidget = (substr($raw_name, 0, 7) === "widget/");
                        $this->isContentProvider = (substr($raw_name, 0, 8) === "content/");
                    }
                }
                $url = $appWK['url']->to('widgetkit');
                if ($url) {
                    if ($url[strlen($url) - 1] != '/')
                        $info['url'] = $url;
                    else
                        $info['url'] = substr($url, 0, strlen($url) - 1);
                    $info['url'] .= $info['relative_path'];
                }
            }

            $info['safe_name'] = preg_replace('/[^A-Za-z_]/', '', $info['codename']);
            return $info;
        }

        /**
         * Prints information for the "About" section of the plugin
         * @param array $appWK parameter that must be set to $app upon call.
         */
        public function printAboutInfo($appWK)
        {
            $versionWK = htmlspecialchars((isset($appWK['version'])) ? $appWK['version'] : 'Unknown');
            $versionDB = htmlspecialchars((isset($appWK['db_version'])) ? $appWK['db_version'] : 'Unknown');
            $php_version = htmlspecialchars(@phpversion());
            if (version_compare(self::minPHPVersion, $php_version) > 0)
                $phpinfo = '<span data-uk-tooltip="\'cls\':\'uk-' . $this->plugin_info['safe_name'] . '-tooltip\'" class="uk-text-danger" style="margin-top: 5px;" title="{{ \'Your PHP is too old! Upgrade is strongly recommended! This plugin may not work with your version of PHP.\' |trans}}"><span uk-icon="icon: warning"></span> ' . $php_version . '</span>';
            else
                if (version_compare(self::stablePHPVersion, $php_version) > 0)
                    $phpinfo = '<span data-uk-tooltip="\'cls\':\'uk-' . $this->plugin_info['safe_name'] . '-tooltip\'" class="uk-text-warning" style="margin-top: 5px;" title="{{ \'Your PHP is quite old. Although this plugin can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.\' |trans}}"><span uk-icon="icon: warning"></span> ' . $php_version . '</span>';
                else
                    $phpinfo = '<span data-uk-tooltip="\'cls\':\'uk-' . $this->plugin_info['safe_name'] . '-tooltip\'" class="uk-text-success" style="margin-top: 5px;" title="{{ \'Your PHP version is OK.\' |trans}}"><span uk-icon="icon: check"></span> ' . $php_version . ' (' . @php_sapi_name() . ')</span>';

            if (version_compare(self::minWKVersion, $versionWK) > 0)
                $wkInfo = '<span data-uk-tooltip="\'cls\':\'uk-' . $this->plugin_info['safe_name'] . '-tooltip\'" class="uk-text-danger" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is too old. Upgrade is strongly recommended. Although this plugin may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit.\' |trans}}"><span uk-icon="icon: warning"></span> ' . $versionWK . '</span>';
            else {
                if (version_compare(self::stableWKVersion, $versionWK) > 0)
                    $wkInfo = '<span data-uk-tooltip="\'cls\':\'uk-' . $this->plugin_info['safe_name'] . '-tooltip\'" class="uk-text-warning" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is quite old. Although this plugin may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit.\' |trans}}"><span uk-icon="icon: warning"></span> ' . $versionWK . '</span>';
                else
                    $wkInfo = '<span data-uk-tooltip="\'cls\':\'uk-' . $this->plugin_info['safe_name'] . '-tooltip\'" class="uk-text-success" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is OK.\' |trans}}"><span uk-icon="icon: check"></span> ' . $versionWK . '</span>';
            }

            $cmsInfo = $this->CMS . ' ' . $this->CMSVersion;

            $item = new WKDiskItem();
            $item->AnalyzeItem($this->plugin_info['path']);
            //Making it beautiful:
            $accessList = $item->printStructure();
            if ($item->hasWriteAccessProblems())
                $accessInfo = '<span class="uk-text-danger"><span uk-icon="icon: warning"></span> ' . $appWK['translator']->trans('Failure') . '</span>';
            else
                $accessInfo = '<span class="uk-text-success"><span uk-icon="icon: check"></span> ' . $appWK['translator']->trans('Ok') . '</span>';
            $accessInfo .= '<a href="#write-check-' . $this->plugin_info['safe_name'] . '" data-uk-modal="{center:true}" class="uk-margin-small-left"><span uk-icon="icon: info"></span>/a>';

            $files = json_encode($item->toArray());

            if ($this->pathCorrect)
                $installPath = '<span class="uk-text-success" style="word-break:break-all"><span uk-icon="icon: check"></span> ' . $this->plugin_info['path'] . '</span>';
            else
                $installPath = '<span class="uk-text-danger" style="word-break:break-all"><span uk-icon="icon: warning"></span> ' . $this->plugin_info['path'] . '</span>';

            $YoothemeProCompatible = ($this->useWKPrefix) ? '<span class="uk-text-success"><span uk-icon="icon: check"></span> {{ "Yes" |trans}}</span>' : '<span class="uk-text-success"><span uk-icon="icon: check"></span> {{ "No" |trans}}</span>';

            if (!isset($this->plugin_info['safe_name'])) {
                echo <<< EOT
<div class="uk-alert uk-alert-danger"><span uk-icon="icon: warning"></span> {{ 'Failed to retrieve information' |trans}}</div>;
EOT;
                return;
            }

            $canVerify = true;
            $filesIntegrity = '<button class="uk-button uk-button-primary"';
            if (!$canVerify)
                $filesIntegrity .= ' disabled';
            else
                $filesIntegrity .= ' onclick="verifyFiles' . $this->plugin_info['safe_name'] . '()"';
            $filesIntegrity .= '>' . $appWK['translator']->trans('Verify files') . '</button>';

            $replacements = array(
                "__WK_SAFE_NAME__" => $this->plugin_info['safe_name'],
                "__WK_ACCESS_LIST__" => $accessList,
                "__WK_FILES__" => $files,
                "__WK_CMS_INFO__" => $cmsInfo,
                "__WK_INFO__" => $wkInfo,
                "__WK_DB_VERSION__" => $versionDB,
                "__WK_UIKIT_VERSION__" => $this->UIkitVersion,
                "__WK_YT_PRO_COMPATIBLE__" => $YoothemeProCompatible,
                "__WK_PHP_INFO__" => $phpinfo,
                "__WK_PATH__" => $installPath,
                "__WK_ACCESS_INFO__" => $accessInfo,
                "__WK_INTEGRITY__" => $filesIntegrity,
            );

            $html = @file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "about.html");
            foreach ($replacements as $key => $value) {
                $html = str_replace($key, $value, $html);
            }
            echo $html;
        }

        /**
         * Prints information for the "Newsletter" section of the plugin with subscribe button
         * @param array $appWK parameter that must be set to $app upon call.
         */
        public function printNewsletterInfo($appWK)
        {
            $firstName = htmlspecialchars($this->extractWKUserName($appWK['user']));
            $lastName = htmlspecialchars($this->extractWKUserName($appWK['user'], false));
            $email = htmlspecialchars($appWK['user']->getEmail());
            $cms = htmlspecialchars($this->getCMSName());
            $origin = htmlspecialchars($appWK['request']->getBaseUrl());
            $locale = htmlspecialchars($appWK['locale']);

            if (!isset($this->plugin_info['safe_name'])) {
                echo <<< EOT
<div class="uk-alert uk-alert-danger"><span uk-icon="icon: warning"></span> {{ 'Failed to retrieve information' |trans}}</div>;
EOT;
                return;
            }

            $replacements = array(
                "__WK_SAFE_NAME__" => $this->plugin_info['safe_name'],
                "__WK_FIRST_NAME__" => $firstName,
                "__WK_LAST_NAME__" => $lastName,
                "__WK_EMAIL__" => $email,
                "__WK_CMS__" => $cms,
                "__WK_ORIGIN__" => $origin,
                "__WK_NAME__" => $this->plugin_info['name'],
                "__WK_LOCALE__" => $locale,
            );

            $html = @file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "newsletter.html");
            foreach ($replacements as $key => $value) {
                $html = str_replace($key, addslashes($value), $html);
            }
            echo $html;
        }

        /**
         * Prints information for the "Donate" section of the plugin
         */
        public function printDonationInfo()
        {
            echo @file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "donation.html");
        }

        /**
         * Generates and returns Javascript code (without <script> tags) used for checking updates
         * @param array $appWK parameter that must be set to $app upon call.
         * @param array $settings array that contains info about the installed plugin. Meaning of the keys:
         * git - URL to the Git repository
         * api - URL to the Git Api
         * name - name of the plugin
         * version - version of the plugin
         * dist_name - dist name (codename)
         * date - build date of the plugin
         * logo - absolute URL to logo of the plugin
         * wiki - absolute URL to wiki of the plugin
         * website - absolute URL to website of the plugin
         * If some fields are missing, then this function tries to detect them
         * @return string
         */
        public function generateUpdaterJS($appWK, $settings = array())
        {
            if (!is_array($settings))
                $settings = array();
            if (!isset($settings['git']))
                $settings['git'] = 'https://github.com/rvalitov/';
            else
                $settings['git'] = htmlspecialchars($settings['git']);
            if (!isset($settings['api']))
                $settings['api'] = 'https://api.github.com/repos/rvalitov/';
            else
                $settings['api'] = htmlspecialchars($settings['api']);
            //Checking for minimum set of required fields:
            if (!isset($this->plugin_info['name']) || !isset($this->plugin_info['version']) || !isset($this->plugin_info['codename']) || empty($this->plugin_info['safe_name']))
                return '';
            if (!isset($settings['name']))
                $settings['name'] = $this->plugin_info['name'];
            else
                $settings['name'] = htmlspecialchars($settings['name']);
            if (!isset($settings['version']))
                $settings['version'] = $this->plugin_info['version'];
            else
                $settings['version'] = htmlspecialchars($settings['version']);
            if (!isset($settings['dist_name']))
                $settings['dist_name'] = 'widgetkit-' . str_replace('_', '-', $this->plugin_info['codename']);
            else
                $settings['dist_name'] = htmlspecialchars($settings['dist_name']);
            if (!isset($settings['date']))
                $settings['date'] = $this->plugin_info['date'];
            else
                $settings['date'] = htmlspecialchars($settings['date']);
            if (!isset($settings['logo']))
                if (!isset($this->plugin_info['logo']))
                    $settings['logo'] = 'https://raw.githubusercontent.com/wiki/rvalitov/' . $settings['dist_name'] . '/images/logo.jpg';
                else
                    $settings['logo'] = htmlspecialchars($this->plugin_info['logo']);
            else
                $settings['logo'] = htmlspecialchars($settings['logo']);
            if (!isset($settings['wiki']))
                $settings['wiki'] = $settings['git'] . $settings['dist_name'] . '/wiki';
            else
                $settings['wiki'] = htmlspecialchars($settings['wiki']);
            if (!isset($settings['website']))
                $settings['website'] = $settings['git'] . $settings['dist_name'];
            else
                $settings['website'] = htmlspecialchars($settings['website']);

            //For JS we must escape single quote character:
            $modal = addcslashes($this->generateUpdateInfoDialog($appWK, $settings), "'");

            $replacements = array(
                "__WK_SAFE_NAME__" => $this->plugin_info['safe_name'],
                "__WK_WAIT__" => $appWK['translator']->trans('Please, wait...'),
                "__WK_API__" => $settings['api'],
                "__WK_DIST_NAME__" => $settings['dist_name'],
                "__WK_VERSION__" => $settings['version'],
                "__WK_INFO_VERSION__" => $settings['version'],
                "__WK_FAILED_JSON_PARSE__" => $appWK['translator']->trans('Failed to parse JSON'),
                "__WK_FILE_ALTERED__" => $appWK['translator']->trans('File is altered'),
                "__WK_FILE_MISSING__" => $appWK['translator']->trans('File is missing'),
                "__WK_FILE__" => $appWK['translator']->trans('File'),
                "__WK_PROBLEM__" => $appWK['translator']->trans('Problem'),
                "__WK_NO_PROBLEMS__" => $appWK['translator']->trans('No problems detected'),
                "__WK_NO_INFO__" => $appWK['translator']->trans("Couldn't retrieve information about files of your release"),
                "__WK_NO_RESPONSE__" => $appWK['translator']->trans('Failed to get information from server'),
                "__WK_NO_RELEASE_INFO__" => $appWK['translator']->trans("Information about your release is not available. The files can't be verified."),
                "__WK_NAME__" => $settings['name'],
                "__WK_WEBSITE__" => $settings['website'],
                "__WK_LOGO__" => $settings['logo'],
                "__WK_DATE__" => $settings['date'],
                "__WK_WIKI_URL__" => $settings['wiki'],
                "__WK_CONFIG_FILE__" => $this->getPluginURL() . '/config.json',
                "__WK_NEW_RELEASE__" => $appWK['translator']->trans('New release of plugin %name% is available!', array('%name%' => $settings['name'])) . " " . $appWK['translator']->trans('Version'),
                "__WK_UPDATE_DETAILS__" => $appWK['translator']->trans('Update details'),
                "__WK_MIN_UIKIT_VERSION__" => self::minUIkitVersion,
                "__WK_MODAL_TEMPLATE__" => $modal,
                "__WK_OK__" => $appWK['translator']->trans('Ok'),
            );

            $js = @file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "../assets/widgetkit-ex-plugin.js");
            foreach ($replacements as $key => $value) {
                $js = str_replace($key, addslashes($value), $js);
            }
            return $js;
        }

        /**
         * Generates code of modal dialog that shows information about available update of the plugin
         * @param array $appWK parameter that must be set to $app upon call.
         * @param array $settings settings of the plugin
         * @return string
         */
        private function generateUpdateInfoDialog($appWK, $settings)
        {
            $replacements = array(
                "__WK_SAFE_NAME__" => $this->plugin_info['safe_name'],
                "__WK_TITLE__" => $appWK['translator']->trans('%name% plugin update details', array('%name%' => $this->plugin_info['name'])),
                "__WK_LOGO__" => $settings['logo'],
                "__WK_INFO_VERSION__" => $settings['version'],
                "__WK_DATE__" => $settings['date'],
                "__WK_WIKI_URL__" => $settings['wiki'],
                "__WK_INSTALLED__" => $appWK['translator']->trans("Installed"),
                "__WK_AVAILABLE__" => $appWK['translator']->trans("Available"),
                "__WK_VERSION_TEXT__" => $appWK['translator']->trans("Version"),
                "__WK_RELEASE_INFO__" => $appWK['translator']->trans("Release information"),
                "__WK_UPDATE_INFO__" => $appWK['translator']->trans("How to update"),
                "__WK_DOWNLOAD_PAGE__" => $appWK['translator']->trans("Download page"),
                "__WK_INSTRUCTIONS__" => $appWK['translator']->trans("Instructions"),
            );

            $js = @file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "update-info.html");
            foreach ($replacements as $key => $value) {
                $js = str_replace($key, addslashes($value), $js);
            }
            return str_replace(array("\r", "\n"), "", $js);
        }

        /**
         * This function is better than print_r because it controls the depth ($max_count), and doesn't cause reaching the memory limit error.
         * @param mixed $var
         * @param string $prefix
         * @param bool $init
         * @param int $count
         * @param int $max_count
         * @return mixed Returns a string that contains dumb of the $var.
         */
        public static function features_var_export($var, $prefix = '', $init = TRUE, $count = 0, $max_count = 5)
        {
            if ($count > $max_count) {
                // Recursion depth reached.
                return '...';
            }

            if (is_object($var)) {
                $output = method_exists($var, 'export') ? $var->export() : self::features_var_export((array)$var, '', FALSE, $count + 1);
            } else if (is_array($var)) {
                if (empty($var)) {
                    $output = 'array()';
                } else {
                    $output = "array(\n";
                    foreach ($var as $key => $value) {
                        // Using normal var_export on the key to ensure correct quoting.
                        $output .= "  " . var_export($key, TRUE) . " => " . self::features_var_export($value, '  ', FALSE, $count + 1) . ",\n";
                    }
                    $output .= ')';
                }
            } else if (is_bool($var)) {
                $output = $var ? 'TRUE' : 'FALSE';
            } else if (is_int($var)) {
                $output = intval($var);
            } else if (is_numeric($var)) {
                $floatVal = floatval($var);
                if (is_string($var) && ((string)$floatVal !== $var)) {
                    // Do not convert a string to a number if the string
                    // representation of that number is not identical to the
                    // original value.
                    $output = var_export($var, TRUE);
                } else {
                    $output = $floatVal;
                }
            } else if (is_string($var) && strpos($var, "\n") !== FALSE) {
                // Replace line breaks in strings with a token for replacement
                // at the very end. This protects whitespace in strings from
                // unintentional indentation.
                $var = str_replace("\n", "***BREAK***", $var);
                $output = var_export($var, TRUE);
            } else {
                $output = var_export($var, TRUE);
            }

            if ($prefix) {
                $output = str_replace("\n", "\n$prefix", $output);
            }

            if ($init) {
                $output = str_replace("***BREAK***", "\n", $output);
            }

            return $output;
        }

        /**
         * Returns an array with items from $array that have keys listed in $list.
         * @param array $array
         * @param string[] $list
         * @return array|string
         */
        public static function intersectArrayItems($array, $list)
        {
            if (!is_array($list))
                return '';
            $s = array();
            for ($i = 0; $i < sizeof($list); $i++)
                $s[$list[$i]] = (isset($array[$list[$i]]) ? ($array[$list[$i]]) : null);
            return $s;
        }

        /**
         * Reads global settings for this plugin
         * @return array
         */
        public function readGlobalSettings()
        {
            $path = $this->getPluginDirectory();
            if (!$path)
                return array();
            $name = $path . DIRECTORY_SEPARATOR . "config.json";
            if (!file_exists($name))
                return array();
            $data = @file_get_contents($name);
            if ($data === false)
                return array();
            $data = @json_decode($data, true);
            if (!$data)
                return array();
            return $data;
        }

        /**
         * Saves global settings for this plugin
         * @param array $settings
         * @return bool
         */
        public function saveGlobalSettings($settings)
        {
            if (!is_array($settings))
                return false;
            $path = $this->getPluginDirectory();
            if (!$path)
                return false;
            $name = $path . DIRECTORY_SEPARATOR . "config.json";
            $data = @json_encode($settings);
            if (!$data)
                return false;
            return (@file_put_contents($name, $data) !== false);
        }

        /**
         * Adds a string to the list of debug strings with "info" debug level
         * @param string $s
         */
        public function addInfoString($s)
        {
            array_push($this->debug_info, $s);
        }

        /**
         * Adds a string to the list of debug strings with "warning" debug level
         * @param string $s
         */
        public function addWarningString($s)
        {
            array_push($this->debug_warning, $s);
        }

        /**
         * Adds a string to the list of debug strings with "error" debug level
         * @noinspection PhpUnused
         * @param string $s
         */
        public function addErrorString($s)
        {
            array_push($this->debug_error, $s);
        }

        public function printDebugStrings()
        {
            echo <<< EOT
if (typeof console.groupCollapsed === "function")
	console.groupCollapsed('{$this->plugin_info['name']} #{$this->id}');
else if (typeof console.group === "function")
	console.group('{$this->plugin_info['name']} #{$this->id}');
EOT;
            /** @noinspection PhpRedundantOptionalArgumentInspection */
            $this->printJSDebugText($this->debug_info, 1);
            $this->printJSDebugText($this->debug_warning, 2);
            $this->printJSDebugText($this->debug_error, 3);
            echo <<< EOT
if (typeof console.groupEnd === "function")
	console.groupEnd();
EOT;
        }

        /*
        Returns true, if the data is suitable for output as a table. Used for debug, see the console.table command.
        */
        public static function isDataForTable($array)
        {
            if ((!is_array($array)) || (sizeof($array) < 1))
                return false;
            $count = -1;
            foreach ($array as $value) {
                if (!is_array($value))
                    return false;
                if ($count < 0)
                    $count = sizeof($value);
                else
                    if ($count != sizeof($value))
                        return false;
            }
            return true;
        }

        /*
        Converts the contents of $value into JSON format that can be later parsed by the browser using Javascript
        */
        public static function EncodeDataJson($value)
        {
            $result = json_encode($value, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
            if (!$result) {
                error_log("Failed to JSON encode data, error code " . json_last_error());
                return '';
            }
            if (is_string($result)) {
                $result = addslashes($result);
                $result = preg_replace("/\r\n|\r|\n/", "\\n", $result);
            }
            return $result;
        }

        /**
         * Prints debug info string for JS console output
         * @param $s
         * @param int $logLevel defines the log warning level
         */
        private function printJSDebugString($s, $logLevel = 1)
        {
            //We don't use prefix anymore, because good browsers can collapse output in groups.
            //$prefix='['.$this->plugin_info['name'].' #'.$this->id.'] ';
            $prefix = '';
            $datatable = self::isDataForTable($s);
            if ($datatable) {
                echo <<< EOT
if (typeof console.table === "function"){
	var data_list=[];
EOT;
                foreach ($s as $value) {
                    echo "try { data_list.push(JSON.parse('" . self::EncodeDataJson($value) . "')); } catch(err) { console.error('Failed to parse JSON: '+err); ";
                    $this->printJSDebugString(self::features_var_export($value, 3));
                    echo "}";
                }
                echo <<< EOT
	console.table(data_list);
}
else {
EOT;
            }
            if (is_string($s)) {
                $s = addslashes($s);
                $s = preg_replace("/\r\n|\r|\n/", "\\n", $s);
            }
            switch ($logLevel) {
                case 1:
                    if (is_string($s))
                        echo "console.info('" . $prefix . $s . "');";
                    else {
                        echo "try {console.info(JSON.parse('" . self::EncodeDataJson($s) . "')); } catch (err) { console.error('Failed to parse JSON: '+err); ";
                        $this->printJSDebugString(self::features_var_export($s), 3);
                        echo "}";
                    }
                    break;
                case 2:
                    if (is_string($s))
                        echo "console.warn('" . $prefix . $s . "');";
                    else {
                        echo "try {console.warn(JSON.parse('" . self::EncodeDataJson($s) . "')); } catch (err) { console.error('Failed to parse JSON: '+err); ";
                        $this->printJSDebugString(self::features_var_export($s), 3);
                        echo "}";
                    }
                    break;
                case 3:
                    if (is_string($s))
                        echo "console.error('" . $prefix . $s . "');";
                    else {
                        echo "try {console.error(JSON.parse('" . self::EncodeDataJson($s) . "')); } catch (err) { console.error('Failed to parse JSON: '+err); ";
                        $this->printJSDebugString(self::features_var_export($s), 3);
                        echo "}";
                    }
                    break;
                default:
                    if (is_string($s))
                        echo "console.log('" . $prefix . $s . "');";
                    else {
                        echo "try {console.log(JSON.parse('" . self::EncodeDataJson($s) . "')); } catch (err) { console.error('Failed to parse JSON: '+err); ";
                        $this->printJSDebugString(self::features_var_export($s), 3);
                        echo "}";
                    }
                    break;
            }
            if ($datatable)
                echo '}';
        }

        /**
         * Prints debug info strings (array) for JS console output
         * @param array $arrayStrings
         * @param int $logLevel defines the log warning level
         */
        private function printJSDebugText($arrayStrings, $logLevel = 1)
        {
            foreach ($arrayStrings as $s) {
                $this->printJSDebugString($s, $logLevel);
            }
        }

        /**
         * UTF8 safe basename function
         * @noinspection PhpUnused
         * @param string $path
         * @param string|null $suffix
         * @return false|string
         */
        public static function mb_basename($path, $suffix = null)
        {
            $split = preg_split('/\\' . DIRECTORY_SEPARATOR . '/', rtrim($path, DIRECTORY_SEPARATOR . ' '));
            return substr(basename('X' . $split[count($split) - 1], $suffix), 1);
        }
    }
}
