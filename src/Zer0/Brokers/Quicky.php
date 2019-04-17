<?php

namespace Zer0\Brokers;

use Zer0\Config\Interfaces\ConfigInterface;

/**
 * Class Quicky
 * @package Zer0\Brokers
 */
class Quicky extends Base
{
    public $mock_functions;

    /**
     * @param ConfigInterface $config
     * @return \Quicky
     */
    public function instantiate(ConfigInterface $config): \Quicky
    {
        $quicky = new class extends \Quicky {
            public $mock_functions = [];
            public function mockFunctions() {
                foreach ($this->mock_functions as $func) {
                    $this->register_function($func, function () {
                    });
                }
            }

            public function applyConfig(ConfigInterface $config) {
                foreach ($config->toArray() as $key => $value) {
                    $ref =& $this;
                    foreach (explode('.', $key) as $split) {
                        if (is_object($ref)) {
                            $ref =& $ref->{$split};
                        } elseif (is_array($ref)) {
                            $ref =& $ref[$split];
                        }
                    }
                    $ref = $value;
                }
            }
        };

        $quicky->applyConfig($config);
        $quicky->mockFunctions();
        //$quicky->load_filter('pre', 'optimize');

        $quicky->lang_callback = function ($match) {
            return __($match[1]);
        };

        $quicky->lang_callback_e = function ($expr) {
            return '__(' . $expr . ')';
        };
        return $quicky;
    }

    /**
     * @param string $name
     * @param bool $caching
     * @return \Quicky
     */
    public function get(string $name = '', bool $caching = true): \Quicky
    {
        return parent::get($name, $caching);
    }
}
