<?php
/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormDateTimeMooPicker represents an HTML input tag with an attached Javscript Datepicker.
 * 
 * Additional Javascript and Stylesheets can be added as required
 *
 * @package    symfony
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfWidgetFormInputDateTimeMooPicker.class.php 30762 2010-08-25 12:33:33Z fabien $
 */
class sfWidgetFormInputDateTimeMooPicker extends sfWidgetFormInput
{
  /**
   * Constructor.
   * 
   * NOTE: Default locale is en-GB, and date defaults have to match DB format to validate
   * date_format: defaults to Y-m-d, see http://mootools.net/docs/more/Types/Date#Date:format
   *
   * Available options:
   *
   * * locale: defaults to en-GB - if this is changed, will require additional locale JS files
   * * with_time: include time in the date picker (date format defaults to Y-m-d H:i instead of Y-m-d)
   * * min_date: default is none, set to restrict date range (format: same as the date_format)
   * * max_date: default is none, set to restrict date range (format: same as the date_format)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
  	$this->addOption('locale', 'en-GB');
    $this->addOption('with_time', 'false');
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
  	if (!isset($attributes['style'])) $attributes['style'] = 'width:auto !important;';
  	
    $input = parent::render($name, $value, $attributes, $errors);
    
    $default_date_format = ('true' == $this->getOption('with_time') ? '%Y-%m-%d %H:%M' : '%Y-%m-%d');
    
    $js = sprintf(<<<EOF
<script type="text/javascript">
  Locale.use('%s');
  new Picker.Date($('%s'), {
    format: '%s',
    timePicker: %s,
    yearPicker: true,
    minDate: '%s',
    maxDate: '%s',
    positionOffset: {x: 5, y: 0},
    pickerClass: 'datepicker_dashboard',
    useFadeInOut: !Browser.ie
  });
</script>
EOF
     ,
      $this->getOption('locale'),
      $this->generateId($name),
      $default_date_format,
      $this->getOption('with_time'),
      $this->getOption('min_date'),
      $this->getOption('max_date')
     );
     
     return $input.$js;
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