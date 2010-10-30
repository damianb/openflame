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

if(!defined('OF_ROOT')) exit;

/**
 * OpenFlame Web Framework - Exception handler class,
 * 		Extension handler class, builds a page that shows debug info on an uncaught exception.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfHandler
{
	/**
	 * @var Exception - The exception to store
	 */
	public static $exception;

	/**
	 * @var string - The contents of the page to display
	 */
	public static $page = '';

	/**
	 * @var string - The HTML to use for the error page.
	 */
	public static $page_format = '';

	/**
	 * @var boolean - Do we want to show debug info to _everyone_?
	 */
	public static $show_throw_info = false;

	/**
	 * Catches an exception and prepares to deal with it
	 * @param Exception $e - The exception to handle
	 * @return void
	 */
	final public static function catcher(Exception $e)
	{
		self::$exception = $e;
		if(defined('OF_DEBUG') || self::$show_throw_info)
		{
			self::displayException();
		}
		else
		{
			self::badassError();
		}

		exit;
	}

	/**
	 * Displays a debug page showing full info on the exception thrown
	 * @return void
	 */
	final protected static function displayException()
	{
		$e = array(
			'e_type' => get_class(self::$exception),
			'message' => self::$exception->getMessage(),
			'code' => self::$exception->getCode(),
			'trace' => self::highlightTrace(implode(self::traceException(self::$exception->getFile(), self::$exception->getLine(), 7))),
			'file' => self::$exception->getFile(),
			'line' => self::$exception->getLine(),
			'stack' => self::formatStackTrace(),
		);

		if(!$e['stack'])
			$e['stack'] = 'No stack trace available.';

		$message = <<<EOD
						<div style="padding: 50px 0;">
							<h3 style="padding: 0 0 20px 0;">Exception information</h3>

							<div>Exception thrown, error code <span style="font-weight: bold; font-family: monospace; background: #ffffff; color: #007700; padding: 0 2px; border: solid 1px #007700;">{$e['e_type']}::{$e['code']}</span> with message “<span style="font-family: monospace; font-weight: bold;">{$e['message']}</span>”<br /><br />
								on line <span style="font-weight: bold;">{$e['line']}</span> in file: <span style="font-weight: bold; font-family: monospace; background: #ffffff; color: #007700; padding: 0 3px; border: solid 1px #007700;">{$e['file']}</span>
							</div>

							<h3 style="padding: 20px 0;">Trace context</h3>
							<div style="font-family: monospace; background: #ffffff; color: #007700; padding: 8px; border: solid 1px #007700; font-size: 1.2em; overflow:auto;">
								{$e['trace']}
							</div>

							<h3 style="padding: 20px 0;">Stack trace</h3>
							<div>
								{$e['stack']}
							</div>
						</div>
EOD;
		self::buildHTML('Unexpected Exception', $message);
		echo self::$page;
	}

	/**
	 * Display a user-friendly (and obscure) error message.
	 * @return void
	 */
	final public static function badassError()
	{
		$e = array(
			'e_type' => get_class(self::$exception),
			'code' => self::$exception->getCode(),
		);

		$message = <<<EOD
						<div style="padding: 50px 0;">
							Looks like something blew up on our end.  If you would be so kind as to report the error below to a site administrator or site technician, we'll get right on fixing it.<br /><br />
							Error code: <span style="font-weight: bold; font-family: monospace; background: #ffffff; color: #007700; padding: 0 3px; border: solid 1px #007700;">{$e['e_type']}::{$e['code']}</span>
						</div>
EOD;
		self::buildHTML('Unexpected Exception', $message);

		echo self::$page;
	}

	/**
	 * Manually throw an error (useful for server errors and such)
	 * @param string $title - The title to use for the page.
	 * @param string $message - The message to display on the page.
	 * @return void
	 */
	final public static function asplode($title, $message)
	{
		self::buildHTML($title, '<div style="padding: 50px 0;"><p>' . $message . '</p></div>');
		echo self::$page;
	}

	/**
	 * Builds the rough HTML page for the exception handler.
	 * @param string $title - The title to use for the page.
	 * @param string $page - The page content to display within the HTML layout.
	 */
	final public static function buildHTML($title, $page)
	{
		if(empty(self::$page_format))
		{
			$page = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>OpenFlame: Community Content Management</title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<style type="text/css">
			:focus{outline: 0;}img{outline: 0;border: 0;}
			.relative-anchor{position: relative !important;}.anchor-top {position: absolute !important;top: 0 !important;}.anchor-right {position: absolute !important;right: 0 !important;}.anchor-bottom {position: absolute !important;bottom: 0 !important;}.anchor-left {position: absolute !important;left: 0 !important;}			.hang-left{float: left;}.hang-right{float: right;}.hang-clear{display: block;clear: both;}			.align-left{text-align: left;}.align-center{text-align: center;}.align-middle{vertical-align: middle;}.align-right{text-align: right;}.smaller{font-size: 90%;}.bigger{font-size: 110%;}.lowercase{text-transform: lowercase;}.proper-cap{text-transform: capitalize;}.uppercase{text-transform: uppercase;}.nocase{text-transform: none;}
			.fluid-width{width: 100%;}.fluid-height{height: 100%;}.retainer{display: block;position: relative;width: 960px;margin: 0 auto;}
			.img{display: inline-block;position: relative;width: auto;min-width: 1px;height: auto;min-height: 1px;background: transparent no-repeat top left;outline: 0;border: 0;/* text-indent: 99999em; */overflow: hidden;white-space: nowrap;}.img .alt-text{position: absolute;left: -99999em;}
			.rbtcl{}.rbtcl.rbx{}.rbtcl.rbx .ridge {height: 5px;}
			.rbtcl.rbx .ridge .edge,.rbtcl.rbx .ridge .edge .i{height: 1px;background: transparent;border: 1px solid transparent;border-top-width: 0;border-bottom-width: 0;}.rbtcl.rbx .ridge .edge.a{height: 0px;margin: 0 5px;border-width: 0;}.rbtcl.rbx .ridge.upper .edge.a{border-top-width: 1px;}.rbtcl.rbx .ridge.lower .edge.a{border-bottom-width: 1px;}.rbtcl.rbx .ridge .edge.a .i{height: 0;border-width: 0;}.rbtcl.rbx .ridge .edge.b{margin: 0 3px;border-width: 2px;border-top-width: 0;border-bottom-width: 0;}.rbtcl.rbx .ridge .edge.b .i{height: 0;border-width: 0;}.rbtcl.rbx .ridge.upper .edge.b .i{border-top-width: 1px;}.rbtcl.rbx .ridge.lower .edge.b .i{border-bottom-width: 1px;}.rbtcl.rbx .ridge .edge.c{margin: 0 2px;}.rbtcl.rbx .ridge .edge.c .i{height: 1px;border-width: 2px;border-top-width: 0;border-bottom-width: 0;}.rbtcl.rbx .ridge .edge.d{height: 2px;margin: 0 1px;}.rbtcl.rbx .ridge .edge.d .i{height: 2px;}
			.rbtcl.rbx.tl .ridge.upper .edge,.rbtcl.rbx.tl .ridge.upper .edge .i{margin-left: 0;border-left-width: 1px;}.rbtcl.rbx.tr .ridge.upper .edge,.rbtcl.rbx.tr .ridge.upper .edge .i{margin-right: 0;border-right-width: 1px;}.rbtcl.rbx.bl .ridge.lower .edge,.rbtcl.rbx.bl .ridge.lower .edge .i{margin-left: 0;border-left-width: 1px;}.rbtcl.rbx.br .ridge.lower .edge,.rbtcl.rbx.br .ridge.lower .edge .i{margin-right: 0;border-right-width: 1px;}
			.rbtcl.rbx .enclosure,.rbtcl.rbx .enclosure .inner{border: 1px solid transparent;border-top-width: 0;border-bottom-width: 0;}.rbtcl.rbx .enclosure .inner{padding: 1px 4px;}
			.rbtcl.rbx,.rbtcl.rbx .ridge,.rbtcl.rbx .ridge .edge,.rbtcl.rbx .ridge .edge .i,.rbtcl.rbx .enclosure,.rbtcl.rbx .enclosure .inner{display: block;}
			.rbtcl.rbx .ridge .edge,.rbtcl.rbx .enclosure{background: transparent !important;border-color: transparent !important;color: inherit;}.rbtcl.rbx .ridge .edge .i,.rbtcl.rbx .enclosure .inner{border-color: transparent !important;}
			.rbtcl.tri{}.rbtcl.tri.north,.rbtcl.tri.south{width: 21px;height: 11px;}.rbtcl.tri.east,.rbtcl.tri.west{width: 11px;height: 21px;}.rbtcl.tri .ridge{position: relative;width: 100%;height: 100%;}
			.rbtcl.tri .ridge .edge,.rbtcl.tri .ridge .edge .i{border: 1px solid transparent;}.rbtcl.tri.north .ridge .edge,.rbtcl.tri.south .ridge .edge,.rbtcl.tri.north .ridge .edge .i,.rbtcl.tri.south .ridge .edge .i{width: auto;height: 1px;margin: 0 auto;border-top-width: 0px;border-bottom-width: 0px;}.rbtcl.tri.east .ridge .edge,.rbtcl.tri.west .ridge .edge,.rbtcl.tri.east .ridge .edge .i,.rbtcl.tri.west .ridge .edge .i{width: 1px;height: auto;border-left-width: 0px;border-right-width: 0px;}.rbtcl.tri.east .ridge .edge,.rbtcl.tri.west .ridge .edge{position: absolute;}
			.rbtcl.tri.north .ridge .edge.a{width: 1px;height: 0px;border-width: 0px;border-top-width: 1px;}.rbtcl.tri.north .ridge .edge.a .i{height: 0px;border-width: 0px;}.rbtcl.tri.north .ridge .edge.b{width: 1px;}.rbtcl.tri.north .ridge .edge.b .i{width: 1px;height: 0px;border-width: 0px;border-top-width: 1px;}.rbtcl.tri.north .ridge .edge.c{width: 3px;}.rbtcl.tri.north .ridge .edge.d{width: 5px;}.rbtcl.tri.north .ridge .edge.e{width: 7px;}.rbtcl.tri.north .ridge .edge.f{width: 9px;}.rbtcl.tri.north .ridge .edge.g{width: 11px;}.rbtcl.tri.north .ridge .edge.h{width: 13px;}.rbtcl.tri.north .ridge .edge.i{width: 15px;}.rbtcl.tri.north .ridge .edge.j{width: 17px;}.rbtcl.tri.north .ridge .edge.k{width: 19px;}
			.rbtcl.tri.south .ridge .edge.a{width: 19px;}.rbtcl.tri.south .ridge .edge.b{width: 17px;}.rbtcl.tri.south .ridge .edge.c{width: 15px;}.rbtcl.tri.south .ridge .edge.d{width: 13px;}.rbtcl.tri.south .ridge .edge.e{width: 11px;}.rbtcl.tri.south .ridge .edge.f{width: 9px;}.rbtcl.tri.south .ridge .edge.g{width: 7px;}.rbtcl.tri.south .ridge .edge.h{width: 5px;}.rbtcl.tri.south .ridge .edge.i{width: 3px;}.rbtcl.tri.south .ridge .edge.j{width: 1px;}.rbtcl.tri.south .ridge .edge.j .i{width: 1px;height: 0px;border-width: 0px;border-bottom-width: 1px;}.rbtcl.tri.south .ridge .edge.k{width: 1px;height: 0px;border-width: 0px;border-bottom-width: 1px;}.rbtcl.tri.south .ridge .edge.k .i{height: 0px;border-width: 0px;}
			.rbtcl.tri.east .ridge .edge.a{height: 19px;top: 0px;right: 10px;}.rbtcl.tri.east .ridge .edge.a .i{height: 17px;}.rbtcl.tri.east .ridge .edge.b{height: 17px;top: 1px;right: 9px;}.rbtcl.tri.east .ridge .edge.b .i{height: 15px;}.rbtcl.tri.east .ridge .edge.c{height: 15px;top: 2px;right: 8px;}.rbtcl.tri.east .ridge .edge.c .i{height: 13px;}.rbtcl.tri.east .ridge .edge.d{height: 13px;top: 3px;right: 7px;}.rbtcl.tri.east .ridge .edge.d .i{height: 11px;}.rbtcl.tri.east .ridge .edge.e{height: 11px;top: 4px;right: 6px;}.rbtcl.tri.east .ridge .edge.e .i{height: 9px;}.rbtcl.tri.east .ridge .edge.f{height: 9px;top: 5px;right: 5px;}.rbtcl.tri.east .ridge .edge.f .i{height: 7px;}.rbtcl.tri.east .ridge .edge.g{height: 7px;top: 6px;right: 4px;}.rbtcl.tri.east .ridge .edge.g .i{height: 5px;}.rbtcl.tri.east .ridge .edge.h{height: 5px;top: 7px;right: 3px;}.rbtcl.tri.east .ridge .edge.h .i{height: 3px;}.rbtcl.tri.east .ridge .edge.i{height: 3px;top: 8px;right: 2px;}.rbtcl.tri.east .ridge .edge.i .i{height: 1px;}.rbtcl.tri.east .ridge .edge.j{height: 1px;top: 9px;right: 1px;}.rbtcl.tri.east .ridge .edge.j .i{width: 0px;height: 1px;border-width: 0px;border-left-width: 1px;}.rbtcl.tri.east .ridge .edge.k{top: 10px;right: 0px;width: 0px;height: 1px;border-width: 0px;border-left-width: 1px;}.rbtcl.tri.east .ridge .edge.k .i{width: 0px;border-width: 0px;}
			.rbtcl.tri.west .ridge .edge.a{top: 10px;left: 0px;width: 0px;height: 1px;border-width: 0px;border-left-width: 1px;}.rbtcl.tri.west .ridge .edge.a .i{width: 0px;border-width: 0px;}.rbtcl.tri.west .ridge .edge.b{height: 1px;top: 9px;left: 1px;}.rbtcl.tri.west .ridge .edge.b .i{width: 0px;height: 1px;border-width: 0px;border-left-width: 1px;}.rbtcl.tri.west .ridge .edge.c{height: 3px;top: 8px;left: 2px;}.rbtcl.tri.west .ridge .edge.c .i{height: 1px;}.rbtcl.tri.west .ridge .edge.d{height: 5px;top: 7px;left: 3px;}.rbtcl.tri.west .ridge .edge.d .i{height: 3px;}.rbtcl.tri.west .ridge .edge.e{height: 7px;top: 6px;left: 4px;}.rbtcl.tri.west .ridge .edge.e .i{height: 5px;}.rbtcl.tri.west .ridge .edge.f{height: 9px;top: 5px;left: 5px;}.rbtcl.tri.west .ridge .edge.f .i{height: 7px;}.rbtcl.tri.west .ridge .edge.g{height: 11px;top: 4px;left: 6px;}.rbtcl.tri.west .ridge .edge.g .i{height: 9px;}.rbtcl.tri.west .ridge .edge.h{height: 13px;top: 3px;left: 7px;}.rbtcl.tri.west .ridge .edge.h .i{height: 11px;}.rbtcl.tri.west .ridge .edge.i{height: 15px;top: 2px;left: 8px;}.rbtcl.tri.west .ridge .edge.i .i{height: 13px;}.rbtcl.tri.west .ridge .edge.j{height: 17px;top: 1px;left: 9px;}.rbtcl.tri.west .ridge .edge.j .i{height: 15px;}.rbtcl.tri.west .ridge .edge.k{height: 19px;top: 0px;left: 10px;}.rbtcl.tri.west .ridge .edge.k .i{height: 17px;}
			.rbtcl.tri,.rbtcl.tri .ridge,.rbtcl.tri .ridge .edge,.rbtcl.tri .ridge .edge .i{display: block;}
			.rbtcl.tri .ridge .edge{background: transparent !important;border-color: transparent !important;color: inherit;}.rbtcl.tri .ridge .edge .i{border-color: transparent !important;}
			html{height: 100%;}body{height: 100%;margin: 0;padding: 0;background: #373e48;font-family: "Helvetica Neue", "Helvetica", "Arial", "Tahoma", "Verdana", sans-serif;font-size: 9pt;color: #dbdee3;}a,a:link,a:visited{text-decoration: underline;color: #2c5aa0;}a:hover,a:focus{text-decoration: underline;color: #2c5aa0;}a:active{text-decoration: underline;color: #3771c8;}h1,h2,h3,h4,h5,h6{margin: 0;font-family: "Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Georgia", serif;color: #6f7c91;}h1{font-size: 24pt;}h2{font-size: 20pt;}h3{font-size: 16pt;}h4,h5,h6{font-size: 12pt;border-bottom: 1px solid #6f7c91;}p{margin: 2px;padding: 3px;}pre,code{font-family: "Consolas", "Menlo", "Monaco", "Courier New", "Courier", monospace;}
			body .rbtcl.rbx .ridge .edge,body .rbtcl.rbx .enclosure{background: transparent !important;border-color: transparent !important;color: inherit;}body .rbtcl.rbx .ridge .edge .i,body .rbtcl.rbx .enclosure .inner{border-color: transparent !important;}
			div#wrap{}div#wrap div#header{padding: 5px 0;}div#wrap div#container{padding: 3px;background: #dbdee3;color: #373e48;}div#footer{}
			div#wrap div#header div#logo.img{width: 179px; height: 40px; background-image: url(data:image/gif;base64,R0lGODlhswAoAOfoADg/STlASjpBSztCTDxDTDxDTT1ETj5ETj5FTz9GT0BGUEBHUUFIUUFIUkJJUkNJU0NKVERLVEVLVUVMVUZNVkdNV0dOV0hOWElPWUlQWUpQWkpRWktSW0xSXExTXE1TXU5UXU5VXk9VX1BWX1BXYFFXYFJYYVJYYlNZYlNaY1RaY1VbZFVcZVZcZVddZldeZ1heZ1lfaFpgaVthalxia1xja11jbF5kbV9lbmBmbmBmb2FncGJocGJocWNpcWRpcmRqc2Vrc2VrdGZsdGdtdWdtdmhudmlud2lveGpweGtweWtxeWxyem1ye21ze25zfG50fG91fXB1fnB2fnF3f3J3f3J4gHN4gXR5gXR6gnV6gnZ7g3Z8hHd8hHd9hXh+hnl+hnl/h3p/h3uAiHuBiXyBiX2Cin2Din6Di3+EjICFjYCGjYGGjoKHj4KIj4OIkISJkISJkYWKkoaLkoaLk4eMk4iNlImOlYmOloqPl4uQl4uQmI2SmY2Smo6Tmo+Tm5CVnJGVnZGWnZKXnpOYn5SYoJSZoJWaoZaaoZabopeco5ico5idpJmepZqepZqfppufppugp5yhqJ2hqJ6jqZ+jqp+kq6Ckq6GlrKGmrKKmraOnrqOorqSor6Spr6aqsaarsaersqissqits6mttKqutKqutauvtqywtqywt62xt62yuK6yua+zua+zurC0urG1vLK2vLO3vbS4vrW4v7W5v7a6wLi8wrm9w7q+xLu/xby/xbzAxr7Bx77CyL/DyL/DycDEysHEysHFy8LGy8PGzMPHzcTIzcXIzsXJzsbJz8fL0MjL0cjM0cnN0srN08rO08vO1MzP1czQ1c3Q1s7R1s7S18/S2NDT2NDT2dHU2dHV2tLV29PW29PX3NTX3NXY3dXY3tbZ3tfa39jb4Nnc4drd4tve4////////////////////////////////////////////////////////////////////////////////////////////////yH5BAEKAP8ALAAAAACzACgAAAj+AP8JHEiw4EAASgwqXMiwocOHECNKnEixokWBMHwBuMixo8ePIEMGkPChQoCGcc6BCcmypcuXFX846kXunM1zxRaxUAjr3LYUMIMKHcrSwRdbN5Oaw9Sg4Debz1wQnUq1KkQR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); }div#wrap div#header h1#title{margin: 12.5px 0 0 10px;font-size: 14pt;color: #dbdee3;}div#wrap div#container h1,div#wrap div#container h2,div#wrap div#container h3,div#wrap div#container h4,div#wrap div#container h5,div#wrap div#container h6{color: #2c5aa0;}div#wrap div#container h4,div#wrap div#container h5,div#wrap div#container h6{border-bottom: 1px solid #2c5aa0;}div#wrap div#container .rbtcl.rbx .ridge .edge,div#wrap div#container .rbtcl.rbx .enclosure{background: #b7bec8 !important;border-color: #939dac !important;color: inherit;}div#wrap div#container .rbtcl.rbx .ridge .edge .i,div#wrap div#container .rbtcl.rbx .enclosure .inner{border-color: transparent !important;}div#wrap div#footer .copyright{font-size: 90%;color: #6f7c91;}div#wrap div#footer .copyright a,div#wrap div#footer .copyright a:link,div#wrap div#footer .copyright a:visited{color: #939dac;}div#wrap div#footer .copyright a:hover,div#wrap div#footer .copyright a:focus{color: #939dac;}div#wrap div#footer .copyright a:active{color: #b7bec8;}
			.syntaxbg { color: #FFFFFF; } .syntaxcomment { color: #FF8000; } .syntaxdefault { color: #0000BB; } .syntaxhtml { color: #000000; } .syntaxkeyword { color: #007700; } .syntaxstring { color: #DD0000; }
		</style>
	</head>
	<body>
		<div id="wrap">
			<div id="header">
				<div class="retainer">
					<div id="logo" class="img hang-left"><div class="alt-text">OpenFlame</div></div>
					<h1 id="title" class="hang-left">{$title}</h1>
					<div class="hang-clear"><!-- // --></div>
				</div>
			</div>
			<div id="container">
				<div class="retainer">
					%2$s
				</div>
			</div>
			<div id="footer">
				<div class="retainer">
					<p class="copyright hang-right"><a href="http://www.openflamecms.com/" target="_blank" title="OpenFlame: Community Content Management">OpenFlame</a> v1.0.x &copy; WebSyntax. Licensed under <del><a href="http://www.gplv4.org/" target="_blank" title="GNU General Public License v4">GPLv4</a></del> <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank" title="GNU General Public License v4">GPLv3</a>.</p>
					<div class="hang-clear"><!-- // --></div>
				</div>
			</div>
		</div>
	</body>
</html>';
		}
		else
		{
			$page = self::$page_format;
		}

		return sprintf($page, $title, $page);
	}

	/**
	 * Retrieves the context code from where an exception was thrown (as long as file/line are provided) and outputs it.
	 * @param string $file - The file where the exception occurred.
	 * @param string $line - The line where the exception occurred.
	 * @param integer $context - How many lines of context (above AND below) the troublemaker should we grab?
	 * @return string - String containing the perpetrator + context lines for where the error/exception was thrown.
	 */
	final protected static function traceException($file, $line, $context = 3)
	{
		$return = array();
		foreach (file($file) as $i => $str)
		{
			if (($i + 1) > ($line - $context))
			{
				if(($i + 1) > ($line + $context))
					break;
				$return[] = $str;
			}
		}

		return $return;
	}

	/**
	 * Highlights the provided trace context code
	 * @param string $code - The code to highlight.
	 * @return string - The HTML highlighted trace context code.
	 */
	final protected static function highlightTrace($code)
	{
		$remove_tags = false;
		if (!preg_match('/\<\?.*?\?\>/is', $code))
		{
			$remove_tags = true;
			$code = "<?php $code";
		}

		$conf = array('highlight.bg', 'highlight.comment', 'highlight.default', 'highlight.html', 'highlight.keyword', 'highlight.string');
		foreach ($conf as $ini_var)
		{
			@ini_set($ini_var, str_replace('highlight.', 'syntax', $ini_var));
		}

		$code = highlight_string($code, true);

		$str_from = array('<span style="color: ', '<font color="syntax', '</font>', '<code>', '</code>','[', ']', '.', ':');
		$str_to = array('<span class="', '<span class="syntax', '</span>', '', '', '&#91;', '&#93;', '&#46;', '&#58;');

		if ($remove_tags)
		{
			$str_from[] = '<span class="syntaxdefault">&lt;?php </span>';
			$str_to[] = '';
			$str_from[] = '<span class="syntaxdefault">&lt;?php&nbsp;';
			$str_to[] = '<span class="syntaxdefault">';
		}

		$code = str_replace($str_from, $str_to, $code);
		$code = preg_replace('#^(<span class="[a-z_]+">)\n?(.*?)\n?(</span>)$#is', '$1$2$3', $code);

		$code = preg_replace('#^<span class="[a-z]+"><span class="([a-z]+)">(.*)</span></span>#s', '<span class="$1">$2</span>', $code);
		$code = preg_replace('#(?:\s++|&nbsp;)*+</span>$#u', '</span>', $code);

		// remove newline at the end
		if (!empty($code) && substr($code, -1) == "\n")
		{
			$code = substr($code, 0, -1);
		}

		return $code;
	}

	/**
	 * Format the stack trace for the currently loaded exception
	 * @return string - The string containing the formatted HTML stack trace
	 */
	final protected static function formatStackTrace()
	{
		$return = array();
		$stack = self::$exception->getTrace();

		if(!$stack)
			return array();

		$return[] = '<ol style="list-style-type: none;">' . "\n";
		foreach($stack as $id => $trace)
		{
			$arg_count = sizeof($trace['args']);
			if($arg_count)
			{
				$i = 1;
				reset($trace['args']);

				$arg = current($trace['args']);
				if(is_string($arg))
				{
					if(strlen($arg) > 30)
						$arg = '{oversize string}';
					$arg = '\'' . $arg . '\'';
				}
				elseif(is_array($arg))
				{
					$arg = 'Array';
				}
				elseif(is_object($arg))
				{
					$arg = 'Object ' . get_class($arg);
				}

				$args = '<span style="color: #0000BB;">' . $arg . '</span>';
				if($arg_count > 1)
				{
					while($i++ < $arg_count);
					{
						$arg = next($trace['args']);
						if(is_string($arg))
						{
							if(strlen($arg) > 30 || strpos($arg, "\n"))
								$arg = '{oversize string}';
							$arg = '\'' . $arg . '\'';
						}
						elseif(is_array($arg))
						{
							$arg = 'Array';
						}
						elseif(is_object($arg))
						{
							$arg = 'Object ' . get_class($arg);
						}
						$args .= '<span style="color: #007700; font-weight: bold;">,</span> <span style="color: #0000BB;">' . $arg . '</span>';
					}
				}
			}
			else
			{
				$args = '';
			}

			$callback = (isset($trace['class']) ? $trace['class'] . '<span style="color: #007700; font-weight: bold;">' . $trace['type'] . '</span>' : '') . '<span style="color: #0000BB; font-weight: bold;">' . $trace['function'] . '</span><span style="color: #007700; font-weight: bold;">(</span>' . $args . '<span style="color: #007700; font-weight: bold;">)</span>';
			$return[] = <<<EOD
				<li style="padding-left: 0px;">
					<span style="font-weight: bold;">#{$id}</span><br />
					<span style="padding-left: 20px;">callback: {$callback}</span><br /><br />
					<span style="padding-left: 20px;">on line <span style="font-weight: bold;">{$trace['line']}</span> of file: <span style="font-weight: bold; font-family: monospace; background: #ffffff; color: #007700; padding: 0 3px; border: solid 1px #007700;">{$trace['file']}</span></span>

				</li>
EOD;
		}
		$return[] = '</ol>';
		return join($return);
	}
}
