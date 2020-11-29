<?php
/*
 -------------------------------------------------------------------------
 CVE
 Copyright (C) 2020 by Curtis Conard
 https://github.com/cconard96/glpi-cve-plugin
 -------------------------------------------------------------------------
 LICENSE
 This file is part of CVE.
 CVE is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
 CVE is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with CVE. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
*/

define('PLUGIN_CVE_VERSION', '1.0.0');
define('PLUGIN_CVE_MIN_GLPI', '9.5.0');
define('PLUGIN_CVE_MAX_GLPI', '9.6.0');

function plugin_init_cve()
{
	global $PLUGIN_HOOKS;
	$PLUGIN_HOOKS['csrf_compliant']['cve'] = true;
   $PLUGIN_HOOKS['menu_toadd']['cve'] = ['plugins' => PluginCveCve::class];
   Plugin::registerClass(PluginCveConfig::class, ['addtabon' => Config::class]);
   Plugin::registerClass(PluginCveCve::class, ['addtabon' => [
      Software::class,
      SoftwareVersion::class
   ]]);
}

function plugin_version_cve()
{
	return [
	   'name'         => __('CVE', 'cve'),
	   'version'      => PLUGIN_CVE_VERSION,
	   'author'       => 'Curtis Conard',
	   'license'      => 'GPLv2+',
	   'homepage'     =>'https://github.com/cconard96/glpi-cve-plugin',
	   'requirements' => [
	      'glpi'   => [
	         'min' => PLUGIN_CVE_MIN_GLPI,
	         'max' => PLUGIN_CVE_MAX_GLPI
	      ]
	   ]
	];
}

function plugin_cve_check_prerequisites()
{
	if (!method_exists('Plugin', 'checkGlpiVersion')) {
	   $version = preg_replace('/^((\d+\.?)+).*$/', '$1', GLPI_VERSION);
	   $matchMinGlpiReq = version_compare($version, PLUGIN_CVE_MIN_GLPI, '>=');
	   $matchMaxGlpiReq = version_compare($version, PLUGIN_CVE_MAX_GLPI, '<');
	   if (!$matchMinGlpiReq || !$matchMaxGlpiReq) {
	      echo vsprintf(
	         'This plugin requires GLPI >= %1$s and < %2$s.',
	         [
	            PLUGIN_CVE_MIN_GLPI,
	            PLUGIN_CVE_MAX_GLPI,
	         ]
	      );
	      return false;
	   }
	}
	return true;
}

function plugin_cve_check_config()
{
	return true;
}

