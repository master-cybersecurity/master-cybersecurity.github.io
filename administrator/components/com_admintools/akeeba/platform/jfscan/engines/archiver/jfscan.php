<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

// Load the diff engine
require_once dirname(__FILE__).'/../../utils/diff.php';

class AEArchiverJfscan extends AEAbstractArchiver
{
	private $generateDiff = null;
	private $ignoreNonThreats = null;

	protected function __bootstrap_code()
	{
		if(is_null($this->generateDiff)) {
			JLoader::import('joomla.html.parameter');
			JLoader::import('joomla.application.component.helper');

			$db = JFactory::getDbo();
			$sql = $db->getQuery(true)
				->select($db->qn('params'))
				->from($db->qn('#__extensions'))
				->where($db->qn('type').' = '.$db->q('component'))
				->where($db->qn('element').' = '.$db->q('com_admintools'));
			$db->setQuery($sql);
			$rawparams = $db->loadResult();
			$params = new JRegistry();
			if(version_compare(JVERSION, '3.0', 'ge')) {
				$params->loadString($rawparams, 'JSON');
			} else {
				$params->loadJSON($rawparams);
			}

			if(version_compare(JVERSION, '3.0', 'ge')) {
				$this->generateDiff = $params->get('scandiffs', false);
				$this->ignoreNonThreats = $params->get('scanignorenonthreats', false);
				$email = $params->get('scanemail', '');
			} else {
				$this->generateDiff = $params->getValue('scandiffs', false);
				$this->ignoreNonThreats = $params->getValue('scanignorenonthreats', false);
				$email = $params->getValue('scanemail', '');
			}
			AEFactory::getConfiguration()->set('admintools.scanner.email', $email);
		}
	}

	public function initialize( $targetArchivePath, $options = array() )
	{
	}

	public function finalize()
	{

	}

	public function getExtension()
	{
		return '';
	}

	protected function _addFile( $isVirtual, &$sourceNameOrData, $targetName )
	{
		if($isVirtual) return true;

		$extensions = explode('|', AEFactory::getConfiguration()->get('akeeba.basic.file_extensions', ''));
		$ignore = true;
		foreach($extensions as $extension) {
			if(('.' . $extension) == (substr($targetName, -(strlen($extension) + 1)))) {
				$ignore = false;
				break;
			}
		}
		if($ignore) {
			return true;
		}

		// Count one more file scanned
		$multipart = AEFactory::getConfiguration()->get('volatile.statistics.multipart', 0);
		$multipart++;
		AEFactory::getConfiguration()->set('volatile.statistics.multipart', $multipart);

		$filedata = (object)array(
			'path'		=> $targetName,
			'filedate'	=> @filemtime($sourceNameOrData),
			'filesize'	=> @filesize($sourceNameOrData),
			'data'		=> gzdeflate(@file_get_contents($sourceNameOrData), 9),
			'checksum'	=> md5_file($sourceNameOrData)
		);

		$db = JFactory::getDbo();
		$sql = 'SELECT * FROM '.$db->quoteName('#__admintools_filescache').
			' WHERE '.$db->quoteName('path').' = '.$db->quote($targetName);
		$db->setQuery($sql,0,1);
		$oldRecord = $db->loadObject();

		if(!is_null($oldRecord)) {
			// Check for changes
			$fileModified = false;
			if($oldRecord->filedate != $filedata->filedate) $fileModified = true;
			if($oldRecord->filesize != $filedata->filesize) $fileModified = true;
			if($oldRecord->checksum != $filedata->checksum) $fileModified = true;

			if($fileModified) {
				// ### MODIFIED FILE ###
				$this->_logFileChange($filedata, $oldRecord);

				if(!$this->generateDiff) {
					$filedata->data = '';
				}

				// Replace the old record
				$sql = 'DELETE FROM '.$db->quoteName('#__admintools_filescache').
						' WHERE '.$db->quoteName('path').' = '.$db->quote($targetName);
				$db->setQuery($sql);
				$db->execute();
				$db->insertObject('#__admintools_filescache', $filedata);
			} else {
				// Existing file. Get the last log record.
				$sql = 'SELECT * FROM '.$db->quoteName('#__admintools_scanalerts').
					' WHERE '.$db->quoteName('path').' = '.$db->quote($targetName).
					' ORDER BY scan_id DESC';
				$db->setQuery($sql,0,1);
				$lastRecord = $db->loadObject();

				// If the file is not "acknowledged", we have to
				// check its threat score.
				if(is_object($lastRecord)) {
					if($lastRecord->acknowledged) return true;
				}

				// Not acknowledged. Proceed.
				$text = @file_get_contents($sourceNameOrData);
				$threatScore = $this->_getThreatScore($text);

				if($threatScore == 0) return true;

				// ### SUSPICIOUS EXISTING FILE ###

				// Stil here? It's a possible threat! Log it as a modified file.
				$alertRecord = array(
					'path'			=> $targetName,
					'scan_id'		=> AEFactory::getStatistics()->getId(),
					'diff'			=> "###SUSPICIOUS FILE###\n",
					'threat_score'	=> $threatScore,
					'acknowledged'	=> 0
				);

				if($this->generateDiff) {
					$alertRecord['diff'] = <<<ENDFILEDATA
###SUSPICIOUS FILE###
>> Admin Tools detected that this file contains potentially suspicious code.
>> This DOES NOT necessarily mean that it is a hacking script. There is always
>> the possibility of a false alarm. The contents of the file are included
>> below this line so that you can review them.
$text
ENDFILEDATA;
				}

				unset($text);
				$alertRecord = (object)$alertRecord;
				$db = JFactory::getDbo();
				$db->insertObject('#__admintools_scanalerts', $alertRecord);
			}
		} else {
			// ### NEW FILE ###
			$this->_logFileChange($filedata);

			if(!$this->generateDiff) {
				$filedata->data = '';
			}

			// Add a new file record
			$db->insertObject('#__admintools_filescache', $filedata);
		}

		return true;
	}

	private function _logFileChange(&$newFileRecord, &$oldFileRecord = null)
	{
		// Initialise the new alert record
		$alertRecord = array(
			'path'			=> $newFileRecord->path,
			'scan_id'		=> AEFactory::getStatistics()->getId(),
			'diff'			=> '',
			'threat_score'	=> 0,
			'acknowledged'	=> 0
		);

		$newText = gzinflate($newFileRecord->data);
		$newText = str_replace("\r\n", "\n", $newText);
		$newText = str_replace("\r", "\n", $newText);

		// Produce the diff if there is an old file
		if(!is_null($oldFileRecord)) {
			if($this->generateDiff) {
				// Modified file, generate diff
				$newLines = explode("\n", $newText);
				unset($newText);

				$newText = gzinflate($oldFileRecord->data);
				$newText = str_replace("\r\n", "\n", $newText);
				$newText = str_replace("\r", "\n", $newText);
				$oldLines = explode("\n", $newText);
				unset($newText);

				$diffObject = new Horde_Text_Diff('native', array($newLines, $oldLines));
				$renderer = new Horde_Text_Diff_Renderer();
				$alertRecord['diff'] = $renderer->render($diffObject);
				unset($renderer);
				unset($diffObject);
				unset($newLines);
				unset($oldLines);

				$alertRecord['threat_score'] = $this->_getThreatScore($alertRecord['diff']);
			} else {
				// Modified file, do not generate diff
				$alertRecord['diff'] = "###MODIFIED FILE###\n";
				$alertRecord['threat_score'] = $this->_getThreatScore($newText);
				unset($newText);
			}
		} else {
			// New file
			$alertRecord['threat_score'] = $this->_getThreatScore($newText);
			unset($newText);
		}

		// Do not create a record for non-threat files
		if($this->ignoreNonThreats && !$alertRecord['threat_score']) return;

		$alertRecord = (object)$alertRecord;
		$db = JFactory::getDbo();
		$db->insertObject('#__admintools_scanalerts', $alertRecord);
	}

	private function _getThreatScore($text)
	{
		// These are the lists of signatures, initially empty
		static $suspiciousWords = null;
		static $knownHackSignatures = null;
		static $suspiciousRegEx = null;

		// Build the lists of signatures from the encoded, compressed configuration.
		// The encoded configuration is built by the build/hacksignatures/create_lists.php
		if (is_null($suspiciousWords) || is_null($knownHackSignatures) || is_null($suspiciousRegEx))
		{
			$encodedConfig = '75534d4fe33010fd2babf4408baa405b2802690f8856e2d22d2a451c0832' .
				'ae3349bdebd8597ff46349fffb8ed34d1a587ab1df7b33f366ec38ef8171' .
				'26e78c2b679e958e4d70f312dc5d5f075d0cf018b784cbf85b7456a11041' .
				'b8b4393566edc306f48a3308f39245676059745647c304f932a35c688654' .
				'8235965a9f18ccc693e97c4c6e47a35914a072d2104e903fdc3f90dba7f9' .
				'3d797a1ccf9afce1d91b2fa85992253756e9eda1b159d258ad0f3cd5cae5' .
				'c6e7675bf35b340ab26dc8648220771a129bc721537bae559336b106a3c4' .
				'aa6242a55c56c4648b1a6e0db3a2c130b162943130a6c1949396cb34c49c' .
				'e0b51bfc926a2def29fbf5c853492d8e861fe43d88a2cd7080cb55dfa34b' .
				'bff43cbda8e865e2d1d06b6514ea8a32e5da2f650a0437bdf3f36ee9d8af' .
				'7d0695cfc05b0c2e6ac7ba4169366c68a5cfaedb783e3348c71b3f6d0bbf' .
				'0d0c2f480c4cc5d08e22d3398da2768b0737e761bf1bb4dab00156982508' .
				'413cec7c91032b2abe90bfefb5b77678da796b3a627a71d4760f8e8fd5c7' .
				'1b6971c9490af653d1cb4914bcbef776862640322c3c34c564027275a460' .
				'34bd7b9a8c7fccc96c3a9dfba21e96c48ab90c64dd24dc83b5e6163ef938' .
				'0986d1fce3a03d3f68db4f8a2f035f32141e1bb09d2353b4ebb90b958324' .
				'fe0e62ae8b5a265c32e170ffa8fafb2ba5981bba1040122799e54a9a820a' .
				'a1d6c46941126f59e0d325a0b5d2a6283782429170ac71b95034febf22a3' .
				'9bb281f386c4f20c87733677962c5c9280c61fa2b3bfe64b3c6eae21c5e3' .
				'e682323876ca9285789f51741ac2c760ebdf936f4dfe940194ae263f2bb8' .
				'9dd66a0d46e665d3dd1e6a77bbbf';

				$zipped = pack('H*', $encodedConfig);
				$json_encoded = gzinflate($zipped);
				$new_list = json_decode($json_encoded, true);

				extract($new_list);
		}

		$score = 0;
		$hits = 0;

		foreach($suspiciousWords as $word) {
			$count = substr_count($text, $word);
			if($count) {
				$hits += $count;
				$score += $count;
			}
		}

		foreach($knownHackSignatures as $signature => $sigscore) {
			$count = substr_count($text, $word);
			if($count) {
				$hits += $count;
				$score += $count * $sigscore;
			}
		}

		foreach($suspiciousRegEx as $pattern => $value) {
			$count += preg_match_all($pattern, $text, $matches);
			if($count) {
				$hits += $count;
				$score += $value * $count;
			}
		}

		if($hits == 0) return 0;

		//return sprintf('%0.2f', $score/$hits);
		return $score;
	}

}