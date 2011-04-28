<?php
/**
 * Enhances the sfValidatorString to allow for HTML strings and stripping 
 * tags from length calculations
 * 
 * @package    symfony
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfEnhancedValidatorString.class.php 30762 2010-08-25 12:33:33Z jocarter $
 */
class sfEnhancedValidatorString extends sfValidatorString 
{
	protected function doClean($value) 
	{
    $clean = (string) $value;
    $noHtml = strip_tags(html_entity_decode($clean, ENT_QUOTES, 'utf-8'));

    $length = function_exists('mb_strlen') ? mb_strlen($noHtml, $this->getCharset()) : strlen($noHtml);

    if ($this->hasOption('max_length') && $length > $this->getOption('max_length')) {
      throw new sfValidatorError($this, 'max_length', array('value' => $value, 'max_length' => $this->getOption('max_length'), 'current_length'=>$length));
    }

    if ($this->hasOption('min_length') && $length < $this->getOption('min_length')) {
      throw new sfValidatorError($this, 'min_length', array('value' => $value, 'min_length' => $this->getOption('min_length'), 'current_length'=>$length));
    }

    return $clean;
  }
}