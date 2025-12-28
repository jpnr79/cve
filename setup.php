function plugin_cve_check_prerequisites() {
	// GLPI 11+ compatible version check: read from version file
	$glpi_version = 'unknown';
	$version_file = dirname(__DIR__, 2) . '/version';
	if (file_exists($version_file)) {
		$glpi_version = trim(file_get_contents($version_file));
	}
	if (version_compare($glpi_version, '11.0', '<')) {
		$msg = sprintf(
			'ERROR [%s:%s] GLPI version too low: %s, user=%s',
			__FILE__, __FUNCTION__, $glpi_version, $_SESSION['glpiname'] ?? 'unknown'
		);
		try {
			if (class_exists('Toolbox') && method_exists('Toolbox', 'logInFile')) {
				@Toolbox::logInFile('cve', $msg);
			} else {
				$logfile = __DIR__ . '/cve_error.log';
				file_put_contents($logfile, $msg . "\n", FILE_APPEND);
			}
		} catch (\Throwable $e) {
			// Fallback: ignore logging errors
		}
		echo "This plugin requires GLPI >= 11.0";
		return false;
	}
	return true;
}
<?php
/*
 -------------------------------------------------------------------------
 CVE
 Copyright (C) 2020-2021 by Curtis Conard
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
define('PLUGIN_CVE_MAX_GLPI', '12.0.0');

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
   $PLUGIN_HOOKS['dashboard_cards']['cve'] = 'plugin_cve_dashboardCards';
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
	         'min' => '11.0',
	         'max' => '12.0'
	      ]
	   ]
	];
}
