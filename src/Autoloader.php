<?php

function nabarabane_cbr_autoload($classname)
{
	$classname = preg_replace('/^[A-Z]+\\\/', '', $classname);
	$filename = __DIR__.DIRECTORY_SEPARATOR.$classname.'.php';
	if (is_readable($filename)) {
		require $filename;
	}
}

spl_autoload_register('nabarabane_cbr_autoload', true, true);
