<?php 
/**
 * Redirects to directory Public for non vhost environments
 */

header('location: ' . dirname($_SERVER['PHP_SELF']) . '/Public/');

