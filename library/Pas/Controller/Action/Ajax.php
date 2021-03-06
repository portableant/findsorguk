<?php
/** 
 * This controller action is primarily used for the creation of ajax responses
 *
 * This class needs total refactoring. Written in 2009 and not very good.
 * 
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @copyright (c) 2014 Daniel Pett
 * @category Pas
 * @package Controller
 * @subpackage Action
 * @version 2
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @since September 2009
 * @todo Refactor completely - is this actually needed?
 */
class Pas_Controller_Action_Ajax extends Zend_Controller_Action {

    /** The error message for a missing parameter
     * @access protected
     * @var string
     */
    protected $_missingParameter = 'The url is missing a parameter.
        Please check your entry point.';

    /** The error message for when nothing has been found from a db call via parameter
     * @access protected
     * @var string
     */
    protected $_nothingFound = 'We cannot find anything with that parameter.
        Please check your entry url carefully.';

    /** Form error message - is this used?
     * @access protected
     * @var string
     */
    protected $_formErrors = 'Your form submission has some errors.
        Please check and resubmit.';

    /** Message when no changes made
     * @access protected
     * @var string
     */
    protected $_noChange = 'No changes have been implemented';

    /** The authority object.
     * @access protected
     * @var object $_auth
     */
    protected $_auth;

    /** Get auth object
     * @access public
     */
    public function init(){
        $this->_auth = Zend_Auth::getInstance();
    }

    /** Get a user's institution
     * @access public
     * @return string
     */
    public function getInstitution()
    {
        return $this->_helper->identity->getPerson()->institution;
    }


    /** Get a user's identity
     * @access public
     * @return integer
     */
    public function getIdentityForForms()
    {
        return $this->_helper->identity->getIdentityForForms();
    }

    /** Get a user's role
     * @access public
     * @return string
     */
    public function getRole()
    {
        $person = $this->_helper->identity->getPerson();
        if (!$person) {
            $role = 'public';
        } else {
            $role = $person->role;
        }
        return $role;
    }

    /** Get a user's username
     * @access public
     * @return string
     */
    public function getUsername() {
        return $this->_helper->identity->getPerson()->username;
    }
    
    /** Get a time for updating form
     * @access public
     * @return string
     */
    public function getTimeForForms() {
        return Zend_Date::now()->toString('yyyy-MM-dd HH:mm');
    }
    
     /** Curl function to retrieve data from url
      * @access public
      * @param string $url
      * @return \Zend_Http_Response
      */
    public function get( $url ){
        $config = array(
            'adapter'   => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(
                CURLOPT_POST =>  true,
                CURLOPT_USERAGENT =>  $_SERVER["HTTP_USER_AGENT"],
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
            ),
	);
        $client = new Zend_Http_Client($url, $config);
	return $client->request();
    }
}