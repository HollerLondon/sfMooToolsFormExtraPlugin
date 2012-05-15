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
The [mootools:publish-assets|INFO] task will (re)publish web assets from the sfMooToolsFormExtraPlugin.

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

    $fileSystem = $this->getFilesystem();
      
    // create web dir for plugin + js and css dirs (will ignore if already created)
    $jsDir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'js';
    $cssDir = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'css';
    
    $fileSystem->mkdirs($jsDir);
    $fileSystem->mkdirs($cssDir);
    
    $this->logSection('plugin', sprintf('Configured %s directory in web folder, with appropriate structure', $plugin));
    
    // Go through all plugins - and link up those that exist
    $pluginCount = 0;
    $externals   = array('Datepicker' => '', 'MooEditable' => 'MooEditable', 'mooRainbow' => '');
    
    foreach ($externals as $external => $subDir)
    {
      $vendorDir = $libVendorDir.DIRECTORY_SEPARATOR.$external.DIRECTORY_SEPARATOR;
      
      // check vendor folder exist
      if (is_dir($vendorDir.'Source') && is_dir($vendorDir.'Assets'))
      {
        // create symlinks inside
        $this->getFilesystem()->relativeSymlink($vendorDir.'Source'.('' != $subDir ? DIRECTORY_SEPARATOR.$subDir : ''),                                      
                                                $jsDir.DIRECTORY_SEPARATOR.$external, true);
        
        $this->getFilesystem()->relativeSymlink($vendorDir.'Assets'.('' != $subDir ? DIRECTORY_SEPARATOR.$subDir : ''),                                      
                                                $cssDir.DIRECTORY_SEPARATOR.$external, true);

        $pluginCount++;
        $this->logSection('plugin', 'Configured js and css for '. $external);
      }
    }
    
    if (0 == $pluginCount)
    {
      $this->logSection('plugin', 'Please ensure you have set up approriate links in the lib/vendor folder as per the README', null, 'ERROR');
    }
  }
}
