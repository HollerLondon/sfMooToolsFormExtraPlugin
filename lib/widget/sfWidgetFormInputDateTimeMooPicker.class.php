<?php
/**
 * sfWidgetFormDateTimeMooPicker represents an HTML input tag with an attached Javscript Datepicker.
 * 
 * Additional Javascript and Stylesheets can be added as required
 *
 * @package    sfMooToolsFormExtraPlugin
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfWidgetFormInputDateTimeMooPicker.class.php 30762 2010-08-25 12:33:33Z jocarter $
 */
class sfWidgetFormInputDateTimeMooPicker extends BaseWidgetMooPicker
{
  /**
   * All of the options defined in BaseWidgetMooPicker, plus:
   *
   * * date_format:     The JavaScript format of the date in the input box (defaults to %Y-%m-%d - see below) - see http://mootools.net/docs/more/Types/Date#Date:format.
   *                    If this is changed should be paired with appropriate sfValidatorDate and regex - see README. 
   *                    Ensure includes time if below option is 'true'
   * * php_date_format: If the date_format for display is changed to a more user friendly format than %Y-%m-%d - the value needs to be converted from the database format
   *                    This field should contain the corresponding PHP date_format for use with date() - see http://uk.php.net/manual/en/function.date.php
   * * with_time:       defaults to 'false', include time in the date picker (date format defaults to Y-m-d H:i instead of Y-m-d)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see BaseWidgetMooPicker::configure()
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('date_format', null);
    $this->addOption('php_date_format', null);
    $this->addOption('with_time', 'false');
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
window.addEvent('domready', function ()
{
  Locale.use('%1\$s');
  new Picker.Date($('%2\$s'), {
    format: '%3\$s',
    timePicker: %4\$s,
    yearPicker: %5\$s,
    minDate: '%6\$s',
    maxDate: '%7\$s',
    positionOffset: {x: 5, y: 0},
    pickerClass: '%8\$s',
    useFadeInOut: !Browser.ie
  });
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

     return $this->renderDatePicker($input, $js);
  }
}