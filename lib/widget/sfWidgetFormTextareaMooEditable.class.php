<?php
/**
 * sfWidgetFormTextareaMooEditable represents a MooEditable widget.
 * 
 * You can override the Javascripts and Stylesheets if required - included from plugin
 *
 * @package    symfony
 * @subpackage widget
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: sfWidgetFormTextareaMooEditable.class.php 17192 2009-04-10 07:58:29Z jocarter $
 */
class sfWidgetFormTextareaMooEditable extends sfWidgetFormTextarea
{
  /**
   * Constructor.
   *
   * Available options: (defaults set in plugin app.yml)
   *
   *  * config:           Additional MooEditable configuration
   *  * width:            The width of the editable area
   *  * height:           The height of the editable area
   *  * extratoolbar:     Any additional toolbar options - include | to separate
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
  	$this->addOption('width', sfConfig::get('app_mooeditable_default_width'));
  	$this->addOption('height', sfConfig::get('app_mooeditable_default_height'));
    $this->addOption('config', sfConfig::get('app_mooeditable_default_config'));
    $this->addOption('extratoolbar', sfConfig::get('app_mooeditable_default_extra_toolbar'));
    
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
    $textarea = parent::render($name, $value, $attributes, $errors);
    
    $extraOptions = $this->getOption('extratoolbar');
    if (!empty($extraOptions)) $extraOptions .= ' |';
    
    $js = sprintf(<<<EOF
<script type="text/javascript">
  window.addEvent('load', function(){
    $('%s').mooEditable( { 
      dimensions: { x: %s, y: %s },
      actions: '%s | %s toggleview',
      baseCSS: '%s',
      %s
    } );
  });
</script>
EOF
     ,
      $this->generateId($name),
      $this->getOption('width'),
      $this->getOption('height'),
      sfConfig::get('app_mooeditable_base_toolbar'),
      $extraOptions,
      sfConfig::get('app_mooeditable_base_css'),
      $this->getOption('config')
     );
     
     return $textarea.$js;
  }
  
  
  /**
   * Include MooEditable Javascript
   * 
   * Requires MooTools.More:
   *  More/Class.Refactor 
   *  More/Locale 
   *  
   * @return string[]
   */
  public function getJavaScripts() 
  {
    $js = array('/sfMooToolsFormExtraPlugin/js/MooEditable/MooEditable.js');
    
    if (sfConfig::get('app_mooeditable_include_clean_paste'))
    {
      $js[] = '/sfMooToolsFormExtraPlugin/js/MooEditable/MooEditable.CleanPaste.js';
    }
    
    $extra_js = sfConfig::get('app_mooeditable_extra_js');
    
    if (!empty($extra_js)) $js += $extra_js;
    
    return $js;
  }

  
  /**
   * Include MooEditable Stylesheet
   * 
   * @return string[]
   */
  public function getStylesheets()
  {
    $css = array('/sfMooToolsFormExtraPlugin/css/MooEditable/MooEditable.css' => 'screen');
    
    $extra_css = sfConfig::get('app_mooeditable_extra_css');
    
    if (!empty($extra_css)) $css += $extra_css;
    
    return $css;
  }
}
