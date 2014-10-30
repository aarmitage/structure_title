<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Memberlist Class
 *
 * @package     ExpressionEngine
 * @category    Plugin
 * @author      Andrew Armitage and Matt Shearing
 * @copyright   Copyright (c) 2014, Andrew Armitage
 * @link        http://example.com/memberlist/
 */

$plugin_info = array(
    'pi_name'         => 'Page Title from Structure Fieldtype',
    'pi_version'      => '1.1',
    'pi_author'       => 'Andrew Armitage and Matt Shearing',
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
        //split the uri at each segment separated by slashes
        $alluri = explode('/', $uri);
        //declare a blank starting point to rebuild the uri
        $newuri = '';
        //set a counter
        $i = 0;
        //loop through each segment that was exploded into an array
        foreach ($alluri as $value) {
	        //wait for the domain to be looped through
	        if ($i > 2) {
		        //add each segment to the new uri excluding the domain
		        $newuri = $newuri . '/' . $value;
	        }
	        //increase the counter
	        $i++;
        }
        
        //need to query the sites table
        $site_data = ee()->db->select()
					->from('sites')
					->get();
		
		//we only need to continue if we've got a result from the DB (which we should have, but just to be sure)
		if ($site_data->num_rows() > 0) {
			//we want to loop through the query on a per row basis to return all the values in the array
			foreach ($site_data->result_array() as $row)
			//site_pages field is serialised and base64 encoded
			$url_strings[] = array('url' => unserialize(base64_decode($row['site_pages'])));
			{
				$results[] = array($url_strings);
			}
		}
		//if the uri is blank then no {site} variable has been passed to the plugin
		if ($uri == '') {
	        $this->return_data = 'Missing Structure URI!';
        } else {
	        //if there is a URI then do a for loop counting through each line of the array
			for ($j = 0; $j < $site_data->num_rows(); $j++){
				//go 6 levels down the multi-dimensional array structure to get the data we need
				$options[$j] = ($results[0][0][0]['url'][1]['uris']);
				//run a foreach loops on this array to save our variables for usage
				foreach ($options[$j] as $key => $value) {
					//only use the values relevant to our uri we passed earlier
					if ($newuri == $value) {
						//explode the uri to get the last segment value
						$all = explode('/', $value);
						$last = end(explode('/', $value));
						$count = count($all);
						//remove any trailing slashes
						if ($last == '') {
							$last = $count - 2;
							$url_title = $all[$last];
							//use last segment in $url_title for a query to find the full page title
							$get_title = ee()->db->select('title')->from('channel_titles')->where('url_title', $url_title)->get();
						} else {
							//use last segment in $url_title for a query to find the full page title
							$get_title = ee()->db->select('title')->from('channel_titles')->where('url_title', $last)->get();
						}
						//put the template_id, full url_path and page_title into the vars array
						$vars = array('template_id' => $key, 'url_path' => $value, 'page_title' => $get_title->row('title'));
						//return only the page title, change this to return vars if you wish to use it as a tag pair and return data such as template_id
						$this->return_data = $get_title->row('title');
					}
				}
			}
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