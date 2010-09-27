<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('IN_OF_TEST')) exit;

/**
 * OpenFlame Web Framework - CLI interface class,
 * 		Provides the rough shell for interaction via CLI.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfCLI
{
	/**
	 * @var boolean - Do we want to enable the use of colors in our output?
	 */
	public $enable_colors = false;

	/**
	 * @var array - Various color codes for use with terminals that support it.
	 */
	protected $fg_colors = array();

	/**
	 * @var array - Various color codes for use with terminals that support it.
	 */
	protected $bg_colors = array();

	/**
	 * @var array - Array of preset color profiles for use with the UI.
	 */
	protected $color_profiles = array();

		/**
	 * @var array - Array of valid strings that can be used as input for boolean true values.
	 */
	protected $bool_yes_vals = array('y' => true, '1' => true, 't' => true, 'yes' => true, 'enable' => true, 'true' => true, 'on' => true);

	/**
	 * @var array - Array of valid strings that can be used as input for boolean false values.
	 */
	protected $bool_no_vals = array('n' => false, '0' => false, 'f' => false, 'no' => false, 'disable' => false, 'false' => false, 'off' => false);

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		if($this->checkColorSupport())
		{
			$this->fg_colors = array('black' => '30', 'blue' => '34', 'green' => '32', 'cyan' => '36', 'red' => '31', 'purple' => '35', 'brown' => '33', 'yellow' => '33', 'white' => '37');
			$this->bg_colors = array('black' => '40', 'red' => '41', 'green' => '42', 'yellow' => '43', 'blue' => '44', 'magenta' => '45', 'cyan' => '46', 'light_gray' => '47');
			$this->color_profiles = array(
				'BOLD'		=> array('foreground' => 'white', 'bold' => true),
				'STATUS'	=> array('background' => 'black', 'foreground' => 'blue'),
				'CAKE'		=> array('background' => 'black', 'foreground' => 'yellow', 'bold' => true),
				'INFO'		=> array('foreground' => 'cyan', 'bold' => true),
				'WARNING'	=> array('background' => 'yellow', 'foreground' => 'black', 'bold' => true),
				'ERROR'		=> array('background' => 'red', 'foreground' => 'white', 'bold' => true),
			);
			$this->enable_colors = true;
		}
	}

	/**
	 * Check if ANSI colors can be used.
	 * @return boolean - Does our environment support use of colors in output?
	 */
	protected function checkColorSupport()
	{
		return ((stristr(PHP_OS, 'WIN')) ? @getenv('ANSICON') !== false : function_exists('posix_isatty') && @posix_isatty(STDOUT));
	}

	/**
	 * Colorizes the given text
	 * @param string $string - The string to colorizate.
	 * @param string $profile - Name of the color profile to use on the given string.
	 * @return string - The colorizered string.
	 * @note doc "typos" intentional.
	 */
	public function addColor($string, $profile)
	{
		if(!isset($this->color_profiles[strtoupper($profile)]) || empty($this->color_profiles[strtoupper($profile)]))
			return $string;

		$profile = $this->color_profiles[strtoupper($profile)];

		$codes = '';
		$codes .= (isset($profile['foreground']) ? "\033[" . (isset($profile['bold']) ? '1;' : '') . $this->fg_colors[$profile['foreground']] . 'm' : '');
		$codes .= (isset($profile['background']) ? "\033[" . $this->bg_colors[$profile['background']] . 'm' : '');

		return "{$codes}{$string}\033[0m";
	}

	/**
	 * Method that handles output of all data for the UI.
	 * @param string $data - The string to output
	 * @param string $color - The color profile to use for output, if we want to use one.
	 * @return void
	 */
	public function output($data, $color = NULL)
	{
		$data = rtrim($data, PHP_EOL);
		if(is_null($color) || !$this->enable_colors)
		{
			echo str_pad($data, 80) . PHP_EOL;
		}
		else
		{
			echo $this->addColor(str_pad($data, 80), $color) . PHP_EOL;
		}
	}

	/**
	 * Builds a prompt for information from STDIN, so we can ask the user for something.
	 * @param string $instruction - The instruction text to provide the user so they know what we're asking.
	 * @param mixed $default - The default value for the question, may be of any type.
	 * @param string $prompt - The prompt text.
	 * @return mixed - The user input directly from STDIN, with the ending PHP_EOL stripped.
	 */
	protected function stdinPrompt($instruction, $default, $prompt)
	{
		if($instruction)
			$this->output($instruction);

		if($prompt)
			echo $prompt . ' ';

		$input = rtrim(fgets(STDIN), PHP_EOL);
		return (!$input) ? $default : $input;
	}

	/**
	 * Get a boolean value answer from the user.
	 * @param string $instruction - The instruction text to provide the user so they know what we're asking.
	 * @param boolean $default - The default value for the question.
	 * @param string $prompt - The prompt text.
	 * @return boolean - Desired user input.
	 */
	public function getBool($instruction, $default, $prompt = 'y/n')
	{
		$values = array_merge($this->bool_yes_vals, $this->bool_no_vals);

		// Nag the user for a usable answer
		$validates = false;
		do
		{
			$input = strtolower($this->stdinPrompt($instruction, (boolean) $default, $prompt));
			$validates = isset($values[$input]);
			if(!$validates)
				$this->output('Invalid response', 'ERROR');
		}
		while(!$validates);

		return (boolean) $values[$input];
	}

	/**
	 * Get a multiple-choice value answer from the user.
	 * @param string $instruction - The instruction text to provide the user so they know what we're asking.
	 * @param boolean $default - The default value for the question.
	 * @param array $choices - The choices available.
	 * @return string - Desired user input.
	 */
	public function getMulti($instruction, $default, array $choices)
	{
		$prompt = implode(', ', $choices);

		// Nag the user for a usable answer
		$validates = false;
		do
		{
			$input = strtolower($this->stdinPrompt($instruction, (boolean) $default, $prompt));
			$validates = in_array($input, $choices);
			if(!$validates)
				$this->output('Invalid response', 'ERROR');
		}
		while(!$validates);

		return (boolean) $input;
	}

	/**
	 * Get a string value answer from the user.
	 * @param string $instruction - The instruction text to provide the user so they know what we're asking.
	 * @param string $default - The default value for the question.
	 * @param string $prompt - The prompt text.
	 * @return string - Desired user input.
	 */
	public function getString($instruction, $default, $prompt)
	{
		return (string) $this->stdinPrompt($instruction, (string) $default, $prompt);
	}

	/**
	 * Get a integer value answer from the user.
	 * @param string $instruction - The instruction text to provide the user so they know what we're asking.
	 * @param integer $default - The default value for the question.
	 * @param string $prompt - The prompt text.
	 * @return integer - Desired user input.
	 */
	public function getInt($instruction, $default, $prompt)
	{
		return (int) $this->stdinPrompt($instruction, (int) $default, $prompt);
	}
}
