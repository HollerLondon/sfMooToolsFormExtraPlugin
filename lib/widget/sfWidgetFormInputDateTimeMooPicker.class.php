<?php
/**
 * sfWidgetFormDateTimeMooPicker represents an HTML input tag with an attached Javscript Datepicker.
 * 
 * Additional Javascript and Stylesheets can be added as required
 *
 * @package    symfony
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfWidgetFormInputDateTimeMooPicker.class.php 30762 2010-08-25 12:33:33Z jocarter $
 */
class sfWidgetFormInputDateTimeMooPicker extends sfWidgetFormInput
{
  /**
   * Constructor.
   * 
   * NOTE: Default locale is en-GB (in plugin app.yml), and date defaults have to match DB format to validate with sfValidatorDate
   * 
   * Available options:
   *
   * * locale:          if this is changed from the default, will require additional JS locale files
   * * date_format:     The JavaScript format of the date in the input box (defaults to %Y-%m-%d - see below) - see http://mootools.net/docs/more/Types/Date#Date:format.
   *                    If this is changed should be paired with appropriate sfValidatorDate and regex - see README. 
   *                    Ensure includes time if below option is 'true'
   * * php_date_format: If the date_format for display is changed to a more user friendly format than %Y-%m-%d - the value needs to be converted from the database format
   *                    This field should contain the corresponding PHP date_format for use with date() - see http://uk.php.net/manual/en/function.date.php
   * * with_time:       defaults to 'false', include time in the date picker (date format defaults to Y-m-d H:i instead of Y-m-d)
   * * year_picker:     defaults to 'true', click on the month name twice to select year - if date range restricted within one year then set to 'false'
   * * min_date:        default is none, set to restrict date range (format: see above)
   * * max_date:        default is none, set to restrict date range (format: see above)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('locale', sfConfig::get('app_datepicker_default_locale'));
    $this->addOption('date_format', null);
    $this->addOption('php_date_format', null);
    $this->addOption('with_time', 'false');
    $this->addOption('year_picker', 'true');
    $this->addOption('min_date', 'null');
    $this->addOption('max_date', 'null');
    
    parent::configure($options, $attributes);
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
    $date_format = $this->getOption('date_format');
    
    if (is_null($date_format)) 
    {
      $date_format = ('true' == $this->getOption('with_time') ? '%Y-%m-%d %H:%M' : '%Y-%m-%d');
    }
    else 
    {
      // $value needs to be converted from Y-m-d into new format for display
      $php_date_format = $this->getOption('php_date_format');
      
      // If not supplied try to convert from JS format - NOT RECOMMENDED
      if (is_null($php_date_format)) $php_date_format = str_replace('%','',$date_format);
      
      $value = date($php_date_format, strtotime($value));
    }
    
    if (!isset($attributes['style'])) $attributes['style'] = 'width:auto !important;';
    
    $input = parent::render($name, $value, $attributes, $errors);
    
    $js = sprintf(<<<EOF
<script type="text/javascript">
  Locale.use('%s');
  new Picker.Date($('%s'), {
    format: '%s',
    timePicker: %s,
    yearPicker: %s,
    minDate: '%s',
    maxDate: '%s',
    positionOffset: {x: 5, y: 0},
    pickerClass: '%s',
    useFadeInOut: !Browser.ie
  });
</script>
EOF
     ,
      $this->getOption('locale'),
      $this->generateId($name),
      $date_format,
      $this->getOption('with_time'),
      $this->getOption('year_picker'),
      $this->getOption('min_date'),
      $this->getOption('max_date'),
      sfConfig::get('app_datepicker_picker_class')
     );
     
     return $input.$js;
  }


  /**
   * Include Datepicker Javascripts
   * 
   * Requires MooTools.Core AND MooTools.More:
   *  More/Date 
   *  More/Date.Extras 
   *  More/Locale 
   *  More/Locale.[REQUIRED_LOCALE(S)].Date
   *  
   * @return string[]
   */
  public function getJavaScripts() 
  {
    $localeJs = sprintf('%s/Locale.%s.DatePicker.js',
                    sfConfig::get('app_datepick_js_locale_location'),
                    $this->getOption('locale'));
    
    return array(
            '/sfMooToolsFormExtraPlugin/js/Datepicker/Picker.js',
            '/sfMooToolsFormExtraPlugin/js/Datepicker/Picker.Attach.js',
            '/sfMooToolsFormExtraPlugin/js/Datepicker/Picker.Date.js',
            $localeJs
    );
  }

  
  /**
   * Include Datepicker Stylesheet
   * 
   * @return string[]
   */
  public function getStylesheets()
  {
    $cssFile = sprintf('%s/%s/%s.css', 
                sfConfig::get('app_datepicker_base_css_location'),
                sfConfig::get('app_datepicker_picker_class'),
                sfConfig::get('app_datepicker_picker_class'));
    
    return array($cssFile => 'screen');
  }
}