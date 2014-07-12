<?php
Route::filter('force_default_admin_language', function()
{
    L::set_admin(L::getDefault());
});