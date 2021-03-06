<?php
/** Form for assigning Treasure case actions
 *
 * An example of code:
 * 
 * <code>
 * <?php
 * $form = new TreasureActionForm();
 * ?>
 * </code> 
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @category   Pas
 * @package    Pas_Form
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @example  /app/modules/database/controllers/TreasureController.php
 * @version 1
 * @uses TreasureActionTypes
*/
class TreasureActionForm extends Pas_Form {

    /** The constructor
     * @access public
     * @param array $options
     * @return void
     */
    public function __construct(array $options = null) {
	
	$actionTypes = new TreasureActionTypes();
	$actionlist = $actionTypes->getList();

	ZendX_JQuery::enableForm($this);
	
	parent::__construct($options);

	$this->setName('actionsForTreasure');

	$actionDescription = new Pas_Form_Element_CKEditor('actionTaken');
	$actionDescription->setLabel('Action summary: ')
		->setRequired(true)
		->setAttribs(array('rows' => 10, 'cols' => 40, 'Height' => 400,
		'ToolbarSet' => 'Basic'))
		->addFilters(array('StringTrim', 'WordChars', 'BasicHtml', 'EmptyParagraph'));

	$action = new Zend_Form_Element_Select('actionID');
	$action->setLabel('Type of action taken: ')
		->setRequired(true)
		->addFilters(array('StringTrim', 'StripTags'))
		->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
		->addValidator('InArray', false, array(array_keys($actionlist)))
		->addMultiOptions($actionlist);

	$submit = new Zend_Form_Element_Submit('submit');
	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)
		->setTimeout(4800);
	
	$this->addElements(array(
            $action, $actionDescription, $submit, $hash
	));
	
	$this->addDisplayGroup(array('actionID','actionTaken',), 'details');
	$this->addDisplayGroup(array('submit'), 'buttons');
	
	parent::init();
    }
}
