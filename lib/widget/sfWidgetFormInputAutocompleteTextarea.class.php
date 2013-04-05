<?php
class sfWidgetFormInputAutocompleteTextarea extends sfWidgetFormTextarea
{
/**
   * Available options:  (defaults set in plugin app.yml)
   * 
   * * url:              The url to create the auto complete with, this will return a JSON array (this will need to be created separately)      
   * * multiple:         Can multiple tags be selected?  Or just one (default: see app.yml)
   * * use_slots:        Set javascript in a slot rather than rendering - see README
   * 
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addRequiredOption('url');
    $this->addOption('multiple', sfConfig::get('app_autocomplete_allow_multiple', true));
    $this->addOption('use_slots', sfConfig::get('app_autocomplete_use_slots', false));
  }
   
  /**
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (is_array($value))
    {
      $value = implode(', ', $value);
    }
    
    $input = parent::render($name, $value, $attributes, $errors);
    
    $js = sprintf(<<<EOF
    <script type="text/javascript">
      new Autocompleter.Request.JSON($('%s'), '%s', {
        'indicatorClass': 'autocompleter-loading',
        'multiple':     %s,
        'selectFirst':  true,
        'selectMode':   true,
        'minLength':    %s
      });
    </script>
EOF
      ,
      $this->generateId($name),
      $this->getOption('url'),
      ($this->getOption('multiple') ? 'true' : 'false'),
      sfConfig::get('app_autocomplete_min_length', 2)
    );
    
    return $this->renderAutocomplete($input, $js);
  }
  
  /**
   * Include Autocompleter Javascripts
   * 
   * Requires MooTools.Core
   *  
   * @return string[]
   */
  public function getJavaScripts() 
  {
    return array(
      '/sfMooToolsFormExtraPlugin/js/Autocompleter/Observer.js', 
      '/sfMooToolsFormExtraPlugin/js/Autocompleter/Autocompleter.js', 
      '/sfMooToolsFormExtraPlugin/js/Autocompleter/Autocompleter.Request.js'
    );
  }
   
  /**
   * Include Autocompleter Stylesheets
   * 
   * @return string[]
   */
  public function getStylesheets()
  {
    return array('/sfMooToolsFormExtraPlugin/css/Autocompleter/Autocompleter.css' => 'screen');
  }
  
  /**
   * Renders the autocomplete widget itself
   * 
   * @param string $input The form field
   * @param string $js The accompanying JavaScript
   * @return string
   */
  public function renderAutocomplete($input, $js)
  {
    if ($this->getOption('use_slots'))
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));
      slot('autocomplete_js',get_slot('autocomplete_js').$js);
      return $input; 
    }
    else
    {
      return $input.$js;
    }
  }
} 