<?php
/**
 * sfWidgetFormDateTimePicker represents a series of HTML select tags with an attached Javscript Datepicker.
 * 
 * Additional Javascript and Stylesheets can be added as required
 *
 * @package    sfMooToolsFormExtraPlugin
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfWidgetFormDateTimeMooPicker.class.php 30762 2010-08-25 12:33:33Z jocarter $
 */
class sfWidgetFormDateTimeMooPicker extends BaseWidgetMooPicker
{
  /**
   * All of the options defined in BaseWidgetMooPicker, plus:
   *
   * * date_time_widget:  The datetime widget to render with the calendar
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

    $this->addOption('date_time_widget', new sfWidgetFormDateTime(array(
      'date' => array('format'=>sfConfig::get('app_datepicker_default_date_display_format')), 
      'time'=> array('format'=>sfConfig::get('app_datepicker_default_time_display_format')))
    ));
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
    $default_date_format = '%Y-%m-%d %H:%M';
    
  	if (!isset($attributes['style'])) $attributes['style'] = 'width:auto !important;';
  	
    $input = $this->getOption('date_time_widget')->render($name, $value, $attributes, $errors);
    
    // Check that supplied default is in the DB date format - and parse correctly if supplied as a Unix timestamp
    if (!empty($value))
    {
      if (is_int($value)) $value = date('Y-m-d H:i:s', $value);
    }
    
    $input .= $this->renderTag('input', array('type' => 'hidden', 'size' => 10, 'id' => $this->generateId($name), 'disabled' => 'disabled', 'value'=>$value));
    
    $js = sprintf(<<<EOF
<script type="text/javascript">
  Locale.use('%s');
  new Picker.Date($('%s'), {
    format: '%s',
    timePicker: true,
    yearPicker: %s,
    minDate: '%s',
    maxDate: '%s',
    toggle: $('%s_control'),
    positionOffset: {x: 5, y: 0},
    pickerClass: '%s',
    useFadeInOut: !Browser.ie,
    onSelect: function(date){
        $('%s_day').set('value', date.get('date'));
        $('%s_month').set('value', date.get('month') + 1); // month starts at 0
        $('%s_year').set('value', date.get('year'));
        
        $('%s_hour').set('value', date.get('hours'));
        $('%s_minute').set('value', date.get('minutes'));
    }
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
      $this->generateId($name),  // toggle calendar control
      sfConfig::get('app_datepicker_picker_class'),
      $this->generateId($name),  // day
      $this->generateId($name),  // month
      $this->generateId($name),   // year
      $this->generateId($name),  // hours
      $this->generateId($name)   // minutes
     );
     
     $toggle = sprintf('<img src="%s/%s/icon_calendar.gif" class="datepicker_calendar" alt="Calendar" id="%s_control" />',
                sfConfig::get('app_datepicker_base_css_location'),
                sfConfig::get('app_datepicker_picker_class'),
                $this->generateId($name));
     
     if($this->getOption('use_slots',FALSE))
     {
       sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));
       slot('date_picker_js',get_slot('date_picker_js').$js);
       return $input.$toggle;
     }
     else
     {
       return $input.$toggle.$js;
     }
     
  }
}