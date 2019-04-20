<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Twig_Environment;
use Twig_Extensions_Extension_I18n;
use Twig_Loader_Array;
use Twig_Source;
use Zer0\App;

/**
 * Class to get gettext strings from Quicky files returning arrays.
 */
class Quicky extends Extractor implements ExtractorInterface
{
    public static $options = [
        'extractComments' => 'notes:',
    ];

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $options += static::$options;

        /**
         * @var \Quicky $tpl
         */
        $tpl = App::instance()->factory('000-default.ymlQuicky');
        $tpl->lang = 'en';
        $tpl->register_function('url', function () {
        });

        $tpl->lang_callback = function ($match) use ($translations) {
            $original = $match[1];
            $translations->insert($original, $original)->setTranslation($original);
        };
        PhpCode::fromString($tpl->_compile_string($string, $options['file']), $translations, $options);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromFile($file, Translations $translations, array $options = [])
    {
        /**
         * @var \Quicky $tpl
         */
        $tpl = App::instance()->factory('Quicky');
        $tpl->lang_callback = function ($match) use ($translations) {
            $original = $match[1];
            $translations->insert($original, $original)->setTranslation($original);
        };
        $tpl->lang = 'en';

        $l = strlen($tpl->template_dir);
        if (substr($file, 0, $l) === $tpl->template_dir) {
            $file = substr($file, $l);
        }
        $options['file'] = $tpl->_compile($file, null, 'Quicky', true);
        PhpCode::fromString(file_get_contents($options['file']), $translations, $options);
    }
}
