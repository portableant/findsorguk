<?php
/** Controller for displaying information about tags used and generated by opencalais
 * 
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @copyright (c) 2014 Daniel Pett
 * @category   Pas
 * @package    Controller_Action
 * @subpackage Admin
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1
 * @uses OpenCalaisModel
 * @uses Pas_Exception_Param
 * 
*/
class Database_TagsController extends Pas_Controller_Action_Admin {
	
    /** The opencalais model
     * @access protected
     * @var \OpenCalaisModel
     */
    protected $_opencalais;
    
    /** Setup the contexts by action and the ACL.
     * @access public
     * @return void
    */	
    public function init() {
        $this->_helper->_acl->allow(null);
        
        $this->_opencalais = new OpenCalaisModel();
    }
    /** Display index page
     * @access public
     * @return void
     */	
    public function indexAction() {
        //Magic in view
    }
    /** Tags created by opencalais
     * @access public
     * @return void
    */	
    public function opencalaisAction() {
        if($this->_getParam('tag',false)) {

        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }
    /** Tags created by yahoo
     * @access public
     * @return void
     */	
    public function geotagAction() {
        if($this->_getParam('tag',false)) {
            
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }
}