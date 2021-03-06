<?php

/**
 * Metadata generator for creating LIDO xml.
 *
 * Class implementing metadata output for the required LIDO metadata format.
 * Also uses grid reference stripping and redisplay tools. This builds on the
 * OAI classes generated for the Omeka system. This was created in August 2014
 * at the request of the Collections Trust *AS A FAVOUR*.
 *
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @copyright (c) 2014, Daniel Pett
 * @category Pas
 * @package  Pas_OaiPmhRepository
 * @subpackage Metadata
 * @version  1
 * @since    August 2014
 * @see http://data.iwm.org.uk/api/oai?Verb=ListRecords&metadataPrefix=lido_eck Example output
 * @see http://www.lido-schema.org/schema/v1.0/lido-v1.0-schema-listing.html for specification
 * @see http://www.lido-schema.org/schema/v1.0/lido-v1.0-specification.pdf for pdf of details
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @example
 */
class Pas_OaiPmhRepository_Metadata_Lido extends Pas_OaiPmhRepository_Metadata_Abstract
{

    /** OAI-PMH metadata prefix */
    const METADATA_PREFIX = 'lido';

    /** XML namespace for output format */
    const METADATA_NAMESPACE = 'http://www.lido-schema.org';

    /** XML schema for output format */
    const METADATA_SCHEMA = 'http://www.lido-schema.org/schema/v1.0/lido-v1.0.xsd';

    /** The crm concept   */
    const CIDOC_CRM_CONCEPT = 'http://www.cidoc-crm.org/crm-concepts/#E22';

    const GML_XSD = 'http://schemas.opengis.net/gml/3.1.1/base/feature.xsd';

    const GML = 'http://www.opengis.net/gml';

    /** CRM term for type **/
    const CIDOC_TERM = 'Man-Made Object';

    /** The language to serve */
    const LANG = 'en';

    /** Protected view object
     * @access protected
     * @var \Zend_View
     */
    protected $_view;

    /** Init the view
     * @access public
     * @return void
     */
    public function init()
    {
        $this->_view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;
    }

    /** Add meta data to the xml response as LIDO XML
     * @access public
     * @return void
     */
    public function appendMetadata()
    {
        // Create the base element
        $metadataElement = $this->document->createElement('metadata');
        $this->parentElement->appendChild($metadataElement);

        //Create the lido element
        $lidoElement = $this->document->createElementNS(self::METADATA_NAMESPACE, 'lido:lido');

        //Append to the base element
        $metadataElement->appendChild($lidoElement);

        //Set the base attributes
        $lidoElement->setAttribute('xmlns:xsi', parent::XML_SCHEMA_NAMESPACE_URI);
        $lidoElement->setAttribute('xsi:schemaLocation', self::METADATA_NAMESPACE . ' ' . self::METADATA_SCHEMA);
        //Create the dublin core metadata from an array of objects
        if (!array_key_exists('0', $this->item)) {

            if (array_key_exists('objecttype', $this->item) && $this->item['objecttype'] === 'HOARD') {
                $uri = self::HOARD_URI;
            } else {
                $uri = self::RECORD_URI;
            }
            //The base LIDO array
            $lido = array();
            if (array_key_exists('broadperiod', $this->item)) {
                $lido['title'] = $this->item['broadperiod'] . ' ' . $this->item['objecttype'];
            } else {
                $lido['title'] = $this->item['objecttype'];
                $this->item['broadperiod'] = 'UNKNOWN';
            }
            if (array_key_exists('creator', $this->item)) {
                $lido['creator'] = $this->item['creator'];
            }
            $lido['subject'] = self::SUBJECT . ' - ' . $this->item['broadperiod'];
            if (array_key_exists('description', $this->item)) {
                $description = strip_tags(str_replace(array("\n", "\r", '    '),
                    array('', '', ' '), $this->item['description']));
            } else {
                $description = 'No description available';
            }
            $lido['old_findID'] = $this->item['old_findID'];
            $lido['description'] = strtr($description, array('\x0B' => '&#x0B;'));
            $lido['publisher'] = self::RIGHTS_HOLDER;
            $lido['contributor'] = $this->institution($this->item['institution']);
            $lido['date'] = $this->item['created'];
            $lido['type'] = $this->item['objecttype'];
            $lido['format'] = self::FORMAT;
            $lido['source'] = self::SOURCE;
            $lido['language'] = self::LANGUAGE;
            $lido['identifier'] = $this->item['old_findID'];
            $lido['coverage'] = $this->item['broadperiod'];
            $lido['rights'] = self::LICENSE;
            $lido['uri'] = $this->_serverUrl . $uri . $this->item['id'];
            //The array of dates
            $dates = array();
            if (array_key_exists('fromdate', $this->item)) {
                $dates['fromdate'] = $this->item['fromdate'];
            }
            if (array_key_exists('todate', $this->item)) {
                $dates['todate'] = $this->item['todate'];
            }
            $discovery = array();
            if (array_key_exists('datefound1', $this->item)) {
                $discovery['datefound1'] = $this->item['datefound1'];
            }
            if (array_key_exists('datefound2', $this->item)) {
                $discovery['datefound2'] = $this->item['datefound2'];
            }
            $measurements = array();
            if (array_key_exists('width', $this->item)) {
                $measurements['width'] = $this->item['width'];
            }
            if (array_key_exists('height', $this->item)) {
                $measurements['height'] = $this->item['height'];
            }
            if (array_key_exists('diameter', $this->item)) {
                $measurements['diameter'] = $this->item['diameter'];
            }
            if (array_key_exists('thickness', $this->item)) {
                $measurements['thickness'] = $this->item['thickness'];
            }
            //Create the spatial array
            $spatial = array();
            if (array_key_exists('county', $this->item)) {
                $spatial['county'] = $this->item['county'];
            }
            if (array_key_exists('district', $this->item)) {
                $spatial['district'] = $this->item['district'];
            }

            //Check for availability of NGR and therefore latlon conversions
            if (array_key_exists('knownas', $this->item)
                && array_key_exists('fourFigure', $this->item)
            ) {
                $lat = $this->item['fourFigureLat'];
                $lon = $this->item['fourFigureLon'];
                $spatial['coords'] = $lat . ',' . $lon;
            }

            $people = array();
            if (array_key_exists('identifer', $this->item)) {
                $people['identifer'] = $this->item['identifer'];
            }
            if (array_key_exists('recorder', $this->item)) {
                $people['recorder'] = $this->item['recorder'];
            }
            if (array_key_exists('secondaryIdentifer', $this->item)) {
                $people['secondary identifer'] = $this->item['secondaryIdentifer'];
            }
            if(array_key_exists('materialTerm', $this->item)){
                $material = $this->item['materialTerm'];
            } else {
                $material = 'Unknown';
            }
            //Basic terms for DC
            $dcterms = array(
                'created' => date('Y-m-d', strtotime($this->item['created'])),
                'medium' => $material,
                'isPartOf' => self::SOURCE,
                'provenance' => self::PROVENANCE
            );
            //ESE terms
            $ese = array();
            $ese['provider'] = self::RIGHTS_HOLDER;
            $ese['type'] = 'TEXT';

            //The temporal array
            $temporal = array();
            if (array_key_exists('fromdate', $this->item)) {
                $temporal['year1'] = $this->item['fromdate'];
            }
            if (array_key_exists('todate', $this->item)) {
                $temporal['year2'] = $this->item['todate'];
            }

            //The formats for the images
            $formats = array();

            if (array_key_exists('thumbnail', $this->item) && array_key_exists('imagedir', $this->item)) {
                $formats['image_thumb'] = 'http://finds.org.uk' . '/' . $this->item['imagedir'] . $this->item['filename'];
                $formats['image_master'] = 'http://finds.org.uk' . '/' . $this->item['imagedir'] . $this->item['filename'];
            }

            //Create the record ID element
            $recID = $this->document->createElement('lido:lidoRecID', $lido['uri']);
            //Append the element to the LIDO element
            $lidoElement->appendChild($recID);
            $recID->setAttribute('lido:source', 'The Portable Antiquities Scheme');
            $recID->setAttribute('lido:type', 'URI');

            //Published at
            $published = $this->document->createElement('lido:objectPublishedID', $lido['uri']);
            //Append the published element to the element
            $lidoElement->appendChild($published);
            $published->setAttribute('lido:type', 'URI');

            // The category wrapper
            $categoryElement = $this->document->createElement('lido:category');
            //Append the category element to the LIDO element
            $lidoElement->appendChild($categoryElement);
            $this->appendNewElement($categoryElement, 'lido:conceptID', self::CIDOC_CRM_CONCEPT)
                ->setAttribute('lido:type', 'URI');
            $this->appendNewElement($categoryElement, 'lido:term', self::CIDOC_TERM)
                ->setAttribute('xml:lang', self::LANG);

            //Descriptive element
            $descriptiveElement = $this->document->createElement('lido:descriptiveMetadata');
            //Append the descriptive element to the LIDO element
            $lidoElement->appendChild($descriptiveElement);
            $descriptiveElement->setAttribute('xml:lang', self::LANG);

            //Create the class wrapper
            $classWrap = $this->document->createElement('lido:objectClassificationWrap');
            //Append the class wrapper to the descriptive element
            $descriptiveElement->appendChild($classWrap);

            //Create the work type wrapper
            $workTypeWrap = $this->document->createElement('lido:objectWorkTypeWrap');
            //Append the worktype to the class wrapper
            $classWrap->appendChild($workTypeWrap);

            $typeWrap = $this->document->createElement('lido:objectWorkType');
            $workTypeWrap->appendChild($typeWrap);

            $searchTerm = $this->document->createElement('lido:term', $this->item['objecttype']);

            $typeWrap->appendChild($searchTerm);
            $searchTerm->setAttribute('lido:addedSearchTerm', 'no');
            if (array_key_exists('thumbnail', $this->item)) {
                $classification = $this->document->createElement('lido:classificationWrap');
                $classWrap->appendChild($classification);
                $class = $this->document->createElement('lido:classification');
                $classification->appendChild($class);
                $class->setAttribute('lido:type', 'europeana:type');
                $this->appendNewElement($class, 'lido:term', 'IMAGE');
            }
            // The identifcation wrapper
            $identificationElement = $this->document->createElement('lido:objectIdentificationWrap');
            $descriptiveElement->appendChild($identificationElement);

            //The title wrap
            $titleWrap = $this->document->createElement('lido:titleWrap');
            $identificationElement->appendChild($titleWrap);
            $titleset = $this->document->createElement('lido:titleSet');
            $titleWrap->appendChild($titleset);
            $this->appendNewElement($titleset, 'lido:appellationValue', $lido['title']);

            //The repository wrapper
            $repoWrap = $this->document->createElement('lido:repositoryWrap');
            $identificationElement->appendChild($repoWrap);
            $repoSet = $this->document->createElement('lido:repositorySet');

            $repoWrap->appendChild($repoSet);
            $repoName = $this->document->createElement('lido:repositoryName');
            $repoSet->appendChild($repoName);
            $legalRepoName = $this->document->createElement('lido:legalBodyName');
            $repoName->appendChild($legalRepoName);
            $this->appendNewElement($legalRepoName, 'lido:appellationValue', 'The British Museum and the Portable Antiquities Scheme');

            //Object description wrap
            $objDescWrap = $this->document->createElement('lido:objectDescriptionWrap');
            $identificationElement->appendChild($objDescWrap);
            $objDescSet = $this->document->createElement('lido:objectDescriptionSet');
            $objDescWrap->appendChild($objDescSet);
            $this->appendNewElement($objDescSet, 'lido:descriptiveNoteValue', $lido['description']);
            if (!empty($measurements)) {
                $objMeasureWrap = $this->document->createElement('lido:objectMeasurementsWrap');
                $identificationElement->appendChild($objMeasureWrap);
                $objMeasureSet = $this->document->createElement('lido:objectMeasurementsSet');
                $objMeasureWrap->appendChild($objMeasureSet);
                $objMeas = $this->document->createElement('lido:objectMeasurements');
                $objMeasureSet->appendChild($objMeas);
                foreach ($measurements as $k => $v) {
                    $set = $this->document->createElement('lido:measurementsSet');
                    $objMeas->appendChild($set);
                    $this->appendNewElement($set, 'lido:measurementType', $k);
                    $this->appendNewElement($set, 'lido:measurementUnit', 'mm');
                    $this->appendNewElement($set, 'lido:measurementValue', $v);
                }
                if (array_key_exists('weight', $this->item)) {
                    $set = $this->document->createElement('lido:measurementsSet');
                    $objMeas->appendChild($set);
                    $this->appendNewElement($set, 'lido:measurementType', 'weight');
                    $this->appendNewElement($set, 'lido:measurementUnit', 'g');
                    $this->appendNewElement($set, 'lido:measurementValue', $this->item['weight']);
                }
            }
            //The event wrapper
            $eventElement = $this->document->createElement('lido:eventWrap');
            $descriptiveElement->appendChild($eventElement);
            $eventSet = $this->document->createElement('lido:eventSet');
            $eventElement->appendChild($eventSet);

            //The creator of the record
            $eventDating = $this->document->createElement('lido:event');
            $eventSet->appendChild($eventDating);
            $eventTypeCreation = $this->document->createElement('lido:eventType');
            $this->appendNewElement($eventTypeCreation, 'lido:term', 'Production');
            $eventDating->appendChild($eventTypeCreation);

            $eventDate = $this->document->createElement('lido:eventDate');
            $eventDating->appendChild($eventDate);
            $date = $this->document->createElement('lido:date');
            $eventDate->appendChild($date);
            if (array_key_exists('fromdate', $this->item)) {
                $eventDateTime = $this->document->createElement('lido:earliestDate', $this->item['fromdate']);
                $date->appendChild($eventDateTime);
            }
            if (array_key_exists('todate', $this->item)) {
                $eventDateTime = $this->document->createElement('lido:latestDate', $this->item['todate']);
                $date->appendChild($eventDateTime);
            }

            if (!empty($discovery)) {
                $eventSetFinding = $this->document->createElement('lido:eventSet');
                $eventElement->appendChild($eventSetFinding);
                $eventDating = $this->document->createElement('lido:event');
                $eventSetFinding->appendChild($eventDating);
                $eventTypeCreation = $this->document->createElement('lido:eventType');
                $this->appendNewElement($eventTypeCreation, 'lido:term', 'Finding');
                $eventDating->appendChild($eventTypeCreation);
                if (array_key_exists('county', $this->item)) {
                    $placeType = $this->document->createElement('lido:eventPlace');
                    $eventDating->appendChild($placeType);
                    $place = array();
                    $place[] = 'United Kingdom';
                    $place[] = $this->item['county'];
                    if (array_key_exists('district', $this->item)) {
                        $place[] = $this->item['district'];
                    }
                    if (array_key_exists('parish', $this->item)) {
                        if (!array_key_exists('knownas', $this->item)) {
                            $place[] = $this->item['parish'];
                        }
                    }
                    $placeDisplay = $this->document->createElement('lido:displayPlace', implode($place, ', '));
                    $placeType->appendChild($placeDisplay);
                }

            }

            //The admin wrapper
            $adminElement = $this->document->createElement('lido:administrativeMetadata');

            $lidoElement->appendChild($adminElement);
            $adminElement->setAttribute('xml:lang', self::LANG);
            $rightsWrapElement = $this->document->createElement('lido:rightsWorkWrap');
            $adminElement->appendChild($rightsWrapElement);
            $rightsWorkSet = $this->document->createElement('lido:rightsWorkSet');
            $rightsWrapElement->appendChild($rightsWorkSet);
            $this->appendNewElement($rightsWorkSet, 'lido:creditLine', self::RIGHTS_HOLDER);

            $recordWrap = $this->document->createElement('lido:recordWrap');
            $adminElement->appendChild($recordWrap);
            $recordID = $this->document->createElement('lido:recordID', $this->item['old_findID']);
            $recordID->setAttribute('lido:source', 'PAS');
            $recordID->setAttribute('lido:type', 'local');
            $recordWrap->appendChild($recordID);


            $recordType = $this->document->createElement('lido:recordType');
            $recordWrap->appendChild($recordType);
            $this->appendNewElement($recordType, 'lido:term', 'item');

            $recordSource = $this->document->createElement('lido:recordSource');

            $recordWrap->appendChild($recordSource);
            $legalBody = $this->document->createElement('lido:legalBodyName');
            $recordSource->appendChild($legalBody);
            $this->appendNewElement($legalBody, 'lido:appellationValue', 'The British Museum and The Portable Antiquities Scheme');

            $this->appendNewElement($recordSource, 'lido:legalBodyWeblink', 'http://finds.org.uk');

            $recordInfoSet = $this->document->createElement('lido:recordInfoSet');
            $recordWrap->appendChild($recordInfoSet);

            $url = new Pas_OaiPmhRepository_ServerUrl();

            $types = array('html');
            foreach ($types as $type) {
                $link = $url->get() . $uri . $this->item['id'];
                $this->appendNewElement($recordInfoSet, 'lido:recordInfoLink', $link)
                    ->setAttribute('lido:formatResource', $type);
            }

            if (array_key_exists('thumbnail', $this->item)) {
                //Create the resource wrap set and append to admin element
                $resourceWrapSet = $this->document->createElement('lido:resourceWrap');
                $adminElement->appendChild($resourceWrapSet);
                //Create resource set and append to resource wrap set
                $resourceSet = $this->document->createElement('lido:resourceSet');
                $resourceWrapSet->appendChild($resourceSet);

                foreach ($formats as $k => $v) {
                    if(file_exists('./' . $this->item['imagedir'] . $this->item['filename'])) {
                        list($width, $height) = getimagesize('./' . $this->item['imagedir'] . $this->item['filename']);
                        $representation = $this->document->createElement('lido:resourceRepresentation');
                        $resourceSet->appendChild($representation);
                        $representation->setAttribute('lido:type', $k);

                        $this->appendNewElement($representation, 'lido:linkResource', $v)
                            ->setAttribute('lido:formatResource', 'jpg');

                        if (file_exists('./' . $this->item['imagedir'] . $this->item['filename'])) {
                            list($width, $height) = getimagesize('./' . $this->item['imagedir'] . $this->item['filename']);
                            //Add height measurement set
                            $measurementSetHeight = $this->document->createElement('lido:resourceMeasurementsSet');
                            $representation->appendChild($measurementSetHeight);
                            //Add image height
                            $this->appendNewElement($measurementSetHeight, 'lido:measurementType', 'height');
                            $this->appendNewElement($measurementSetHeight, 'lido:measurementUnit', 'pixel');
                            $this->appendNewElement($measurementSetHeight, 'lido:measurementValue', $height);
                            //Add Width Measurement Set
                            $measurementSetWidth = $this->document->createElement('lido:resourceMeasurementsSet');
                            $representation->appendChild($measurementSetWidth);
                            //Add image width
                            $this->appendNewElement($measurementSetWidth, 'lido:measurementType', 'width');
                            $this->appendNewElement($measurementSetWidth, 'lido:measurementUnit', 'pixel');
                            $this->appendNewElement($measurementSetWidth, 'lido:measurementValue', $width);
                        }
                    }

                }
                $this->appendNewElement($resourceSet, 'lido:resourceDescription', 'Original Image');
                $resourceSource = $this->document->createElement('lido:resourceSource');
                $resourceSet->appendChild($resourceSource);
                $resourceSource->setAttribute('lido:type', 'Holder of image');
                $this->appendNewElement($resourceSource, 'lido:legalBodyID', 'http://britishmuseum.org')
                    ->setAttribute('lido:type', 'URI');
                $legalBodyName = $this->document->createElement('lido:legalBodyName');
                $resourceSource->appendChild($legalBodyName);
                $this->appendNewElement($legalBodyName, 'lido:appellationValue', 'The British Museum: Department of Britain, Europe and Prehistory');

                $rightsResource = $this->document->createElement('lido:rightsResource');
                $resourceSet->appendChild($rightsResource);

                $resourceRightsType = $this->document->createElement('lido:rightsType');
                $rightsResource->appendChild($resourceRightsType);
                $this->appendNewElement($resourceRightsType, 'lido:conceptID', self::LICENSE_URI)
                    ->setAttribute('lido:type', 'URI');


            }
        }
    }

    /** Returns the OAI-PMH metadata prefix for the output format.
     * @access public
     * @return string Metadata prefix
     */
    public function getMetadataPrefix()
    {
        return self::METADATA_PREFIX;
    }

    /** Returns the XML schema for the output format.
     * @access public
     * @return string XML schema URI
     */
    public function getMetadataSchema()
    {
        return self::METADATA_SCHEMA;
    }

    /** Returns the XML namespace for the output format.
     * @access public
     * @return string XML namespace URI
     */
    public function getMetadataNamespace()
    {
        return self::METADATA_NAMESPACE;
    }

    /** Get the full name for an institution
     * @access public
     * @param string $inst
     * @return string
     */
    public function institution($inst)
    {
        if (!is_null($inst)) {
            $institutions = new Institutions();
            $where = array();
            $where[] = $institutions->getAdapter()->quoteInto('institution = ?', $inst);
            $institution = $institutions->fetchRow($where);
            if (!is_null($institution)) {
                return $institution->description;
            }
        } else {
            return 'The Portable Antiquities Scheme';
        }
    }
}
