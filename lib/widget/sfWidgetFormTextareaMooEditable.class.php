<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
   * Available options:
   *
   *  * config: Additional MooEditable configuration
   *  * width: The width of the editable area, defaults to 500px
   *  * height: The height of the editable area, defaults to 200px
   *  * extratoolbar: Any additional toolbar options - include | to separate, by default this contains 'urlimage'
   *                  for image insertion
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
    $this->addOption('config', '');
    $this->addOption('extratoolbar', 'urlimage');
    
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
      actions: 'bold italic underline | insertunorderedlist insertorderedlist | undo redo | createlink unlink | %s toggleview',
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
   *  More/Locale.en-GB.Date
   */
  public function getJavaScripts() 
  {
    $js = array('/sfMooToolsFormExtraPlugin/js/MooEditable/MooEditable.js');
    
    if (sfConfig::get('app_mooeditable_include_clean_paste'))
    {
      $js[] = '/sfMooToolsFormExtraPlugin/js/MooEditable/MooEditable.CleanPaste.js';
    }
    
    return $js;
  }

  
  /**
   * Include MooEditable Stylesheet
   */
  public function getStylesheets()
  {
    return array('/sfMooToolsFormExtraPlugin/css/MooEditable/MooEditable.css' => 'screen');
  }
}
