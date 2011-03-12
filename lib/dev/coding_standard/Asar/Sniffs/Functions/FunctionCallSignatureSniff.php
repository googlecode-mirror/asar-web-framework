<?php
require_once 'PHP/CodeSniffer/Standards/PEAR/Sniffs/' .
  'Functions/FunctionCallSignatureSniff.php';

class Asar_Sniffs_Functions_FunctionCallSignatureSniff 
  extends PEAR_Sniffs_Functions_FunctionCallSignatureSniff
{


    function processMultiLineCall(
      PHP_CodeSniffer_File $phpcsFile, $stackPtr, $openBracket, $tokens
    ) {
        // We need to work out how far indented the function
        // call itself is, so we can work out how far to
        // indent the arguments.
        $functionIndent = 0;
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
                $i++;
                break;
            }
        }

        if ($tokens[$i]['code'] === T_WHITESPACE) {
            $functionIndent = strlen($tokens[$i]['content']);
        }

        // Each line between the parenthesis should be indented 4 spaces.
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $lastLine     = $tokens[$openBracket]['line'];
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            // Skip nested function calls.
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $i        = $tokens[$i]['parenthesis_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            if ($tokens[$i]['line'] !== $lastLine) {
                $lastLine = $tokens[$i]['line'];

                // We changed lines, so this should be a whitespace indent
                // token.
                $_test = in_array(
                  $tokens[$i]['code'], PHP_CodeSniffer_Tokens::$heredocTokens
                );
                if ($_test === true) {
                    // Ignore heredoc indentation.
                    continue;
                }
                
                $_test = in_array(
                  $tokens[$i]['code'], PHP_CodeSniffer_Tokens::$stringTokens
                );
                if ($_test === true) {
                    if ($tokens[$i]['code'] === $tokens[($i - 1)]['code']) {
                        // Ignore multi-line string indentation.
                        continue;
                    }
                }

                if ($tokens[$i]['line'] === $tokens[$closeBracket]['line']) {
                    // Closing brace needs to be indented to the same level
                    // as the function call.
                    $expectedIndent = $functionIndent;
                } else {
                    $expectedIndent = ($functionIndent + 2);
                }

                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    $foundIndent = 0;
                } else {
                    $foundIndent = strlen($tokens[$i]['content']);
                }

                if ($expectedIndent !== $foundIndent) {
                    $error = 'Multi-line function call not indented ' .
                      "correctly; expected $expectedIndent spaces but found " .
                      "$foundIndent";
                    $phpcsFile->addError($error, $i);
                }
            }//end if
        }//end for

        if ($tokens[($openBracket + 1)]['content'] !== $phpcsFile->eolChar) {
            $error = 'Opening parenthesis of a multi-line function call must ' .
              'be the last content on the line';
            $phpcsFile->addError($error, $stackPtr);
        }

        $prev = $phpcsFile->findPrevious(
          T_WHITESPACE, ($closeBracket - 1), null, true
        );
        if ($tokens[$prev]['line'] === $tokens[$closeBracket]['line']) {
            $error = 'Closing parenthesis of a multi-line function call must ' .
              'be on a line by itself';
            $phpcsFile->addError($error, $closeBracket);
        }

    }//end processMultiLineCall()


}//end class

