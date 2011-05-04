<?php
/**
 * sfWidgetFormDateTimePicker represents a series of HTML select tags with an attached Javscript Datepicker.
 * 
 * Additional Javascript and Stylesheets can be added as required
 *
 * @package    symfony
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfWidgetFormDateTimeMooPicker.class.php 30762 2010-08-25 12:33:33Z jocarter $
 */
class sfWidgetFormDateTimeMooPicker extends sfWidgetForm
{
  /**
   * Constructor.
   * 
   * NOTE: Default locale is en-GB (in plugin app.yml), and date defaults have to match DB format to validate with sfValidatorDate
   *
   * Available options:
   *
   * * locale:            if this is changed from the default, will require additional JS locale files
   * * year_picker:       defaults to true, click on the month name twice to select year - if date range restricted within one year then set to 'false'
   * * min_date:          default is none, set to restrict date range (format: Y-m-d)
   * * max_date:          default is none, set to restrict date range (format: Y-m-d)
   * * date_time_widget:  The datetime widget to render with the calendar
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
  	$this->addOption('locale', sfConfig::get('app_datepicker_default_locale'));
  	$this->addOption('year_picker', 'true');
    $this->addOption('min_date', 'null');
    $this->addOption('max_date', 'null');
    $this->addOption('date_time_widget', new sfWidgetFormDateTime(array('date' => array('format'=>sfConfig::get('app_datepicker_default_date_display_format')), 
                                                                        'time'=> array('format'=>sfConfig::get('app_datepicker_default_time_display_format')))));
    
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
                
     return $input.$toggle.$js;
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
                    sfConfig::get('app_datepicker_js_locale_location'),
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