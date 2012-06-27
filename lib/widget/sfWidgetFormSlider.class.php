<?php
class sfWidgetFormSlider extends sfWidgetFormInput
{
  /**
   * Constructor
   * 
   * Available options:  (defaults set in plugin app.yml)
   * 
   * * start_value:      Start value of the range for the slider - set on a slider by slider basis
   * * end_value:        End value of the range for the slider
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
    
    $this->addRequiredOption('start_value');
    $this->addRequiredOption('end_value');
    
    $this->addOption('use_slots', sfConfig::get('app_slider_use_slots', false));
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
    $start    = $this->getOption('start_value');
    $end      = $this->getOPtion('end_value');
    
    $input    = parent::render($name, ($value ?: $start), array_merge(array('type'=>'hidden'), $attributes), $errors);
    $inputId  = $this->generateId($name);
    
    $toggle = sprintf('<div id="%s_slider" class="slider" style="%s">
                        <div class="knob" style="%s"></div>
                      </div>',
                      $inputId,
                      sfConfig::get('app_slider_div_style'),
                      sfConfig::get('app_slider_knob_style'));
    
    $js = sprintf(<<<EOF
  <script type="text/javascript">
    window.addEvent('load', function()
    {
      new Slider($('%1\$s_slider'), $('%1\$s_slider').getElement('.knob'), {
        range:       [%2\$s, %3\$s],
        initialStep: %4\$s,
        steps:       %5\$s,
        snap:        true,
        onChange: function(value)
        {
          $('%1\$s').set('value', value);
        }
      });
    });
  </script>
EOF
        , 
        $inputId,
        $start,
        $end,
        ($value ?: $start),
        ($end - $start) + 2 // the range plus 1 either end for start and finish
    );
    
    return $this->renderSlider($input, $js, $toggle);
  }
  
  /**
   * Include Slider Javascripts
   * 
   * Requires MooTools.Core AND MooTools.More:
   *  More/Slider 
   *  More/Drag
   *  
   * @return string[]
   */
  public function getJavaScripts() 
  {
    return array();
  }
   
  /**
   * Include Slider Stylesheets
   * 
   * @return string[]
   */
  public function getStylesheets()
  {
    return array();
  }
  
  /**
   * Renders the slider widget itself
   * 
   * @param string $input The form field
   * @param string $js The accompanying JavaScript
   * @param string $toggle The toggler button
   * @return string
   */
  protected function renderSlider($input = NULL, $js = NULL, $toggle = NULL)
  {
    if ($this->getOption('use_slots'))
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));
      slot('slider_js', get_slot('slider_js') . $js);
      return $input.$toggle; 
    }
    else
    {
      return $input.$toggle.$js;
    }
  }
}