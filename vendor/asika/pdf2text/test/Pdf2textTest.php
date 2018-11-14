<?php
/**
 * Part of pdf2text project. 
 *
 * @license  GNU General Public License version 2 or later;
 */

use Asika\Pdf2text;

/**
 * Class TestPdf2text
 */
class Pdf2TextTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Property instance.
	 *
	 * @var  Pdf2Text
	 */
	protected $instance = null;

	/**
	 * setUp
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$this->instance = new Pdf2text;
	}

	/**
	 * testConvert2Test
	 *
	 * @return  void
	 */
	public function testDecodePDF()
	{
		$output = $this->instance->decode(__DIR__ . '/test.pdf');

		$text = <<<TXT
Nick Fury: You think you
’
re the only hero in the world?

TXT;
		$output = str_replace(array("\n", "\r"), '', trim($output));
		$text   = str_replace(array("\n", "\r"), '', trim($text));

		$this->assertEquals($output, $text);
	}

	/**
	 * testDecodeContent
	 *
	 * @return  void
	 */
	public function testDecodeContent()
	{
		$output = $this->instance->decodeContent(file_get_contents(__DIR__ . '/test.pdf'));

		$text = <<<TXT
Nick Fury: You think you
’
re the only hero in the world?

TXT;
		$output = str_replace(array("\n", "\r"), '', trim($output));
		$text   = str_replace(array("\n", "\r"), '', trim($text));

		$this->assertEquals($output, $text);
	}
}
