# OpenFlame

## OpenFlame Coding Guidelines

### Editor settings

* You must use tab indentation for within each block
* Tabs should be set to equal 4 (four) spaces for OpenFlame.
* All files should be saved with UNIX (also known as LF or "\n") linefeeds, and not using the Windows linefeeds (also known as CRLF or "\r\n") or Classic Mac (also known as CR or "\r") linefeeds.

### General coding style

* Use braces and not keywords (such as endif; and endwhile;)
* Braces indicating the start or end of code blocks are to be placed on their own line at all times.
* Only have one statement in a line!  Do not stack multiple statements separated by the end-of-statement character on one line!
* Long lines of code should not be wrapped onto the next line.
* Order of operations should be made explicitly clear; use parenthesis as necessary.
* Put a space between operators and values.
* Use symbolic operators instead of keyword operators.  This isn't Visual Basic, this is PHP.
* Ternary statements are allowed for assigning values to a variable or property, or specifying parameters for a method/function.  Ternary statements are NOT mini-if statements for deciding on what methods/functions to execute!

**Examples:**

example 1 - *braces vs keywords*

	// this is bad
	if($something == true)
		echo 'hi';
	endif;
	// this is good
	if($something == true)
	{
		echo 'hi';
	}

example 2 - *brace location*

	// this is bad
	if($something == true){
		echo 'hi';
	}
	// this is good
	if($something == true)
	{
		echo 'hi';
	}

example 3 - *statement stacking*

	// this is bad
	echo 'hi'; $this->doSomething(); exit();
	// this is good
	echo 'hi';
	$this->doSomething();
	exit();

example 4 - *statement wrapping*

	// this is bad
	$this->doSomething('somereallylongstringthatiswaytoolongthatyoushouldneverseeinyourcode',
		array('something' => 'foobar'));
	// this is good
	$this->doSomething('somereallylongstringthatiswaytoolongthatyoushouldneverseeinyourcode', array('something' => 'foobar'));
	// this is much better
	$long_string = 'somereallylongstringthatiswaytoolongthatyoushouldneverseeinyourcode';
	$this->doSomething($long_string, array('something' => 'foobar'));

example 5 - *order of operations, operator precedence*

	// this is bad
	$bool = ($big_var < 7 && $super_var > 8 || $some_var == 4);
	// this is good
	$bool = ($big_var < 7 && ($super_var > 8 || $some_var == 4));

example 6 - *spacing between operators and values*

	// this is bad
	$value=3+$another_value;
	// this is good
	$value = 3 + $another_value;

example 7 - *symbolic operators versus keyword operators*

	// this is bad
	if($something AND $something_else)
	// this is good
	if($something && $something_else)

example 8 - *ternary statements*

	// this is bad
	($some_value) ? $this->doSomething($value) : $this->doSomethingElse($value);
	// this is good
	$some_value = ($value !== false) ? $this->doSomething($value) : $some_other_value;

### Naming style
* All names (classes, files, methods, properties, etc.) should have short yet descriptive names.
* Class names, file names, directory names, and method names should be in camelCase.
* Filenames and class names should start with the first letter capitalized
* Method names should start with the first letter NOT capitalized.
* Variable names and property names are to be written in lowercase with underscores replacing spaces.
* Boolean values (true and false) are to be written in lowercase, while the "NULL" keyword is to be written in uppercase.
* Underscores in method names are reserved for hookable methods and built-in magic methods.

**Examples:**

example 1 - *class naming, method naming, property naming*

	class SomeClass
	{
		public $some_var = '';
		public function doSomething()
		{
			echo 'did something';
		}
	}

example 2 - *directory/file naming*

	/Includes/Core/Language.php

example 3 - *variable naming*

	$some_variable = 3;
	$another_variable = 'string';

example 4 - *boolean/null use*

	$null_variable = NULL;
	$true_variable = true;
	$false_variable = false;

example 5 - *naming things clearly*

	// this is bad
	$cost_to_construct_giant_robot = 300;
	// this is better
	$giant_robot_cost = 300;

	// this is very bad
	$bc = 'red';
	// this is better
	$background_color = 'red';


### "IF" conditionals

* Do not include a space between "if" and the opening parenthesis.
* When no "else" or "elseif" extension is present and when there is only one line being executed, does not have to use braces.

**Example:**

example 1 - *single line if*

	if($some_var)
		$this->doSomething();

example 2 - *multiline if*

	if($some_var)
	{
		$this->doSomething();
		$this->doSomethingElse();
	}

example 3 - *if conditional with elseif/else*

	if($some_var)
	{
		$this->doSomething();
	}
	else
	{
		$this->doSomethingElse();
	}

### Loops

* For and foreach loops, as long as they only execute one line of code in the loop, are not required to use braces
* While and do-while loops are required to use braces.

**Examples:**

example 1 - *single-line for loop*

	// no braces
	for($i = 1;  $i <= 10; $i++)
		echo $i;

example 2 - *multiline for loop*

	// use braces
	for($i = 1;  $i <= 10; $i++)
	{
		echo $i;
		echo $i * 2;
	}

example 3 - *single line foreach loop*

	// no braces
	foreach($array as $key => $value)
		$another_array[$key] = $value;

example 4 - *multiline foreach loop*

	// use braces
	foreach($array as $key => $value)
	{
		$another_array[$key] = $value;
		$yet_another_array[] = $key;
	}

example 5 - *while loops*

	// always use braces for while
	while(true)
	{
		$this->doSomething();
	}

example 6 - *do-while loops*

	// always use braces for while
	do
	{
		$this->doSomething();
	}
	while(true);

### Code documentation

* All files, classes, methods, and properties should be properly documented as per PHPDoc guidelines.
* Any methods that throw an exception must also state such in their method documentation using the @throws comment.  See the provided example on how to write the @throws comment.
* Always declare the access level for methods and properties.  This is PHP5 we are coding for, not PHP4.

**Examples:**

example 1 - *file header documentation*

	// you should fill this in with the appropriate information...
	// @link should be changed to a link to your own project (or omitted),
	// the @package comment should change to the correct package this is in as well
	<?php
	/**
	 *
	 * @package     {package name}
	 * @copyright   (c) {copyright year(s)} {copyright holder}
	 * @license     {license info}
	 * @link        {project link}
	 */

example 2 - *class documentation*

	// @note this should say what the class is, describe it, and also contain the correct @author, @link, and @license comments
	/**
	 * OpenFlame Web Framework - Main class
	 * 	     Contains the static objects that power the framework
	 *
	 *
	 * @author      Sam Thompson ("Sam")
	 * @license     http://opensource.org/licenses/mit-license.php The MIT License
	 * @link        http://github.com/OpenFlame/OpenFlame-Framework
	 */
	class Of
	{

example 3 - *property documentation*

	// @note this should be @var (variable type) - (property description)
	/**
	 * @var array - Automagical property for use with magic methods
	 */
	private $virtual_storage = array();

example 4 - *method documentation, no parameters*

	// @note even if the method does not return a value, it should have an @return comment! just say @return void
	/**
	 * Instantiates OpenFlame and sets everything up.
	 * @return void
	 */
	public function __construct()

example 5 - *method documentation with parameters, @throws comment*

	// @note the @param comment should be like follows: @param (variable type) $(parameter name) - (parameter description)
	// @note also, take note: this shows that the method throws an exception of type OfException, and any calls to this method should be prepared if it does throw an exception of this type
	/**
	 * Configuration file settings load method
	 * @param string $file - The configuration file to load
	 * @return void
	 *
	 * @throws OfException
	 */
	private function load($file)
