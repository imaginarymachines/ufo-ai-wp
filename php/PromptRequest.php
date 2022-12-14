<?php
namespace ImaginaryMachines\ContentMachine;

class PromptRequest {

	const WHAT = 'what';

	const FOR = 'for';

	const THAT = 'that';

	const LENGTH = 'length';


	// properties for each of the constants
	protected $what;
	protected array $for;
	protected array $that;
	protected int $length;

	/**
	 * Keys that are allowed in the 'that' array
	 */
	protected $thatKeys = array(
		'style',
		'genre',
		'about',
	);

	/**
	 * Keys that are allowed in the 'for' array
	 */
	protected $forKeys = array(
		'type',
		'title',
		'content',
	);


	public function __construct( $what, array $for, array $that, $length ) {
		// images not supported yet
		if ( ! in_array( $what, array( 'words', 'sentences', 'paragraphs' ) ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'Invalid what: %s',
					$what
				)
			);
		}
		$this->what   = $what;
		$this->for    = $this->filterArray( $for, static::FOR );
		$this->that   = $this->filterArray( $that, static::THAT );
		$this->length = absint( $length );
	}

	public function getWhat() {
		return $this->what;
	}

	public function getFor() {
		return $this->for;
	}

	public function getThat() {
		return $this->that;
	}

	public function getLength() {
		return $this->length;
	}

	public function toArray() {
		return array(
			self::WHAT   => $this->getWhat(),
			self::FOR    => $this->getFor(),
			self::THAT   => $this->getThat(),
			self::LENGTH => $this->getLength(),
		);
	}
	// from array
	public static function fromArray( array $data ) {
		// validate array has all the keys
		if ( ! array_key_exists( self::WHAT, $data ) || ! array_key_exists( self::FOR, $data ) || ! array_key_exists( self::THAT, $data ) || ! array_key_exists( self::LENGTH, $data ) ) {
			throw new \InvalidArgumentException( 'Invalid data' );
		}

		$for  = is_array( $data[ self::FOR ] ) ? $data[ self::FOR ] : array();
		$that = is_array( $data[ self::THAT ] ) ? $data[ self::THAT ] : array();
		// create new object
		$obj = new static( $data[ self::WHAT ], $for, $that, $data[ self::LENGTH ] );
		return $obj;
	}

	/**
	 * Remove keys from $data that are not in $allowedKeys
	 */
	protected function filterArray( array $data, $type ) {
		$optional = array(
			static::FOR  => array(
				'content',
			),
			static::THAT => $this->thatKeys,
		);
		$optional = $optional[ $type ];
		switch ( $type ) {
			case static::THAT:
				$allowedKeys = $this->thatKeys;
				break;
			case static::FOR:
				$allowedKeys = $this->forKeys;
				break;
			default:
				throw new \InvalidArgumentException( 'Invalid type' );
				break;

		}
		$prepared = array();
		foreach ( $allowedKeys as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				$prepared[ $key ] = $data[ $key ];
			}
		}
		return $prepared;
	}
}
