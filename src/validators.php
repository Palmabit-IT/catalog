<?php

Validator::extend('currency', function($attribute, $value, $parameters)
{
    return preg_match("/^[0-9]+\.[0-9]{0,2}$/", $value);
});