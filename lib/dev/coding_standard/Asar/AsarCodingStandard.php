<?php

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
  throw new PHP_CodeSniffer_Exception(
    'Class PHP_CodeSniffer_Standards_CodingStandard not found'
  );
}

class PHP_CodeSniffer_Standards_Asar_AsarCodingStandard
  extends PHP_CodeSniffer_Standards_CodingStandard 
{
  function getIncludedSniffs() {
    return array(
      'Generic/Sniffs/Functions/OpeningFunctionBraceKernighanRitchieSniff.php',
      'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php',
      'Generic/Sniffs/Files/LineLengthSniff.php',
      'Generic/Sniffs/NamingConventions/ConstructorNameSniff.php',
      'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
      'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php',
      'PEAR/Sniffs/Files/LineEndingsSniff.php',
      'PEAR/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php',
      'PEAR/Sniffs/Functions/ValidDefaultValueSniff.php',
      'Squiz/Sniffs/Functions/GlobalFunctionSniff.php',
      
      // No closing php tag at the end of the file
      // Spacing after function declaration (Asar_View source file)
    );
  }
}

