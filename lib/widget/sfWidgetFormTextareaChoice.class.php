<?php
/**
 * Class to handle arrays of inputs for doctrine type array
 * @author Jo Carter <jocarter@holler.co.uk>
 *
 */
class sfWidgetFormTextareaChoice extends sfWidgetFormChoiceBase
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->setAttribute('rows', 4);
    $this->setAttribute('cols', 30);
    
    parent::configure($options, $attributes);
  }
  
  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget // NOT USED
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ('[]' != substr($name, -2))
    {
      $name .= '[]';
    }

    $choices = $this->getChoices();

    return $this->formatChoices($name, $value, $choices, $attributes);
  }

  /**
   * Choices should be sent through as an array of pairs 'label' => 'value'
   */
  protected function formatChoices($name, $value, $choices, $attributes)
  {
    $inputs = array();
    $key    = 0;
    $values = $value;
    
    foreach ($choices as $label => $value)
    {
      // If we've bound the form and there are input, use those instead of the predefined "options"
      if (isset($values[$key]) && !empty($values[$key])) $value = $values[$key];
      
      $baseAttributes = array(
        'id'    => $id = $this->generateId($name, self::escapeOnce($key))
      );

      $inputs[$id] = array(
        'input' => $this->renderContentTag('textarea', self::escapeOnce($value), array_merge(array('name' => $name), array_merge($baseAttributes, $attributes))),
        'label' => $this->renderContentTag('label', $label, array('for' => $id, 'style'=>'float: left !important;'))
      );
      
      $key++;
    }
    
    return $this->formatter($this, $inputs);
  }

  public function formatter($widget, $inputs)
  {
    $rows = array();
    foreach ($inputs as $input)
    {
      $rows[] = $this->renderContentTag('div', $input['label'] . $this->renderContentTag('div', $input['input'], array('class'=>'content')), array('class'=>'sf_admin_form_row', 'style'=>'clear:none;'));
    }

    return !$rows ? '' : implode($this->getOption('separator'), $rows);
  }
}