<?php
/**
 * WordPressVIPMinimum_Sniffs_Files_RestrictedFunctionsSniff.
 *
 * @package VIPCS\WordPressVIPMinimum
 */

namespace WordPressVIPMinimum\Sniffs\JS;

use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * WordPressVIPMinimum_Sniffs_JS_RestrictedFunctionsSniff.
 *
 * Looks for incorrect way of stripping tags.
 *
 * @package VIPCS\WordPressVIPMinimum
 */
class RestrictedFunctionsSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = array(
		'JS',
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING,
		);

	}//end register()

	/**
	 * Returns an array of of groups of functions we are looking for.
	 *
	 * @return array
	 */
	public function get_groups() {
		return array(
			'eval' => array(
				'type'      => 'error',
				'message'   => '%s is a security risk so not allowed.',
				'functions' => array( 'eval' ),
			),
			'obfuscation' => array(
				'type'      => 'warning',
				'message'   => '%s is often used for obfuscation. Requires manual inspection.',
				'functions' => array(
					'fromCharCode',
					'toString',
					'charCodeAt',
				),
			),
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token in the
	 *                                               stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		foreach( $this->get_groups() as $name => $group ) {
			if ( true === in_array( $tokens[ $stackPtr ]['content'], $group['functions'] ) ) {
				if ( 'error' === $group['type'] ) {
					$phpcsFile->addError( sprintf( $group['message'], $tokens[ $stackPtr ]['content'] ), $stackPtr, $name );
				} else {
					$phpcsFile->addWarning( sprintf( $group['message'], $tokens[ $stackPtr ]['content'] ), $stackPtr, $name );
				}
			}
		}

	}//end process()

}//end class
