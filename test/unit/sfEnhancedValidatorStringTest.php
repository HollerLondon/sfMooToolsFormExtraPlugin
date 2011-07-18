<?php
/**
 * Unit test for the sfEnhancedValidatorString
 * 
 * Extends sfStringValidator so these tests are duplicated to ensure that this functionality has not been compromised
 * 
 * @package   sfMooToolsFormExtraPlugin
 * @author    Jo Carter <jocarter@holler.co.uk>
 */

require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(20);

$v = new sfEnhancedValidatorString();

// The sfStringValidator tests - 12
// ->clean()
$t->diag('->clean()');
$t->is($v->clean('foo'), 'foo', '->clean() returns the string unmodified');

$v->setOption('required', false);
$t->ok($v->clean(null) === '', '->clean() converts the value to a string');
$t->ok($v->clean(1) === '1', '->clean() converts the value to a string');

$v->setOption('max_length', 2);
$t->is($v->clean('fo'), 'fo', '->clean() checks the maximum length allowed');
try
{
  $v->clean('foo');
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
  $v->clean('foo');
  $t->fail('"max_length" error message customization');
}
catch (sfValidatorError $e)
{
  $t->is($e->getMessage(), 'Too long', '"max_length" error message customization');
}

$v->setOption('max_length', null);

$v->setOption('min_length', 3);
$t->is($v->clean('foo'), 'foo', '->clean() checks the minimum length allowed');
try
{
  $v->clean('fo');
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
  $v->clean('fo');
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
  $t->is($v->clean('été'), 'été', '"sfValidatorString" supports UTF-8');
}


// Enhanced tests - 8
$t->diag('enhanced clean()->');
$t->is($v->clean('<p>foo</p>'), '<p>foo</p>', '->clean() returns the string with HTML');
$t->is($v->clean('<p>foo&quot;</p>'), '<p>foo&quot;</p>', '->clean() returns the string with HTML and entities (QUOTES)');
$t->is($v->clean('<p>foo&amp;</p>'), '<p>foo&amp;</p>', '->clean() returns the string with HTML and entities (AMPERSAND)');

$v->setOption('max_length', 3);

$t->is($v->clean('<p>foo</p>'), '<p>foo</p>', '->clean() ignores the HTML when max_length set');
$t->is($v->clean('<p>fo&quot;</p>'), '<p>fo&quot;</p>', '->clean() ignores the HTML and entities (QUOTES) when max_length set');
$t->is($v->clean('<p>fo&amp;</p>'), '<p>fo&amp;</p>', '->clean() returns the string with HTML and entities (AMPERSAND) when max_length set');

$v->setMessage('max_length', 'Current length: %current_length%, max length: %max_length%');

try
{
  $v->clean('<p>food</p>');
  $t->fail('"max_length" error message customization with lengths, ignores HTML');
}
catch (sfValidatorError $e)
{
  $t->is($e->getMessage(), 'Current length: 4, max length: 3', '"max_length" error message customization with lengths, ignores HTML');
}

$v->setOption('max_length', null);

$v->setOption('min_length', 2);
$v->setMessage('min_length', 'Too short');

try
{
  $v->clean('<p>f</p>');
  $t->fail('"min_length" error ignores HTML');
}
catch (sfValidatorError $e)
{
  $t->is($e->getMessage(), 'Too short', '"min_length" error ignores HTML');
}

$v->setOption('min_length', null);
