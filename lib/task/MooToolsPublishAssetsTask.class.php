<?php
/**
 * Publishes Web Assets for the sfMooToolsFormExtraPlugin
 *
 * @package    sfMooToolsFormExtraPlugin
 * @subpackage task
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: MooToolsPublishAssetsTask.class.php 23922 2009-11-14 14:58:38Z jocarter $
 */
class MooToolsPublishAssetsTask extends sfPluginBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
    ));

    $this->addOptions(array(
    ));

    $this->namespace = 'mootools';
    $this->name = 'publish-assets';

    $this->briefDescription = 'Publishes web assets for the sfMooToolsFormExtraPlugin';

    $this->detailedDescription = <<<EOF
The [mootools:publish-assets|INFO] task will publish web assets from the sfMooToolsFormExtraPlugin.

  [./symfony mootools:publish-assets|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $pluginConfiguration = $this->configuration->getPluginConfiguration('sfMooToolsFormExtraPlugin');

    $this->logSection('plugin', 'Configuring plugin - sfMooToolsFormExtraPlugin');
    $this->installPluginAssets('sfMooToolsFormExtraPlugin', $pluginConfiguration->getRootDir());
  }

  /**
   * Installs web content for the plugin.
   *
   * @param string $plugin The plugin name
   * @param string $dir    The plugin directory
   */
  protected function installPluginAssets($plugin, $dir)
  {
    // NOTE: Depending on how the plugin is installed - the lib/vendor folder could be within the project or the plugin
    // Check the project first (as the plugin may contain stubs from the git submodules via the SVN bridge)
    $symfonyLibDir = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'vendor';
    
    if (is_dir($symfonyLibDir.DIRECTORY_SEPARATOR.'Datepicker'))
    {
      $libVendorDir = $symfonyLibDir;
    }
    else 
    {
      $libVendorDir = $dir.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'vendor';
    }

    // check both vendor folders exist
    if (is_dir($libVendorDir.DIRECTORY_SEPARATOR.'Datepicker'.DIRECTORY_SEPARATOR.'Source') && is_dir($libVendorDir.DIRECTORY_SEPARATOR.'MooEditable'.DIRECTORY_SEPARATOR.'Source'))
    {
      $fileSystem = $this->getFilesystem();
      
      // create web dir for plugin + js and css dirs
      $jsDir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'js';
      $cssDir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'css';
      $fileSystem->mkdirs($jsDir);
      $fileSystem->mkdirs($cssDir);
      
      // create symlinks inside
      $this->getFilesystem()->relativeSymlink($libVendorDir.DIRECTORY_SEPARATOR.'Datepicker'.DIRECTORY_SEPARATOR.'Source',                                      
                                              $jsDir.DIRECTORY_SEPARATOR.'Datepicker', true);
      $this->getFilesystem()->relativeSymlink($libVendorDir.DIRECTORY_SEPARATOR.'MooEditable'.DIRECTORY_SEPARATOR.'Source'.DIRECTORY_SEPARATOR.'MooEditable',   
                                              $jsDir.DIRECTORY_SEPARATOR.'MooEditable', true);
      
      $this->getFilesystem()->relativeSymlink($libVendorDir.DIRECTORY_SEPARATOR.'Datepicker'.DIRECTORY_SEPARATOR.'Assets',                                      
                                              $cssDir.DIRECTORY_SEPARATOR.'Datepicker', true);
      $this->getFilesystem()->relativeSymlink($libVendorDir.DIRECTORY_SEPARATOR.'MooEditable'.DIRECTORY_SEPARATOR.'Assets'.DIRECTORY_SEPARATOR.'MooEditable',   
                                              $cssDir.DIRECTORY_SEPARATOR.'MooEditable', true);
                                              
      $this->logSection('plugin', 'Plugin configured');
    }
    else
    {
      $this->logSection('plugin', 'Please ensure you have set up approriate links in the lib/vendor folder as per the README', null, 'ERROR');
    }
  }
}
