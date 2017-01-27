<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id: files.php 430 2011-02-06 11:14:44Z nikosdion $
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Subdirectories exclusion filter. Excludes temporary, cache and backup output
 * directories' contents from being backed up.
 */
class AEFilterPlatformFiles extends AEAbstractFilter
{
	public function __construct()
	{
		$this->object	= 'file';
		$this->subtype	= 'all';
		$this->method	= 'direct';
		$this->filter_name = 'PlatformFiles';

		// We take advantage of the filter class magic to inject our custom filters
		$allFiles = explode('|', AEFactory::getConfiguration()->get('akeeba.basic.exclude_files'));
		$this->filter_data['[SITEROOT]'] = array_unique($allFiles);

		parent::__construct();
	}

}