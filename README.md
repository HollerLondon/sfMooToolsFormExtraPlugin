sfMooToolsFormExtraPlugin
=========================

@author:    Jo Carter (<jocarter@holler.co.uk>)

@version:   1.0


Introduction
------------

This symfony plugin contains several widgets (and a validator) for use with the [MooTools](http://mootools.net/) javascript framework.

All default configuration is controlled in plugins/sfMooToolsFormExtraPlugin/config/app.yml.

See *Setup* for publishing assets.


Credits:
--------

* [MooEditable](http://mootools.net/forge/p/mooeditable) - Lim Chee Aun
* [Datepicker](http://mootools.net/forge/p/mootools_datepicker) - Arian Stolwijk


Pre-requisites
--------------

### PHP ###

 * PHP 5.2.4 or later
 * symfony 1.3/1.4

### Javascript ###

 * MooTools Core 1.3+
 * Custom version of MooTools.More on a project level
   * Datepicker requires MooTools.More:
     * More/Date 
     * More/Date.Extras 
     * More/Locale 
     * More/Locale.en-GB.Date
   * MooEditable requires MooTools.More:
     * More/Class.Refactor 
     * More/Locale 
 * MooEditable and Datepicker in lib/vendor - see *Setup*


Setup
-----

*NOTE*: Using [custom fork of Mooditable](https://github.com/angelsk/mooeditable) for convenience.  
*NOTE*: Using [custom fork for Datepicker](https://github.com/angelsk/mootools-datepicker) as file structure changed, also added 'datepicker_light' theme (default) with calendar icon - meshes better with symfony


### Note for SVN

If using SVN you will need to add this plugin and its dependancies (custom forks) as svn:externals

      sfMooToolsFormExtraPlugin                                 https://svn.github.com/HollerLondon/sfMooToolsFormExtraPlugin.git
      sfMooToolsFormExtraPlugin/lib/vendor/Datepicker           https://svn.github.com/angelsk/mootools-datepicker.git
      sfMooToolsFormExtraPlugin/lib/vendor/MooEditable          https://svn.github.com/angelsk/mooeditable.git


### Note for Git

The lib/vendor folder contains submodules for the javascript libraries, if the repository is exported then these will also need to be exported in the appropriate place.

    [submodule "lib/vendor/MooEditable"]
      path = lib/vendor/MooEditable
      url = git://github.com/angelsk/mooeditable.git
    [submodule "lib/vendor/Datepicker"]
      path = lib/vendor/Datepicker
      url = git://github.com/angelsk/mootools-datepicker.git


### Publish assets

Run `./symfony mootools:publish-assets` after installation to ensure all the JavaScript and Stylesheet files are in place.

This plugin has a custom task due to symlinks in web being required from lib/vendor in the plugin, rather than just symlinking the web/ folder as normal.


Validators
----------

* Enhanced String Validator


***

### Enhanced String Validator
  
An enhanced string validator which allows HTML strings to have a max length which doesn't include the tags (especially useful if the character limit is for display purposes). 
Adds an extra dynamic option to the validator message to tell the user how many characters they have actually used, to make it easier to reduce.  Can be used in conjunction with 
other validators.


#### Changes:
  
 * Extends sfValidatorString so all options and messages for that are used
 * Adds %current_length% to the options for the validator messages
 * Strips HTML tags before calculating length, but returns the string with HTML


#### Example usage:

  Default implementation:
    
      $this->validatorSchema['text'] = new sfEnhancedValidatorString(array('max_length'=>200), 
                                                                     array('max_length'=>'Content is too long (%current_length% characters), please limit to %max_length% characters'));


Widgets
-------

* MooEditable Textarea
* Datepicker - input (with and without time)
* Datepicker - dropdown (with and without time)


***

### MooEditable Textarea

A configurable rich text editor widget, which includes (turned on by default) a clean paste from Word


#### All configuration options:

 * config:          Additional MooEditable configuration
 * width:           The width of the editable area
 * height:          The height of the editable area
 * extratoolbar:    Any additional toolbar options - include | to separate


#### Default configuration: 

 * Controls all default configuration options
 * Controls base CSS for the editable area
 * Controls whether CleanPaste included (by default)
 * Controls base toolbar options, and allows extra CSS and JS to be added if the default toolbar for all widgets is changed


#### Example usage: 

![mooeditable.png](/HollerLondon/sfMooToolsFormExtraPlugin/blob/master/docs/images/mooeditable.png?raw=true)

  Default implementation:
    
      $this->widgetSchema['text'] = new sfWidgetFormTextareaMooEditable();


***

### Datepicker - input

A date picker with the calendar control appearing when the user clicks on the input box to enter the date


#### Default configuration: 

 * Controls locale (defaults to en-GB), and location of locale files - must include all locales if local files
 * Controls the theme and location of the theme CSS files and images


#### Widget configuration options: 

 * locale:            if this is changed from the default, will require additional JS locale files
 * date_format:       The JavaScript format of the date in the input box (defaults to %Y-%m-%d - see below) - see [MooTools Date Format](http://mootools.net/docs/more/Types/Date#Date:format).
 
  If this is changed should be paired with appropriate sfValidatorDate and regex - see Example usage. 
                      
  Ensure includes time if below option is 'true'
  
 * php_date_format:   If the date_format for display is changed to a more user friendly format than %Y-%m-%d - the value needs to be converted from the database format
 
  This field should contain the corresponding PHP date_format for use with date() - see [PHP Date Format](http://uk.php.net/manual/en/function.date.php)
  
 * with_time:         defaults to 'false', include time in the date picker (date format defaults to %Y-%m-%d %H:%i instead of %Y-%m-%d)
 * year_picker:       defaults to 'true', click on the month name twice to select year - if date range restricted within one year then set to 'false'
 * min_date:          default is none, set to restrict date range (format: see above)
 * max_date:          default is none, set to restrict date range (format: see above)


#### Example usage:

![datepicker_input.png](/HollerLondon/sfMooToolsFormExtraPlugin/blob/master/docs/images/datepicker_input.png?raw=true)

  Just date:
    
      $this->widgetSchema['date'] = new sfWidgetFormInputDateTimeMooPicker();
    
  Date with min and max:
    
      $minDate = '2006-01-01';
      $maxDate = (date('Y') + 1) . '-12-31';
      $this->widgetSchema['date'] = new sfWidgetFormInputDateTimeMooPicker(array('min_date'=>$minDate, 'max_date'=>$maxDate));
      
  With time:

      $this->widgetSchema['date'] = new sfWidgetFormInputDateTimeMooPicker(array('with_time'=>'true'));

  With a different date format:
  
      $regex = '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~';
      $date_format = '%d/%m/%Y';
      $php_date_format = 'd/m/Y';
      
      $this->widgetSchema['date']     = new sfWidgetFormInputDateTimeMooPicker(array('date_format'=>$date_format, 'php_date_format'=>$php_date_format));
      $this->validatorSchema['date']  = new sfValidatorDate(array('date_format'=>$regex));


***
  
### Datepicker - dropdown

A date picker with the calendar control appearing when the user clicks on the calendar image.


#### Default configuration: 

 * Controls locale (defaults to en-GB), and location of locale files - must include all locales if moved
 * Controls the theme and location of the theme CSS files and images
 * Controls the default display date and time formats for the symfony dropdown widgets


#### Widget configuration options:

(date):

 * locale:            if this is changed from the default, will require additional JS locale files
 * year_picker:       defaults to 'true', click on the month name twice to select year - if date range restricted within one year then set to 'false'
 * min_date:          default is none, set to restrict date range (format: Y-m-d)
 * max_date:          default is none, set to restrict date range (format: Y-m-d)
 * date_widget:       The date widget to render with the calendar

(datetime):

 * locale:            if this is changed from the default, will require additional JS locale files
 * year_picker:       defaults to 'true', click on the month name twice to select year - if date range restricted within one year then set to 'false'
 * min_date:          default is none, set to restrict date range (format: Y-m-d)
 * max_date:          default is none, set to restrict date range (format: Y-m-d)
 * date_time_widget:  The datetime widget to render with the calendar
    
    
#### Example usage: 

![datepicker_date.png](/HollerLondon/sfMooToolsFormExtraPlugin/blob/master/docs/images/datepicker_date.png?raw=true)

  Just date:
  
      $this->widgetSchema['date'] = new sfWidgetFormDateMooPicker();
      
  Date with min and max and customised date widget:

      $years = range(2006, (date('Y') + 1));
      $minDate = '2006-01-01';
      $maxDate = (date('Y') + 1) . '-12-31';
      $this->widgetSchema['date'] = new sfWidgetFormDateMooPicker(array('min_date'=>$minDate, 'max_date'=>$maxDate, 
                                                                        'date_widget'=>new sfWidgetFormDate(array('years'=>array_combine($years, $years), 
                                                                                                                  'format'=>'%day% %month% %year%'))));
      
  With time:
  
      $this->widgetSchema['date'] = new sfWidgetFormDateTimeMooPicker();
    
![datepicker_datetime.png](/HollerLondon/sfMooToolsFormExtraPlugin/blob/master/docs/images/datepicker_datetime.png?raw=true)
    
  Customised datetime widget:
  
      $years = range(2006, (date('Y') + 1));
      $minDate = '2006-01-01';
      $maxDate = (date('Y') + 1) . '-12-31';
      $this->widgetSchema['date'] = new sfWidgetFormDateTimeMooPicker(array('min_date'=>$minDate, 'max_date'=>$maxDate, 
                                                                            'date_time_widget'=>new sfWidgetFormDateTime(array('date' => array('years'=>array_combine($years, $years), 
                                                                                                                                               'format'=>'%day% %month% %year%'),
                                                                                                                               'time'=> array('format'=>'%hour% %minute%')))));
