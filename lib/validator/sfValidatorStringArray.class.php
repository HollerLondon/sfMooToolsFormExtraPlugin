<?php
/**
 * Handle array of strings
 * @author Jo Carter <jocarter@holler.co.uk>
 *
 */
class sfValidatorStringArray extends sfValidatorString
{
/**
   * Configures the current validator.
   *
   * additional options:
   * 
   *  num_required - how many of them are required?  use with required: true to extend - see README
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorString
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
    
    $this->addMessage('num_required', 'At least %num_required% options must be filled in');
    $this->addOption('num_required');
  }
  
  /**
   * Override definition to deal with array of empty strings
   * 
   * @see sfValidatorBase::isEmpty()
   */
  public function isEmpty($value)
  {
    $emptyValues = 0;
    
    foreach ($value as $test)
    {
      $isEmpty = parent::isEmpty($test);
      if ($isEmpty) $emptyValues++;
    }
    
    // If all empty
    if ($emptyValues == count($value))
    {
      $emptyArray = array();
      
      for ($i = 0; $i < $emptyValues; $i++)
      {
        $emptyArray[] = '';
      }
      
      // Need to make sure empty value set to correct number of array items
      $this->setOption('empty_value', $emptyArray);
      
      return true;
    }
    else return false;
  }
  
  /**
   * @param $value is array of strings
   */
  protected function doClean($value)
  {
    $errors = array();
    $emptyValues = array();
    
    if (!is_array($value))
    {
      $value = array($value);
    }
    
    foreach ($value as $idx => $rawValue)
    {
      try
      {
        if (!is_int($idx)) var_dump($idx);
        $rawValue = (string) $rawValue;
        
        // If empty - count
        if (empty($rawValue)) $emptyValues[$idx] = $idx;
        
        $value[$idx] = parent::doClean($rawValue);
      }
      catch (sfValidatorError $e)
      {
        $errors[$idx] = $e;
      }
    }
    
    // Do we have the required number of values?
    $num_required = $this->getOption('num_required');
    
    if ($num_required)
    {
      $num_remaining = count($value) - count($emptyValues);
      
      if ($num_required != $num_remaining)
      {
        throw new sfValidatorError($this, 'num_required', array('value' => $value, 'num_required' => $this->getOption('num_required')));
      }
    }
    
    // Errors will be the min/max length ones - required handled elsewhere
    if (!empty($errors))
    {
      // Unless required and num required error not thrown
      if ($num_required && !empty($emptyValues))
      {
        foreach ($errors as $idx => $error)
        {
          if (in_array($idx, $emptyValues)) unset($errors[$idx]);
        }
      }
      
      if (!empty($errors)) 
      {
        $error = array_slice($errors, 0, 1);
        throw $error[0]; // throw the first error
      }
    }
    
    return $value;
  }
}