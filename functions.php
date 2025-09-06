<?php
    /**
     * Template Name: Functions
     * 
     * Theme Functions for ReviewMVP
     * 
     * @package ReviewMVP
     */

    foreach (glob(get_template_directory() . '/functions/*.php') as $file) {
        require_once $file;
    }