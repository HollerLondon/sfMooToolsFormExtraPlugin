<?php
/**
 * Unit test for the sfValidatorStringArray
 * 
 * Extends sfValidatorString - but modifies functionality to deal with arrays of strings
 * The original sfValidatorString tests are run, but compared against array output to
 * check that normal functionality is not affected
 * 
 * @package   sfMooToolsFormExtraPlugin
 * @author    Jo Carter <jocarter@holler.co.uk>
 */

require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(34);

$v = new sfValidatorStringArray();

// The sfStringValidator tests - 12
// ->clean()
$t->diag('->clean()');
$t->is($v->clean(array('foo')), array('foo'), '->clean() returns the array unmodified');

$v->setOption('required', false);
$t->ok($v->clean(array(null)) === array(''), '->clean() converts the value to a string in an array');
$t->ok($v->clean(array(1)) === array('1'), '->clean() converts the value to a string in an array');

$v->setOption('max_length', 2);
$t->is($v->clean(array('fo')), array('fo'), '->clean() checks the maximum length allowed');
try
{
  $v->clean(array('foo'));
  $t->fail('"max_length" option set the maximum length of the string');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"max_length" option set the maximum length of the string');
  $t->is($e->getCode(), 'max_length', '->clean() throws a sfValidatorError');
}

$v->setMessage('max_length', 'Too long');
try
{
  $v->clean(array('foo'));
  $t->fail('"max_length" error message customization');
}
catch (sfValidatorError $e)
{
  $t->is($e->getMessage(), 'Too long', '"max_length" error message customization');
}

$v->setOption('max_length', null);

$v->setOption('min_length', 3);
$t->is($v->clean(array('foo')), array('foo'), '->clean() checks the minimum length allowed');
try
{
  $v->clean(array('fo'));
  $t->fail('"min_length" option set the minimum length of the string');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"min_length" option set the minimum length of the string');
  $t->is($e->getCode(), 'min_length', '->clean() throws a sfValidatorError');
}

$v->setMessage('min_length', 'Too short');
try
{
  $v->clean(array('fo'));
  $t->fail('"min_length" error message customization');
}
catch (sfValidatorError $e)
{
  $t->is($e->getMessage(), 'Too short', '"min_length" error message customization');
}

$v->setOption('min_length', null);

$t->diag('UTF-8 support');
if (!function_exists('mb_strlen'))
{
  $t->skip('UTF-8 support needs mb_strlen');
}
else
{
  $v->setOption('max_length', 4);
  $t->is($v->clean(array('été')), array('été'), '"sfValidatorString" supports UTF-8');
}


// Array tests - 22
$t->comment('Array tests');

$v->setOption('num_required', 2);
$t->comment('"num_required": 2');

try 
{
  $v->clean(array('',''));
  $t->pass('"num_required" option only validated when "required" is false if array is not empty');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->fail('"num_required" option only validated when "required" is false if array is not empty');
  $t->is($e->getCode(), 'num_required', '->clean() throws a sfValidatorError');
}

$v->setOption('min_length', 2);

try 
{
  $v->clean(array('',''));
  $t->pass('"min_length" option not validated if "required" is false and array is empty - standard sfValidatorBase behaviour');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->fail('"min_length" option not validated if "required" is false and array is empty - standard sfValidatorBase behaviour');
  $t->is($e->getCode(), 'min_length', '->clean() throws a sfValidatorError');
}

try 
{
  $v->clean(array('foo','b',''));
  $t->fail('"min_length" + "num_required" validates the number required of strings');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"min_length" + "num_required" validates the number required of strings');
  $t->is($e->getCode(), 'min_length', '->clean() throws a sfValidatorError');
}

$t->comment('"required": true');
$v->setOption('required', true);
$v->setOption('min_length', null);
$v->setOption('num_required', null);

try 
{
  $v->clean(array());
  $t->fail('"required" option doesn\'t allow empty array');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"required" option doesn\'t allow empty array');
  $t->is($e->getCode(), 'required', '->clean() throws a sfValidatorError');
}

try 
{
  $v->clean(array('', '', ''));
  $t->fail('"required" option doesn\'t allow array with empty values');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"required" option doesn\'t allow array with empty values');
  $t->is($e->getCode(), 'required', '->clean() throws a sfValidatorError');
}

try 
{
  $v->clean(array('foot', '', ''));
  $t->pass('"required" option is happy as long as there\'s one entry');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->fail('"required" option is happy as long as there\'s one entry');
  $t->is($e->getCode(), 'required', '->clean() throws a sfValidatorError');
}

$v->setOption('num_required', 2);

try 
{
  $v->clean(array('foo'));
  $t->fail('"num_required" option requires 2 array indexes');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"num_required" option requires 2 non-empty array indexes');
  $t->is($e->getCode(), 'num_required', '->clean() throws a sfValidatorError');
}

try 
{
  $v->clean(array('foo','bar'));
  $t->pass('"num_required" option requires 2 array indexes');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->fail('"num_required" option requires 2 array indexes');
  $t->is($e->getCode(), 'num_required', '->clean() throws a sfValidatorError');
}

try 
{
  $v->clean(array('foo',''));
  $t->fail('"num_required" option requires 2 array indexes - not empty ones');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"num_required" option requires 2 array indexes - not empty ones');
  $t->is($e->getCode(), 'num_required', '->clean() throws a sfValidatorError');
}

try 
{
  $v->clean(array('foo','bar',''));
  $t->pass('"num_required" option requires 2 array indexes - others can be empty');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->fail('"num_required" option requires 2 array indexes - others can be empty');
  $t->is($e->getCode(), 'num_required', '->clean() throws a sfValidatorError');
}

$v->setOption('min_length', 2);

try 
{
  $v->clean(array('foo','bar',''));
  $t->pass('"min_length" + "required" validates only the non-empty "num_required" of values');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->fail('"min_length" + "required" validates only the non-empty "num_required" of values');
  $t->is($e->getCode(), 'min_length', '->clean() throws a sfValidatorError');
}