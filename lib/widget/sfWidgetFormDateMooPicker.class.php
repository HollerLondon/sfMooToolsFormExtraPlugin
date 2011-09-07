<?php
/**
 * sfWidgetFormDateMooPicker represents a series of HTML select tags with an attached Javscript Datepicker.
 * 
 * Additional Javascript and Stylesheets can be added as required
 *
 * @package    sfMooToolsFormExtraPlugin
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfWidgetFormDateMooPicker.class.php 30762 2010-08-25 12:33:33Z jocarter $
 */
class sfWidgetFormDateMooPicker extends BaseWidgetMooPicker
{
  /**
   *  All of the options defined in BaseWidgetMooPicker, plus:
   *
   * * date_widget:     The date widget to render with the calendar
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

    $this->addOption('date_widget', new sfWidgetFormDate(array('format'=>sfConfig::get('app_datepicker_default_date_display_format'))));
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
    $default_date_format = '%Y-%m-%d';
    
  	if (!isset($attributes['style'])) $attributes['style'] = 'width:auto !important;';
  	
    $input = $this->getOption('date_widget')->render($name, $value, $attributes, $errors);
    
    // Check that supplied default is in the DB date format - and parse correctly if supplied as a Unix timestamp
    if (!empty($value))
    {
      if (is_int($value)) $value = date('Y-m-d', $value);
    }
    
    $input .= $this->renderTag('input', array('type' => 'hidden', 'size' => 10, 'id' => $this->generateId($name), 'disabled' => 'disabled', 'value'=>$value));
    
    $js = sprintf(<<<EOF
<script type="text/javascript">
window.addEvent('domready', function ()
{
  Locale.use('%1\$s');
  new Picker.Date($('%2\$s'), {
    format: '%3\$s',
    timePicker: false,
    yearPicker: %4\$s,
    minDate: '%5\$s',
    maxDate: '%6\$s',
    toggle: $('%2\$s_control'),
    positionOffset: {x: 5, y: 0},
    pickerClass: '%7\$s',
    useFadeInOut: !Browser.ie,
    onSelect: function(date){
        $('%2\$s_day').set('value', date.get('date'));
        $('%2\$s_month').set('value', date.get('month') + 1); // month starts at 0
        $('%2\$s_year').set('value', date.get('year'));
    }
  });
});
</script>
EOF
     ,
      $this->getOption('locale'),
      $this->generateId($name),  // target element
      $default_date_format,
      $this->getOption('year_picker'),
      $this->getOption('min_date'),
      $this->getOption('max_date'),
      sfConfig::get('app_datepicker_picker_class')
     );
     
     $toggle = sprintf('<img src="%s/%s/icon_calendar.gif" class="datepicker_calendar" alt="Calendar" id="%s_control" />',
                sfConfig::get('app_datepicker_base_css_location'),
                sfConfig::get('app_datepicker_picker_class'),
                $this->generateId($name));
     
     return $this->renderDatePicker($input, $js,$toggle);
  }
}