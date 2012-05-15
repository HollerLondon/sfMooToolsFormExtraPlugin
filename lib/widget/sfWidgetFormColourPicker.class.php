<?php
/**
 * Colour picker widget
 * 
 * @package    sfMooToolsFormExtraPlugin
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 */
class sfWidgetFormColourPicker extends sfWidgetFormInput
{
  /**
   * Constructor
   * 
   * Available options:  (defaults set in plugin app.yml)
   * 
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
    
    $this->addOption('use_slots', sfConfig::get('app_colourpicker_use_slots', false));
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
    $input    = parent::render($name, $value, $attributes, $errors);
    $inputId  = $this->generateId($name);
    
    $startValue = (!empty($value) ? $value : sfConfig::get('app_colourpicker_default_colour', '#ffffff'));
    
    if (!function_exists('hex2RGB')) 
    {
      sfApplicationConfiguration::getActive()->loadHelpers(array('Colour'));
    }
    
    $toggle = sprintf(' <img src="%s" alt="" width="16" height="16" class="rain" id="mooRainbow_%s" />',
                      sfConfig::get('app_colourpicker_picker_icon'),
                      $inputId);
    
    $js = sprintf(<<<EOF
  <script type="text/javascript">
    window.addEvent('load', function()
    {
      new MooRainbow('mooRainbow_%1\$s', {
        id: 'mooRainbowSelector_%1\$s',
        wheel: %5/$s,
        imgPath: '%3\$s',
        startColor: [ %2\$s ],
        onChange: function(color) {
          $$('body').setStyle('background-color', color.hex);
          $('%1\$s').value = color.hex;
        },
        onComplete: function(color) {
          $$('body').setStyle('background-color', '%4\$s');
          $('%1\$s').value = color.hex;
        }
      });
    });
  </script>
EOF
        , 
        $inputId,
        hex2RGB($startValue, true, ", "),
        sfConfig::get('app_colourpicker_image_path'),
        sfConfig::get('app_colourpicker_default_background_colour', '#eeeeee'),
        sfConfig::get('app_colourpicker_allow_scroll', true )
    );
    
    return $this->renderColourPicker($input, $js, $toggle);
  }
  
  /**
   * Include Colourpicker Javascripts
   * 
   * Requires MooTools.Core AND MooTools.More:
   *  More/Slider 
   *  More/Drag
   *  More/Color
   *  
   * @return string[]
   */
  public function getJavaScripts() 
  {
    return array('/sfMooToolsFormExtraPlugin/js/mooRainbow/mooRainbow.js');
  }
   
  /**
   * Include Colourpicker Stylesheets
   * 
   * @return string[]
   */
  public function getStylesheets()
  {
    return array('/sfMooToolsFormExtraPlugin/css/mooRainbow/mooRainbow.css' => 'screen');
  }
  
  /**
   * Renders the colourpicker widget  itself
   * 
   * @param string $input The form field
   * @param string $js The accompanying JavaScript
   * @param string $toggle The toggler button
   * @return string
   */
  protected function renderColourPicker($input = NULL, $js = NULL, $toggle = NULL)
  {
    if ($this->getOption('use_slots'))
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));
      slot('colour_picker_js', get_slot('colour_picker_js').$js);
      return $input.$toggle; 
    }
    else
    {
      return $input.$toggle.$js;
    }
  }
}