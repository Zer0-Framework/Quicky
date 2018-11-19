<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Twig_Loader_Array;
use Twig_Environment;
use Twig_Source;
use Twig_Extensions_Extension_I18n;
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
         * @var \Quicky $quicky
         */
        $quicky = App::instance()->broker('Quicky')->get();
        $quicky->lang = 'en';

        $quicky->lang_callback = function ($match) use ($translations) {
            $original = $match[1];
            $translations->insert(null, $original);
        };
        PhpCode::fromString($quicky->_compile_string($string, $options['file']), $translations, $options);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromFile($file, Translations $translations, array $options = [])
    {
        /**
         * @var \Quicky $quicky
         */
        $quicky = App::instance()->broker('Quicky')->get();
        $quicky->lang_callback = function ($match) use ($translations) {
            $original = $match[1];
            $translations->insert(null, $original);
        };
        $quicky->lang = 'en';

        $l = strlen($quicky->template_dir);
        if (substr($file, 0, $l) === $quicky->template_dir) {
            $file = substr($file, $l);
        }
        $options['file'] = $quicky->_compile($file, null, 'Quicky', true);
        PhpCode::fromString(file_get_contents($options['file']), $translations, $options);
    }
}
