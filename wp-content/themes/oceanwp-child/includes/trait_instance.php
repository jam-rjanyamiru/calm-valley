<?php
if(!trait_exists('Plugin_Instance')) {
    trait Plugin_Instance
    {
        private static $instance;

        public static function get_instance()
        {
            if(is_null(self::$instance))
                self::$instance = new self;

            return self::$instance;
        }
    }
}