<?php
/*
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
*/

namespace WidgetkitEx\MapEx {

    require_once(__DIR__ . '/WidgetkitExPlugin.php');

    class WidgetkitExMapPlugin extends WidgetkitExPlugin
    {
        //First version of WK that supports Google Maps API key natively
        const minWKAPIVersion = '2.7.5';

        public function isWKAPIKeySupported($appWK)
        {
            return (($appWK['config']->get('googlemapseapikey')) || (version_compare($this->getWKVersion(), WidgetkitExMapPlugin::minWKAPIVersion) >= 0));
        }

        //$appWK - is parameter that must be set to $app upon call.
        public function generateMapExJS($appWK)
        {
            $replacements = array(
                "__WK_SAFE_NAME__" => ($this->getInfo(false))['safe_name'],
                "__WK_FIELD_DISPLAY_DISABLED__" => $appWK['translator']->trans('Field disabled by MapEx widget'),
                "__WK_OK__" => $appWK['translator']->trans('Ok'),
                "__WK_LOADING__" => $appWK['translator']->trans('Loading, please wait...'),
                "__WK_LEVEL__" => $appWK['translator']->trans('Level'),
                "__WK_ACTIVATED__" => $appWK['translator']->trans('Selected collection was activated!'),
                "__WK_ACTIVATE__" => $appWK['translator']->trans('Activate Collection'),
                "__WK_DOWNLOAD_FAILED__" => $appWK['translator']->trans('Failed to download a list of markers collections.'),
                "__WK_WAIT__" => $appWK['translator']->trans('Please, wait...'),
                "__WK_ERROR__" => $appWK['translator']->trans('Error'),
                "__WK_SUCCESS__" => $appWK['translator']->trans('Success'),
                "__WK_INVALID_KEY__" => $appWK['translator']->trans('It seems that your key is invalid. Below is a list of error messages recieved from Google.'),
                "__WK_VALID_KEY__" => $appWK['translator']->trans('It seems that your key is valid.'),
                "__WK_ITEMS_INFO__" => $appWK['translator']->trans('Downloaded information about %number% items.'),
                "__WK_FAILED_ITEMS__" => $appWK['translator']->trans('Failed to parse %number% items:'),
            );

            $js = @file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "../assets/map-ex.js");
            foreach ($replacements as $key => $value) {
                $js = str_replace($key, addslashes($value), $js);
            }
            return $js;
        }

        /**
         * @param string|numeric $value
         * @param string $auto
         * @return string
         */
        public static function getMapSize($value, $auto)
        {
            if ($value === 'auto')
                return $auto;
            if (is_numeric($value))
                return (int)$value . 'px';
            return $value;
        }
    }
}
