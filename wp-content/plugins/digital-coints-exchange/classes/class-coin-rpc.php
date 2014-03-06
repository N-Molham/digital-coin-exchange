<?php
/**
 * Coin RPC Class
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/*
Based on EasyBitcoin-PHP

A simple class for making calls to Bitcoin's API using PHP.
https://github.com/aceat64/EasyBitcoin-PHP
*/

class DCE_Coin_RPC 
{
	// Configuration options
	private $username;
	private $password;
	private $proto;
	private $host;
	private $port;
	private $uri;
	private $CACertificate;

	// Information and debugging
	public $status;
	public $error;
	public $raw_response;
	public $response;

	private $id = 0;

	// HTTP errors
	static $http_codes = array ( 
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Switch Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			425 => 'Unordered Collection',
			426 => 'Upgrade Required',
			449 => 'Retry With',
			450 => 'Blocked by Windows Parental Controls',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			509 => 'Bandwidth Limit Exceeded',
			510 => 'Not Extended'
	);

	/**
	 * @param string $username
	 * @param string $password
	 * @param string $host
	 * @param int $port
	 * @param string $proto
	 * @param string $uri
	 */
	function __construct( $username, $password, $host, $port, $uri = null ) 
	{
		$this->username	  = $username;
		$this->password	  = $password;
		$this->host		  = $host;
		$this->port		  = $port;
		$this->uri		   = $uri;

		// Set some defaults
		$this->proto		 = 'http';
		$this->CACertificate = null;
	}

	/**
	 * @param string|null $certificate
	 */
	function setSSL( $certificate = null ) 
	{
		$this->proto		 = 'https'; // force HTTPS
		$this->CACertificate = $certificate;
	}

	function __call( $method, $params ) 
	{
		$this->status	   = null;
		$this->error		= null;
		$this->raw_response = null;
		$this->response	 = null;

		// If no parameters are passed, this will be an empty array
		$params = array_values( $params );

		// The ID should be unique for each call
		$this->id++;

		// Build the request, it's ok that params might have any empty array
		$request = json_encode( array (
				'method' => $method,
				'params' => $params,
				'id'	 => $this->id
		) );

		// Build the cURL session
		$curl = curl_init( "{$this->proto}://{$this->username}:{$this->password}@{$this->host}:{$this->port}/{$this->uri}" );
		$options = array (
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_MAXREDIRS	  => 10,
			CURLOPT_HTTPHEADER	 => array('Content-type: application/json'),
			CURLOPT_POST		   => TRUE,
			CURLOPT_POSTFIELDS	 => $request
		);

		if ( $this->proto == 'https' ) 
		{
			// If the CA Certificate was specified we change CURL to look for it
			if ( $this->CACertificate != null ) 
			{
				$options[CURLOPT_CAINFO] = $this->CACertificate;
				$options[CURLOPT_CAPATH] = DIRNAME($this->CACertificate);
			}
			else 
			{
				// If not we need to assume the SSL cannot be verified so we set this flag to FALSE to allow the connection
				$options[CURLOPT_SSL_VERIFYPEER] = false;
			}
		}

		curl_setopt_array( $curl, $options );

		// Execute the request and decode to an array
		$this->raw_response = curl_exec( $curl );
		$this->response	 = json_decode( $this->raw_response, TRUE );

		// If the status is not 200, something is wrong
		$this->status = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

		// If there was no error, this will be an empty string
		$curl_error = curl_error( $curl );

		// close connection
		curl_close( $curl );

		// curl errors
		if ( !empty( $curl_error ) )
		{
			$this->error = $curl_error;
			return new WP_Error( 'error-curl', $curl_error );
		}

		// check for error
		if ( isset( $this->response['error'] ) && is_array( $this->response['error'] ) ) 
		{
			// If xcoin returned an error, put that in $this->error
			$this->error = $this->response['error']['message'];

			// return error
			return new WP_Error( $this->response['error']['code'], $this->response['error']['message'] );
		}
		elseif ( $this->status != 200 ) 
		{
			// If xcoin didn't return a nice error message, we need to make our own
			if ( isset( self::$http_codes[ $this->status ] ) )
				return new WP_Error( $this->status, self::$http_codes[ $this->status ] );
			else
				return new WP_Error( $this->status, 'Unknown error' );
		}

		return $this->response['result'];
	}
}