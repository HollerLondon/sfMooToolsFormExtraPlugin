<?php
/**
 * Core functionality for MooTools widgets
 *
 * @package sfMooToolsFormExtra
 * @subpackage Widget
 * @author  Ben Lancaster <benlancaster@holler.co.uk>
 */
abstract class BaseWidgetMooPicker extends sfWidgetFormInput
{
  /**
   * Constructor.
   * 
   * NOTE: Default locale is en-GB (in plugin app.yml), and date defaults have to match DB format to validate with sfValidatorDate
   * 
   * Available options:  (defaults set in plugin app.yml)
   *
   * * locale:          if this is changed from the default, will require additional JS locale files
   * * year_picker:     defaults to 'true', click on the month name twice to select year - if date range restricted within one year then set to 'false'
   * * min_date:        default is none, set to restrict date range (format: see above)
   * * max_date:        default is none, set to restrict date range (format: see above)
   * * use_slots:       Set javascript in a slot rather than rendering - see README
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('locale', sfConfig::get('app_datepicker_default_locale'));
    $this->addOption('year_picker', 'true');
    $this->addOption('min_date', 'null');
    $this->addOption('max_date', 'null');
    $this->addOption('use_slots', sfConfig::get('app_datepicker_use_slots', false));
    
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
  
  /**
   * Renders the date picker widget  itself
   * 
   * @param string $input The form field
   * @param string $js The accompanying JavaScript
   * @param string $toggle The toggler button
   * @return string
   */
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