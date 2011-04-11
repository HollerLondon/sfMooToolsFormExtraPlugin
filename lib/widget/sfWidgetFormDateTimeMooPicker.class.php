<?php
/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormDateTimePicker represents a series of HTML select tags with an attached Javscript Datepicker.
 * 
 * Additional Javascript and Stylesheets can be added as required
 *
 * @package    symfony
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfWidgetFormDateTimeMooPicker.class.php 30762 2010-08-25 12:33:33Z fabien $
 */
class sfWidgetFormDateTimeMooPicker extends sfWidgetForm
{
  /**
   * Constructor.
   * 
   * NOTE: Default locale is en-GB, and date defaults have to match DB format to validate
   *
   * Available options:
   *
   * * locale: defaults to en-GB - if this is changed, will require additional locale JS files
   * * min_date: default is none, set to restrict date range (format: Y-m-d)
   * * max_date: default is none, set to restrict date range (format: Y-m-d)
   * * date_time_widget: The datetime widget to render with the calendar
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
  	$this->addOption('locale', 'en-GB');
    $this->addOption('min_date', 'null');
    $this->addOption('max_date', 'null');
    $this->addOption('date_time_widget', new sfWidgetFormDateTime(array('date' => array('format'=>'%day% %month% %year%'), 'time'=> array('format'=>'%hour% %minute%'))));
    
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
  	if (!isset($attributes['style'])) $attributes['style'] = 'width:auto !important;';
  	
    $input = $this->getOption('date_time_widget')->render($name, $value, $attributes, $errors);
    
    $input .= $this->renderTag('input', array('type' => 'hidden', 'size' => 10, 'id' => $this->generateId($name), 'disabled' => 'disabled', 'value'=>$value));
    
    $default_date_format = '%Y-%m-%d %H:%M';
    
    $js = sprintf(<<<EOF
<script type="text/javascript">
  Locale.use('%s');
  new Picker.Date($('%s'), {
    format: '%s',
    timePicker: true,
    yearPicker: true,
    minDate: '%s',
    maxDate: '%s',
    toggle: $('%s_control'),
    positionOffset: {x: 5, y: 0},
    pickerClass: 'datepicker_dashboard',
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
      $this->generateId($name),
      $default_date_format,
      $this->getOption('min_date'),
      $this->getOption('max_date'),
      $this->generateId($name),
      $this->generateId($name),
      $this->generateId($name),
      $this->generateId($name),
      $this->generateId($name),
      $this->generateId($name)
     );
     
     $toggle = '<img src="/sfMooToolsFormExtraPlugin/css/Datepicker/datepicker_dashboard/icon_calendar.gif" class="datepicker_calendar" alt="Calendar" id="'.$this->generateId($name).'_control" />';
     
     return $input.$toggle.$js;
  }


  /**
   * Include Datepicker Javascript
   * 
   * Requires MooTools.More:
   *  More/Date 
   *  More/Date.Extras 
   *  More/Locale 
   *  More/Locale.en-GB.Date
   */
  public function getJavaScripts() 
  {
    return array(
            //'/sfMooToolsFormExtraPlugin/js/mootools-more.js',
            '/sfMooToolsFormExtraPlugin/js/Datepicker/Picker.js',
            '/sfMooToolsFormExtraPlugin/js/Datepicker/Picker.Attach.js',
            '/sfMooToolsFormExtraPlugin/js/Datepicker/Picker.Date.js',
            '/sfMooToolsFormExtraPlugin/js/Datepicker/Locale.en-GB.DatePicker.js'
    );
  }

  
  /**
   * Include Datepicker Stylesheet
   */
  public function getStylesheets()
  {
    return array('/sfMooToolsFormExtraPlugin/css/Datepicker/datepicker_dashboard/datepicker_dashboard.css' => 'screen');
  }
}