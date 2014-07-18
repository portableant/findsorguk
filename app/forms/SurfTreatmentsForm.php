<?php
/** Form for setting up and editing types of surface treatments
 * 
 * An example of code:
 * 
 * <code>
 * <?php
 * $form = new SurfTreatmentsForm();
 * ?>
 * </code>
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @version 1
 * @category   Pas
 * @package    Pas_Form
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @example/app/modules/admin/controllers/TerminologyController.php
*/
class SurfTreatmentsForm extends Pas_Form { 

    /** The constructor
     * @access public
     * @param array $options
     * @return void
     */
    public function __construct(array $options) {

	parent::__construct($options);

	$this->setName('surfmethods');

	$term = new Zend_Form_Element_Text('term');
	$term->setLabel('Decoration style term: ')
		->setRequired(true)
		->addFilters(array('StripTags', 'StringTrim'))
		->addValidator('Alpha', true, array('allowWhiteSpace' => true))
		->addErrorMessage('Please enter a valid title for this surface treatment');

	$termdesc = new Pas_Form_Element_CKEditor('termdesc');
	$termdesc->setLabel('Description of decoration style: ')
		->setRequired(true)
		->setAttribs(array('rows' => 10, 'cols' => 80))
		->addFilter(array('BasicHtml', 'EmptyParagraph', 'StringTrim', 'WordChars'))
		->addErrorMessage('You must enter a description for this surface treatment');

	$valid = new Zend_Form_Element_Checkbox('valid');
	$valid->setLabel('Termis currently in use: ')
		->setRequired(true)
		->addFilters(array('StripTags', 'StringTrim'))
		->addErrorMessage('You must set a status for this treatment term');

	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)->setTimeout(4800);

	$submit = new Zend_Form_Element_Submit('submit');
	$this->addElements(array($term, $termdesc, $valid, $submit, $hash));

	$this->addDisplayGroup(array('term','termdesc','valid'), 'details');

	$this->details->setLegend('Surface treatment details: ');
	$this->addDisplayGroup(array('submit'), 'buttons');
	parent::init();
    }
}