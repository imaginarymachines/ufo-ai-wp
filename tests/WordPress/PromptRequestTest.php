<?php

use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\PromptRequest;

//Class that tests PromptRequest class
//All tests are in the form of public function test*()
class PromptRequstTest extends WP_UnitTestCase {

	//Test that the toArray function works
	public function test_toArray() {
		$what = 'words';
		$for = [];
		$that = [];
		$length = 5;
		$promptRequest = new PromptRequest($what, $for, $that, $length);
		$this->assertEquals([
			PromptRequest::WHAT => $what,
			PromptRequest::FOR => $for,
			PromptRequest::THAT => $that,
			PromptRequest::LENGTH => $length,
			PromptRequest::N => 1
		],$promptRequest->toArray());
	}

	//Test that the fromArray function works
	public function test_fromArray() {
		$what = 'words';
		//valid "for" keys
		$expectFor = [
			'type' => 'blog post',
			'title' => 'a title',
			'content' => 'a words lorewm ipsum',
		];
		//merge in an invalid
		$for = array_merge(
			$expectFor,
			[
				'notAllowed' => 'tacos in space suits',
			]
		);

		$expectThat = [
			'style' => 'style',
			'genre' => 'genre',
			'about' => 'about',
		];
		$that = array_merge($expectThat,[
			'notAllowed' => 'notAllowed',
		]);
		$length = 5;
		$promptRequest = PromptRequest::fromArray(
			[
				PromptRequest::WHAT => $what,
				PromptRequest::FOR => $for,
				PromptRequest::THAT => $that,
				PromptRequest::LENGTH => $length,
			]
		);
		file_put_contents('prompt-request.json',json_encode($promptRequest->toArray()));

		//Test the getters
		$this->assertEquals(
			$what,
			$promptRequest->getWhat(),
		);
		$this->assertEquals(
			$expectFor,
			$promptRequest->getFor(),
		);
		$this->assertEquals(
			$expectThat,
			$promptRequest->getThat(),
		);
		$this->assertEquals(
			$length,
			$promptRequest->getLength(),
		);

		//That and for can be empty arrays
		$promptRequest = PromptRequest::fromArray(
			[
				PromptRequest::WHAT => $what,
				PromptRequest::FOR => [],
				PromptRequest::THAT => [],
				PromptRequest::LENGTH => $length,
			]
		);
		$this->assertEquals(
			[],
			$promptRequest->getThat(),
		);
		$this->assertEquals(
			[],
			$promptRequest->getFor(),
		);
	}

	//Only valid values of what are allowed
	public function test_fromArray_invalidWhat() {
		$this->expectException(\InvalidArgumentException::class);
		PromptRequest::fromArray(
			[
				PromptRequest::WHAT => 'invalid',
				PromptRequest::FOR => [],
				PromptRequest::THAT => [],
				PromptRequest::LENGTH => 3,
			]
		);
	}

	//valid values of  are allowed
	public function test_valid_What() {

		$this->assertSame('words', (new PromptRequest(
			'words',
			[],
			[],
			3
		))->getWhat());
	}

	//valid values of  are allowed
	public function test_fromArray_valid_What() {

		$this->assertSame('words', PromptRequest::fromArray(
			[
				PromptRequest::WHAT => 'words',
				PromptRequest::FOR => [],
				PromptRequest::THAT => [],
				PromptRequest::LENGTH => 3,

			]
		)->getWhat());
	}
}
