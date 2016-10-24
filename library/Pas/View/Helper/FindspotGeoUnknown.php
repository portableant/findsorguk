<?php
/** A view helper for displaying findspots where grid reference is unknown.
 *
 * @category Pas
 * @package View
 * @subpackage Helper * @author Daniel Pett <dpett@britishmuseum.org>
 * @copyright (c) 2014, Daniel Pett
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1
 * @uses Zend_View_Helper_Abstract
 * @uses Pas_View_Helper_YahooGeoAdjacent
 */

class Pas_View_Helper_FindspotGeoUnknown extends Zend_View_Helper_Abstract
{

    /** The auth object
     * @access public
     * @var object $_auth
    */
    protected $_auth = NULL;

    /** The auth object
     * @access protected
     * *@var object $_cache
    */
    protected $_cache = NULL;

    /** The config object
    * @var object $_config
    */
    protected $_config = NULL;

    /** The appid object
    * @var object $_appid
    */
    protected $_appid = NULL;

    /** Get the auth object
     * @access public
     * @return \Zend_Auth
     */
    public function getAuth() {
        $this->_auth = Zend_Registry::get('auth');
        return $this->_auth;
    }

    /** Set the app id
     * @access public
     * @param type $appid
     * @return \Pas_View_Helper_FindspotGeoUnknown
     */
    public function setAppid($appid) {
        $this->_appid = $appid;
        return $this;
    }

    /** Get the cache
     * @access public
     * @return \Zend_Cache
     */
    public function getCache() {
        $this->_cache = Zend_Registry::get('cache');
        return $this->_cache;
    }

    /** Get the config
     * @access public
     * @return \Zend_Config
     */
    public function getConfig() {
        $this->_config = Zend_Registry::get('config');
        return $this->_config;
    }

    /** Create the findspot with no known geo
     * @access public
     * @param $string
     */
    public function FindspotGeoUnknown($string) {
        return $this->buildHtml(null);
    }

    /** Function for determining whether elevation is -ve or +ve or =
     * @param  int $elevation
     * @return string $string
     */
    public function metres($elevation)  {
        switch ($elevation) {
            case ($elevation == 0):
                $string = 'sea level.';
                break;
            case ($elevation > 0):
                $string = $elevation . ' metres above sea level.';
                break;
            case ($elevation < 0):
                $string = $elevation . ' metres below sea level.';
                break;
        }
        return $string;
    }

    /** Build the HTML for rendering
     * @param  array  $data
     * @return string $html
     */
    public function buildHtml($data) {
        $html = '';
        $html .= '<h4 class="lead">Data from Yahoo! GeoPlanet</h4>';
	if($data) {
	        $html .= '<p>The spatially enriched data provided here was sourced from the excellent Places/Placemaker service';
	        $html .= 'from Yahoo\'s geo team. <strong>It is an autogenerated findspot and therefore should not be used for GIS';
        	$html .= ' studies.</strong><br />';
	        $html .= $this->view->gridref($data['latitude'],$data['longitude']) . '<br />';
	        $html .= 'Latitude: ' . $data['latitude'] . '<br />';
	        $html .= 'Longitide: ' . $data['longitude'] . '<br />';
	        $html .= 'Settlement type: ' . $data['placeTypeName'] . '<br/>';
	        $html .= 'WOEID: ' . $data['woeid'] . '<br/>';
	        if (array_key_exists('postal',$data)) {
	            $html .= 'Postcode: ' . $data['postal'] . '<br/>';
	        }
	        $html .= 'Country: ' . $data['admin1'] . '<br/>';
	        $html .= 'Astergdem generated elevation: ' . $this->metres($data['elevation']);
	        $html .= '</p>';
	        $html .= $this->view->YahooGeoAdjacent($data['woeid']);
	}
	else {
            $html .= '<p>No data returned</p>';
        }

        return $html;
    }
}
