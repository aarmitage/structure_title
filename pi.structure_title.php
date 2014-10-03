<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Memberlist Class
 *
 * @package     ExpressionEngine
 * @category    Plugin
 * @author      Andrew Armitage
 * @copyright   Copyright (c) 2014, Andrew Armitage
 * @link        http://example.com/memberlist/
 */

$plugin_info = array(
    'pi_name'         => 'Page Title from Structure Fieldtype',
    'pi_version'      => '1.0',
    'pi_author'       => 'Andrew Armitage',
    'pi_author_url'   => 'http://www.armitageonline.co.uk',
    'pi_description'  => 'Uses structure page URI to extract last segment to obtain entry title',
    'pi_usage'        =>  Structure_title::usage()
);

class Structure_title
{

    public $return_data = "";

    // --------------------------------------------------------------------

    /**
     * Structure Title
     *
     * This function returns the page title from a structure fieldtype (Structure page URI)
     *
     * @access  public
     * @return  string
     */
    public function __construct()
    {
        //get the structure URI from the template
        $uri = ee()->TMPL->fetch_param('structure_uri');
				
				//check we've got a structure URI in the plugin tag
        if($uri == '')
        {
	        $this->return_data = 'Missing Structure URI!';
        }
        else
        {
        	//break up the URI at '/'
					$all = explode('/', $uri);
					//get the last segment
					$last = end(explode('/', $uri));
					//count the values in the array from the full URI
					$count = count($all);
					
					//if there's a trailing slash then $last will be empty
					if($last == '') {
						//array is 0 indexed, so remove last 2 elements to get the last segment
						$last = $count - 2;
						//set the url title from the original structure URI
						$url_title = $all[$last];
						//query the DB with URL Title to get the title
						$get_title = ee()->db->select('title')->from('channel_titles')->where('url_title', $url_title)->get();
						
					}
					else {
						//there is no trailing slash, so we can use the value of $last in the query as this is our url title
						$get_title = ee()->db->select('title')->from('channel_titles')->where('url_title', $last)->get();
					}
					//return query result to template
					$this->return_data = $get_title->row('title');
				}

    }

    // --------------------------------------------------------------------

    /**
     * Usage
     *
     * This function describes how the plugin is used.
     *
     * @access  public
     * @return  string
     */
    public static function usage()
    {
        ob_start();  ?>

Looks up the page title from a Structure URI when the structure fieldtype is used in a channel field. The structure_uri parameter is required.

{exp:structure_title structure_uri="{page}"}


    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
    // END
}
/* End of file pi.structure_title.php */
/* Location: ./system/expressionengine/third_party/structure_title/pi.structure_title.php */