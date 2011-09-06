<?php
/**
 * Core functionality for MooTools widgets
 *
 * @package sfMooToolsFormExtra
 * @subpackage Widget
 * @author  Ben Lancaster <benlancaster@holler.co.uk>
 */
abstract class BaseWidgetMooPicker extends  sfWidgetFormInput
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
    parent::configure($options,$attributes);
    
    $this->addOption('locale', sfConfig::get('app_datepicker_default_locale'));
    $this->addOption('year_picker', 'true');
    $this->addOption('min_date', 'null');
    $this->addOption('max_date', 'null');
    $this->addOption('use_slots',sfConfig::get('app_datepicker_use_slots',false));
    
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
  
  protected function renderDatePicker($input = NULL, $js = NULL, $toggle = NULL)
  {
    if($this->getOption('use_slots'))
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
  
} // END