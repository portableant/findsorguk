<?php
/** An interface to the Edina ClosestMatchSearch api call
 * 
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @copyright (c) 2014 Daniel Pett
 * @category Pas
 * @package Pas_Geo
 * @subpackage Edina
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @since 3/2/12
 * @version 1
 * @uses Pas_Geo_Edina_Exception
 * @see http://unlock.edina.ac.uk/places/queries/
 *
 * Usage:
 *
 * $edina = new Pas_Geo_Edina_ClosestMatchSearch();
 * $edina->setName('Astrope');
 * $edina->setFormat('json'); - you can use georss, kml, xml, jaon
 * $edina->get();
 * You can also get the place name queried back from:
 * $edina->getName();
 * If you want to get the url of the api call
 * $edina->getUrl();
 *
 * Then process the object returned as you want (up to you!)
 */
class Pas_Geo_Edina_ClosestMatchSearch extends Pas_Geo_Edina{

    /** The method to call
     *
     */
    const METHOD = 'closestMatchSearch?';

    /** The name that will be queried
     * @access protected
     * @var string
     */
    protected $_name;

    /** Set the name to query
     * @access protected
     * @param string $name
     * @return string
     * @throws Pas_Geo_Edina_Exception
     */
    public function setName($name){
        if(!is_string($name)){
            throw new Pas_Geo_Edina_Exception('The name must be a string');
        } else {
            return $this->_name = $name;
        }
    }

    /** Get the single name called via api
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /** Query the api via parent
     * @access public
     * @return \Zend_Http_Response
     */
    public function get() {
        $params = array(
            'name' => $this->_name
        );
        return parent::get(self::METHOD, $params);
    }
}
