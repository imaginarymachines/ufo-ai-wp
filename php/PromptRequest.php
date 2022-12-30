<?php

namespace ImaginaryMachines\ContentMachine;

/**
 * Represents a request to the prompt API
 */
class PromptRequest {

	const WHAT = 'what';

	const FOR = 'for';

	const THAT = 'that';

	const LENGTH = 'length';

	const N = 'n';

	// properties for each of the constants
	/**
	 * What to generate
	 *
	 * @var string
	 */
	protected string $what;

	/**
	 * For what
	 *
	 * @var array
	 */
	protected array $for;

	/**
	 * That
	 *
	 * @var array
	 */
	protected array $that;

	/**
	 * Length of the generated text
	 *
	 * @var int
	 */
	protected int $length;

	/**
	 * Number of options to genrate
	 *
	 * @var int
	 */
	protected int $n;

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


	public function __construct( string $what, array $for, array $that, $length = 1, $n = 1 ) {
		// images not supported yet
		if ( ! static::isValidWhat( $what ) ) {
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
		$this->n      = absint( $n );
	}

	/**
	 * Test if what is a valid value
	 */
	public static function isValidWhat( $what ) {
		return in_array( $what, static::getValidWhats() );
	}

	/**
	 * Get valid values for what
	 *
	 * @return array
	 */
	public static function getValidWhats() :array {
		return array( 'words', 'sentences', 'paragraphs' );
	}

	/**
	 * Get default what value
	 *
	 * @return string
	 */
	public static function getDefaultWhat() :string {
		return 'paragraphs';
	}

	/**
	 * Get what
	 * @return string
	 */
	public function getWhat() {
		return $this->what;
	}

	/**
	 * Get for
	 * @return array
	 */
	public function getFor() {
		return $this->for;
	}

	/**
	 * Get that
	 *
	 * @return array
	 */
	public function getThat() {
		return $this->that;
	}

	/**
	 * Get length
	 *
	 * @return int
	 */
	public function getLength() {
		return $this->length;
	}

	/**
	 * Get N property
	 *
	 * @return int
	 */
	public function getN() {
		return $this->n;
	}

	/**
	 * Set N property
	 *
	 * @param int $n
	 *
	 * @return PromptRequest
	 */
	public function setN( $n ) {
		$this->n = absint( $n );
		return $this;
	}

	/**
	 * Set length
	 *
	 * @param int $length
	 *
	 * @return PromptRequest
	 */
	public function setLength( $length ) {
		$this->length = absint( $length );
		return $this;
	}

	/**
	 * Convert to array
	 *
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			self::WHAT   => $this->getWhat(),
			self::FOR    => $this->getFor(),
			self::THAT   => $this->getThat(),
			self::LENGTH => $this->getLength(),
			self::N      => $this->getN(),
		);
	}
	/**
	 * Create a new PromptRequest object from an array
	 *
	 * @param array $data
	 *
	 * @throws \InvalidArgumentException if $data is not valid
	 */
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
	 *
	 * @param array $data
	 * @param string $type
	 * @throws \InvalidArgumentException if $type is not valid
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
